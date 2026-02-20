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
        
        .status-active {
            background: #DEF7EC;
            color: #03543F;
        }
        
        .status-inactive {
            background: #FDF6B2;
            color: #723B13;
        }
        
        .status-deleted {
            background: #FDE8E8;
            color: #9B1C1C;
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
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Periode: {{ $period }}</p>
        @if($rubrik)
            <p>Rubrik: {{ $rubrik }}</p>
        @endif
        <p style="font-size: 9px;">Dicetak pada: {{ $generatedAt }}</p>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <table>
            <tr>
                <td>Tipe Report:</td>
                <td>{{ ucfirst($reportType) }}</td>
                <td>Total Artikel:</td>
                <td>{{ number_format($stats['total_artikel']) }}</td>
            </tr>
            <tr>
                <td>Active:</td>
                <td>{{ number_format($stats['total_active']) }}</td>
                <td>Inactive:</td>
                <td>{{ number_format($stats['total_inactive']) }}</td>
            </tr>
            <tr>
                <td>Deleted:</td>
                <td>{{ number_format($stats['total_deleted']) }}</td>
                <td>Dengan Foto:</td>
                <td>{{ number_format($stats['artikel_with_foto']) }}</td>
            </tr>
        </table>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats">
        <div class="stat-item">
            <div class="label">Total Artikel</div>
            <div class="value">{{ number_format($stats['total_artikel']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Active</div>
            <div class="value">{{ number_format($stats['total_active']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Inactive</div>
            <div class="value">{{ number_format($stats['total_inactive']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Deleted</div>
            <div class="value">{{ number_format($stats['total_deleted']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Dengan Foto</div>
            <div class="value">{{ number_format($stats['artikel_with_foto']) }}</div>
        </div>
        <div class="stat-item">
            <div class="label">Total Rubrik</div>
            <div class="value">{{ number_format($stats['total_rubrik']) }}</div>
        </div>
    </div>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Penugasan</th>
                <th style="width: 30%;">Judul</th> 
                <th style="width: 12%;">Penulis</th>
                <th style="width: 10%;">AKD</th>
                <th style="width: 10%;">Jenis Artikel</th>
                <th style="width: 10%;">Anggota DPR</th>
                <th style="width: 12%;">{{ $reportType === 'upload' ? 'Tgl Upload' : 'Tgl Edit' }}</th>
                <th style="width: 10%;">{{ $reportType === 'upload' ? 'Add By' : 'Edit By' }}</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 7%;">Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($artikels as $index => $artikel)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td> {{ $artikel->event?->nama_event ? $artikel->event->nama_event : '-' }}
                        <br/>
                        {{ $artikel->event?->tanggal ? $artikel->event->tanggal->format('d M Y H:i') : '-' }}
                    </td>
                    <td>{{ Str::limit($artikel->judul, 50) }}</td>
                    <td>{{ $artikel->penulis ?? '-' }}</td>
                     <td>{{ $artikel->kategori->k_name ?? '-' }}</td>
                    <td>{{$artikel->komisiDpr->nama_komisi ?? '-'}}</td>
                    <td><small>{{$artikel->anggota_dpr ?? '-'}}</small> </td>
                    <td>
                        @if($reportType === 'upload')
                            {{ $artikel->add_date ? $artikel->add_date->format('d/m/Y H:i') : '-' }}
                        @else
                            {{ $artikel->edit_date ? $artikel->edit_date->format('d/m/Y H:i') : '-' }}
                        @endif
                    </td>
                 <td>
                    @if($reportType === 'upload')
                        {{ $artikel->creator ? $artikel->creator->name : 'Unknown User' }}
                        ({{ $artikel->creator ? $artikel->creator->role_name : '-' }})
                    @else
                        {{ $artikel->editor ? $artikel->editor->name : 'Unknown User' }}
                        ({{ $artikel->editor ? $artikel->editor->role_name : '-' }})
                    @endif
                </td>
                    <td>
                        @if($artikel->del == 1)
                            <span class="status-badge status-deleted">Deleted</span>
                        @elseif($artikel->active == 1)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $artikel->foto ? 'Ada' : 'Tidak' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Report Artikel - Digital Asset Management System</p>
        <p>Halaman {PAGE_NUM} dari {PAGE_COUNT}</p>
    </div>
</body>
</html>