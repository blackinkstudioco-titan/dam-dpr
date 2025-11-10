<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4F46E5;
        }
        
        .header h1 {
            font-size: 18px;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .info-box {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .info-box table {
            width: 100%;
        }
        
        .info-box td {
            padding: 3px 5px;
            font-size: 10px;
        }
        
        .info-box td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        
        .stat-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .stat-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table th {
            background: #4F46E5;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        
        table.data-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        
        table.data-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-published {
            background: #DEF7EC;
            color: #03543F;
        }
        
        .status-draft {
            background: #FDF6B2;
            color: #723B13;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #e5e7eb;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Periode: {{ $period }}</p>
        <p style="font-size: 9px;">Dicetak pada: {{ $generatedAt }}</p>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <table>
            <tr>
                <td>Tipe Report:</td>
                <td>{{ ucfirst($reportType) }}</td>
                <td>Total Foto:</td>
                <td>{{ number_format($stats['total_foto']) }}</td>
            </tr>
            <tr>
                <td>Total Size:</td>
                <td>{{ $stats['total_size'] }}</td>
                <td>Published:</td>
                <td>{{ number_format($stats['total_published']) }}</td>
            </tr>
            <tr>
                <td>Total Views:</td>
                <td>{{ number_format($stats['total_views']) }}</td>
                <td>Total Downloads:</td>
                <td>{{ number_format($stats['total_downloads']) }}</td>
            </tr>
        </table>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats">
        <div class="stat-item">
            <div class="label">Total Foto</div>
            <div class="value">{{ number_format($stats['total_foto']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Total Size</div>
            <div class="value">{{ $stats['total_size'] }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Published</div>
            <div class="value">{{ number_format($stats['total_published']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Draft</div>
            <div class="value">{{ number_format($stats['total_draft']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Views</div>
            <div class="value">{{ number_format($stats['total_views']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Downloads</div>
            <div class="value">{{ number_format($stats['total_downloads']) }}</div>
        </div>
    </div>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 10%;">MM ID</th>
                <th style="width: 25%;">Judul</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 12%;">{{ $reportType === 'upload' ? 'Tgl Upload' : 'Tgl Edit' }}</th>
                <th style="width: 10%;">{{ $reportType === 'upload' ? 'Fotografer' : 'Edit By' }}</th>
                <th style="width: 8%;">Size</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 6%;">Views</th>
                <th style="width: 6%;">DL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fotos as $index => $foto)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $foto->mm_id }}</td>
                    <td>{{ Str::limit($foto->judul, 40) }}</td>
                    <td>{{ $foto->kategori->nama_kategori ?? '-' }}</td>
                    <td>
                        @if($reportType === 'upload')
                            {{ $foto->created_at->format('d/m/Y H:i') }}
                        @else
                            {{ $foto->edit_date ? $foto->edit_date->format('d/m/Y H:i') : '-' }}
                        @endif
                    </td>
                    <td>
                        @if($reportType === 'upload')
                            {{ $foto->perekam ?? '-' }}
                        @else
                            {{ $foto->edit_by ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $foto->formatted_file_size }}</td>
                    <td>
                        @if($foto->publish == 1)
                            <span class="status-badge status-published">Published</span>
                        @else
                            <span class="status-badge status-draft">Draft</span>
                        @endif
                    </td>
                    <td>{{ number_format($foto->view) }}</td>
                    <td>{{ number_format($foto->download) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Report Aset Foto - Digital Asset Management System</p>
        <p>Halaman {PAGE_NUM} dari {PAGE_COUNT}</p>
    </div>
</body>
</html>