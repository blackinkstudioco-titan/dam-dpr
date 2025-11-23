<?php

namespace App\Http\Controllers;

use App\Models\DataFoto;
use App\Models\User;
use App\Exports\FotoReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportFotoController extends Controller
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
        $userId = $request->get('user_id');

        $query = DataFoto::with(['kategori', 'anggotaDpr', 'komisiDpr', 'album']);

        if ($reportType === 'upload') {
            if($userId){
                 $query->where('add_by', $userId);
            }
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            $query->orderBy('created_at', 'desc');
        } else {
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

        // Get users for filter
        $users = User::select('id', 'name', 'role')
        ->orderBy('name')
        ->get();

        $fotos = $query->paginate($perPage)->withQueryString();
        $stats = $this->getStatistics($reportType, $startDate, $endDate);

        return view('reports.foto.index', compact('fotos', 'reportType', 'startDate', 'endDate', 'stats', 'perPage','users','userId'));
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $reportType = $request->get('report_type', 'upload');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $fileName = 'Report_Foto_' . ucfirst($reportType) . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new FotoReportExport($reportType, $startDate, $endDate), 
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

        // Query data
        $query = DataFoto::with(['kategori', 'anggotaDpr', 'komisiDpr', 'album']);

        if ($reportType === 'upload') {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            $query->orderBy('created_at', 'desc');
        } else {
            if ($startDate) {
                $query->whereDate('edit_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('edit_date', '<=', $endDate);
            }
            $query->whereNotNull('edit_date')->orderBy('edit_date', 'desc');
        }

        $fotos = $query->get();
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
            'title' => 'Report Aset Foto - ' . ucfirst($reportType),
            'reportType' => $reportType,
            'period' => $periodText,
            'fotos' => $fotos,
            'stats' => $stats,
            'generatedAt' => Carbon::now()->format('d M Y H:i:s'),
        ];

        $pdf = Pdf::loadView('reports.foto.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Report_Foto_' . ucfirst($reportType) . '_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get statistics for the report
     */
    private function getStatistics($reportType, $startDate, $endDate)
    {
        $query = DataFoto::query();

        if ($reportType === 'upload') {
            if ($startDate) $query->whereDate('created_at', '>=', $startDate);
            if ($endDate) $query->whereDate('created_at', '<=', $endDate);
        } else {
            if ($startDate) $query->whereDate('edit_date', '>=', $startDate);
            if ($endDate) $query->whereDate('edit_date', '<=', $endDate);
            $query->whereNotNull('edit_date');
        }

        return [
            'total_foto' => $query->count(),
            'total_size' => $this->formatBytes($query->sum('f_size')),
            'total_published' => $query->where('publish', 1)->count(),
            'total_draft' => $query->where('publish', 0)->count(),
            'total_views' => $query->sum('view'),
            'total_downloads' => $query->sum('download'),
        ];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        if ($bytes == 0) return '0 B';
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}