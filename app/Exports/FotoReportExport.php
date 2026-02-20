<?php

namespace App\Exports;

use App\Models\DataFoto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FotoReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $reportType;
    protected $startDate;
    protected $endDate;
    protected $akd;
    protected $jenisFoto;
    protected $DPR;
    protected $rowNumber = 0;

    public function __construct($reportType, $startDate, $endDate,$akd,$kegiatan,$DPR)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->akd = $akd;
        $this->jenisFoto = $kegiatan;
        $this->DPR = $DPR;
        
    }

    /**
     * Query data
     */
    public function query()
    {
        $query = DataFoto::with(['kategori', 'anggotaDpr', 'komisiDpr', 'album']);

        if ($this->reportType === 'upload') {
            if ($this->startDate) {
                $query->whereDate('created_at', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $query->whereDate('created_at', '<=', $this->endDate);
            }
            if($this->akd){
                $query->where('komisi_dpr_id', $this->akd);
            }
            if($this->jenisFoto){
                $query->where('kategorisasi_datatempo', $this->jenisFoto);
            }
            if($this->DPR){
                $query->where('anggota_dpr','like', "%{$this->DPR}%");
            }
            $query->orderBy('created_at', 'desc');
        } else {
            if ($this->startDate) {
                $query->whereDate('edit_date', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $query->whereDate('edit_date', '<=', $this->endDate);
            }
            if($this->akd){
                $query->where('komisi_dpr_id', $this->akd);
            }
            if($this->jenisFoto){
                $query->where('kategorisasi_datatempo', $this->jenisFoto);
            }
            if($this->DPR){
                $query->where('anggota_dpr','like', "%{$this->DPR}%");
            }
            $query->whereNotNull('edit_date')->orderBy('edit_date', 'desc');
        }

        return $query;
    }

    /**
     * Headings
     */
    public function headings(): array
    {
        $dateColumn = $this->reportType === 'upload' ? 'Tanggal Upload' : 'Tanggal Edit';
        $userColumn = $this->reportType === 'upload' ? 'Perekam' : 'Di Edit Oleh';

        return [
            'No',
            'Penugasan',
            'Tanggal',
            'Judul',
            'Deskripsi',
            'Kategori',
            'Album',
            'AKD',
            'Anggota DPR',
            $dateColumn,
            $userColumn,
            'Size',
            'Status',
            'Views',
            'Downloads',
            'Keywords',
            'Subyek',
        ];
    }

    /**
     * Mapping data
     */
    public function map($foto): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $foto?->event?->nama_event ?? '-',
            $foto?->event?->tanggal ?? '-',
            $foto->judul,
            strip_tags($foto->deskrp),
            $foto->kategori->k_name ?? '-',
            $foto->album->nama_album ?? '-',
            $foto->komisiDpr->nama_komisi ?? '-',
            $foto->anggota_dpr ?? '-',
            $this->reportType === 'upload' 
                ? $foto->created_at->format('d-m-Y H:i:s') 
                : ($foto->edit_date ? $foto->edit_date->format('d-m-Y H:i:s') : '-'),
            $this->reportType === 'upload' ? ($foto->uploader->name ?? '-') : ($foto->editor->name ?? '-'),
            $this->formatBytes($foto->f_size),
            $foto->publish == 1 ? 'Published' : 'Draft',
            $foto->view ?? 0,
            $foto->download ?? 0,
            $foto->k_word,
            $foto->subyek,
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
            'A' => 5,
            'B' => 15,
            'C' => 40,
            'D' => 50,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 12,
            'K' => 12,
            'L' => 10,
            'M' => 12,
            'N' => 30,
            'O' => 20,
        ];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Report Foto ' . ucfirst($this->reportType);
    }

    /**
     * Format bytes
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