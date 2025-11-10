<?php

namespace App\Exports;

use App\Models\Artikel;
use App\Models\ArtikelPublish;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ArtikelReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $reportType;
    protected $startDate;
    protected $endDate;
    protected $rubrik;
    protected $status;
    protected $rowNumber = 0;

    public function __construct($reportType, $startDate, $endDate, $rubrik = null, $status = null)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->rubrik = $rubrik;
        $this->status = $status;
    }

    /**
     * Get collection with relationships
     */
    public function collection()
    {
        if ($this->reportType === 'upload') {
            // Query dari tabel artikel (draft) dengan creator
            $query = Artikel::with('creator');

            if ($this->startDate) {
                $query->whereDate('add_date', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $query->whereDate('add_date', '<=', $this->endDate);
            }
            $query->orderBy('add_date', 'desc');
        } else {
            // Query dari tabel artikel_publish dengan editor
            $query = ArtikelPublish::with(['editor', 'artikel']);

            if ($this->startDate) {
                $query->whereDate('edit_date', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $query->whereDate('edit_date', '<=', $this->endDate);
            }
            $query->whereNotNull('edit_date')->orderBy('edit_date', 'desc');
        }

        // Filter rubrik
        if ($this->rubrik) {
            $query->where('rubrik', $this->rubrik);
        }

        // Filter status
        if ($this->status === 'active') {
            $query->where('active', 1)->where('del', 0);
        } elseif ($this->status === 'inactive') {
            $query->where('active', 0)->where('del', 0);
        } elseif ($this->status !== 'all') {
            $query->where('del', 0);
        }

        return $query->get();
    }

    /**
     * Headings
     */
    public function headings(): array
    {
        $dateColumn = $this->reportType === 'upload' ? 'Tanggal Upload' : 'Tanggal Edit (Publish)';
        $userColumn = $this->reportType === 'upload' ? 'Ditambahkan Oleh' : 'Di Edit Oleh (Publisher)';
        $idColumn = $this->reportType === 'upload' ? 'ID Draft' : 'ID Publish / ID Draft';

        return [
            'No',
            $idColumn,
            'Tanggal Artikel',
            'Judul',
            'Rubrik',
            'Penulis',
            'Sumber',
            'Subyek',
            'Keywords',
            $dateColumn,
            $userColumn,
            'Role User',
            'Status',
            'Deskripsi',
        ];
    }

    /**
     * Mapping data
     */
    public function map($artikel): array
    {
        $this->rowNumber++;

        // Status
        $status = 'Deleted';
        if ($artikel->del == 0) {
            $status = $artikel->active == 1 ? 'Active' : 'Inactive';
        }

        // ID column
        if ($this->reportType === 'upload') {
            $idColumn = $artikel->id;
        } else {
            $idColumn = $artikel->id . ' / ' . ($artikel->artikel_draft_id ?? '-');
        }

        // User information dari relasi
        if ($this->reportType === 'upload') {
            $userName = $artikel->creator ? $artikel->creator->name : 'Unknown User';
            $userRole = $artikel->creator ? $artikel->creator->role_name : '-';
        } else {
            $userName = $artikel->editor ? $artikel->editor->name : 'Unknown User';
            $userRole = $artikel->editor ? $artikel->editor->role_name : '-';
        }

        return [
            $this->rowNumber,
            $idColumn,
            $artikel->tanggal ? $artikel->tanggal->format('d-m-Y') : '-',
            $artikel->judul,
            $artikel->rubrik ?? '-',
            $artikel->penulis ?? '-',
            $artikel->sumber ?? '-',
            $artikel->subyek ?? '-',
            $artikel->keyword ?? '-',
            $this->reportType === 'upload' 
                ? ($artikel->add_date ? $artikel->add_date->format('d-m-Y H:i:s') : '-')
                : ($artikel->edit_date ? $artikel->edit_date->format('d-m-Y H:i:s') : '-'),
            $userName, // Nama user dari relasi
            $userRole, // Role user dari relasi
            $status,
            strip_tags($artikel->deskripsi ?? '-'),
        ];
    }

    /**
     * Styles
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // ID
            'C' => 12,  // Tanggal Artikel
            'D' => 50,  // Judul
            'E' => 15,  // Rubrik
            'F' => 20,  // Penulis
            'G' => 20,  // Sumber
            'H' => 20,  // Subyek
            'I' => 30,  // Keywords
            'J' => 20,  // Tanggal Upload/Edit
            'K' => 20,  // User Name
            'L' => 12,  // User Role
            'M' => 12,  // Status
            'N' => 50,  // Deskripsi
        ];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Report Artikel ' . ucfirst($this->reportType);
    }
}