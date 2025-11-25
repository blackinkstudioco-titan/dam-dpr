<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\User;
use App\Models\Event;
use App\Models\ArtikelPublish;
use App\Exports\ArtikelReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportArtikelController extends Controller
{
    /**
     * Display the report page
     */
public function index(Request $request)
{
    $reportType = $request->get('report_type', 'upload');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    $perPage = $request->get('per_page', 25);
    $rubrik = $request->get('rubrik');
    $status = $request->get('status');

    $userId = $request->get('user_id');

    // Query builder - berbeda berdasarkan report type
    if ($reportType === 'upload') {
        // Report Upload: gunakan tabel artikel (draft) + eager load creator
        $query = Artikel::with(['creator','event']);
        if($userId){
            $query->where('add_by', $userId);
        }
        if ($startDate) {
            $query->whereDate('add_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('add_date', '<=', $endDate);
        }
        $query->orderBy('add_date', 'desc');
    } else {
        // Report Edit: gunakan tabel artikel_publish + eager load editor
        $query = ArtikelPublish::with(['editor', 'creator','event']);
        if($userId){
            $query->where('edit_by', $userId);
        }
        if ($startDate) {
            $query->whereDate('edit_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('edit_date', '<=', $endDate);
        }
        $query->whereNotNull('edit_date')
              ->orderBy('edit_date', 'desc');
    }

    // Filter rubrik
    if ($rubrik) {
        $query->where('rubrik', $rubrik);
    }

    // Filter status
    if ($status === 'active') {
        $query->where('active', 1)->where('del', 0);
    } elseif ($status === 'inactive') {
        $query->where('active', 0)->where('del', 0);
    } elseif ($status !== 'all') {
        $query->where('del', 0);
    }

    $artikels = $query->paginate($perPage)->withQueryString();

    // Statistik summary
    $stats = $this->getStatistics($reportType, $startDate, $endDate);
    
    // Get unique rubrik untuk filter (dari kedua tabel)
    $rubriks = collect();
    $rubriks = $rubriks->merge(
        Artikel::where('del', 0)->distinct()->pluck('rubrik')->filter()
    );
    $rubriks = $rubriks->merge(
        ArtikelPublish::where('del', 0)->distinct()->pluck('rubrik')->filter()
    );
    $rubriks = $rubriks->unique()->sort()->values();

    // Get users for filter
    $users = User::select('id', 'name', 'role')
        ->orderBy('name')
        ->get();

    
    return view('reports.artikel.index', compact(
        'artikels', 
        'reportType', 
        'startDate', 
        'endDate', 
        'stats', 
        'perPage',
        'rubrik',
        'status',
        'rubriks',
        'users',
        'userId'
    ));
}

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {

        $reportType = $request->get('report_type', 'upload');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $rubrik = $request->get('rubrik');
        $status = $request->get('status');
        $userId = $request->get('user_id');

        $fileName = 'Report_Artikel_' . ucfirst($reportType) . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new ArtikelReportExport($reportType, $startDate, $endDate, $rubrik, $status,$userId), 
            $fileName
        );
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
    $reportType = $request->get('report_type', 'upload');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    $rubrik = $request->get('rubrik');
    $status = $request->get('status');
    $userId = $request->get('user_id');

    // Query data - berbeda berdasarkan report type
    if ($reportType === 'upload') {
        $query = Artikel::with('creator','event'); // Eager load creator
        if($userId){
            $query->where('add_by', $userId);
        }
        if ($startDate) {
            $query->whereDate('add_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('add_date', '<=', $endDate);
        }
        $query->orderBy('add_date', 'desc');
    } else {
        $query = ArtikelPublish::with(['editor', 'creator','event']); // Eager load editor
        if($userId){
            $query->where('edit_by', $userId);
        }
        if ($startDate) {
            $query->whereDate('edit_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('edit_date', '<=', $endDate);
        }
        $query->whereNotNull('edit_date')->orderBy('edit_date', 'desc');
    }

    // Filter rubrik
    if ($rubrik) {
        $query->where('rubrik', $rubrik);
    }

    // Filter status
    if ($status === 'active') {
        $query->where('active', 1)->where('del', 0);
    } elseif ($status === 'inactive') {
        $query->where('active', 0)->where('del', 0);
    } elseif ($status !== 'all') {
        $query->where('del', 0);
    }

    $artikels = $query->get();
    $stats = $this->getStatistics($reportType, $startDate, $endDate);

        // Format dates for display
        $periodText = 'Semua Data';
        if ($startDate && $endDate) {
            $periodText = Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($startDate) {
            $periodText = 'Mulai ' . Carbon::parse($startDate)->format('d M Y');
        } elseif ($endDate) {
            $periodText = 'Sampai ' . Carbon::parse($endDate)->format('d M Y');
        }

        $data = [
            'title' => 'Report Artikel - ' . ucfirst($reportType),
            'reportType' => $reportType,
            'period' => $periodText,
            'artikels' => $artikels,
            'stats' => $stats,
            'rubrik' => $rubrik,
            'status' => $status,
            'generatedAt' => Carbon::now()->format('d M Y H:i:s'),
        ];

        $pdf = Pdf::loadView('reports.artikel.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Report_Artikel_' . ucfirst($reportType) . '_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get statistics for the report
     */
   
    private function getStatistics($reportType, $startDate, $endDate)
    {
        if ($reportType === 'upload') {
            $query = Artikel::query();

            if ($startDate) $query->whereDate('add_date', '>=', $startDate);
            if ($endDate) $query->whereDate('add_date', '<=', $endDate);

        } else {
            $query = ArtikelPublish::query();

            if ($startDate) $query->whereDate('edit_date', '>=', $startDate);
            if ($endDate) $query->whereDate('edit_date', '<=', $endDate);
            $query->whereNotNull('edit_date');
        }

        return [
            'total_artikel'      => (clone $query)->count(),
            'total_active'       => (clone $query)->where('active', 1)->where('del', 0)->count(),
            'total_inactive'     => (clone $query)->where('active', 0)->where('del', 0)->count(),
            'total_deleted'      => (clone $query)->where('del', 1)->count(),
            'artikel_with_foto'  => (clone $query)->whereNotNull('foto')->where('foto', '!=', '')->count(),
            'total_rubrik'       => (clone $query)->distinct('rubrik')->count('rubrik'),
        ];
    }

}