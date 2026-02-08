<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artikel;
use App\Models\ArtikelPublish;
use App\Models\DataFoto;
use App\Models\AnggotaDpr;
use App\Models\KomisiDpr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        // ========================================
        // STATISTIK FOTO
        // ========================================
        $totalFoto = DataFoto::count();
        $fotoPublished = DataFoto::where('publish', 1)->count();
        $fotoDraft = DataFoto::where('publish', 0)->count();
        $fotoThisMonth = DataFoto::whereMonth('created_at', Carbon::now()->month)
                                  ->whereYear('created_at', Carbon::now()->year)
                                  ->count();
        $totalFotoViews = DataFoto::sum('view');
        $totalFotoDownloads = DataFoto::sum('download');
        $totalFotoSize = $this->formatBytes(DataFoto::sum('f_size'));

        // ========================================
        // STATISTIK ARTIKEL DRAFT
        // ========================================
        $totalArtikelDraft = Artikel::count();
        $artikelDraftActive = Artikel::where('active', 1)->where('del', 0)->count();
        $artikelDraftInactive = Artikel::where('active', 0)->where('del', 0)->count();
        $artikelDraftDeleted = Artikel::where('del', 1)->count();
        $artikelDraftThisMonth = Artikel::whereMonth('add_date', Carbon::now()->month)
                                         ->whereYear('add_date', Carbon::now()->year)
                                         ->count();

        // ========================================
        // STATISTIK ARTIKEL PUBLISH
        // ========================================
        $totalArtikelPublish = ArtikelPublish::count();
        $artikelPublishActive = ArtikelPublish::where('active', 1)->where('del', 0)->count();
        $artikelPublishInactive = ArtikelPublish::where('active', 0)->where('del', 0)->count();
        $artikelPublishThisMonth = ArtikelPublish::whereMonth('edit_date', Carbon::now()->month)
                                                   ->whereYear('edit_date', Carbon::now()->year)
                                                   ->count();

        // ========================================
        // STATISTIK USER
        // ========================================
        $totalUsers = User::count();
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
                            ->groupBy('role')
                            ->pluck('total', 'role')
                            ->toArray();
        
        $totalAdmin = $usersByRole['admin'] ?? 0;
        $totalEditor = $usersByRole['editor'] ?? 0;
        $totalUploader = $usersByRole['uploader'] ?? 0;
        $totalGuest = $usersByRole['guest'] ?? 0;

        // ========================================
        // AKTIVITAS TERBARU
        // ========================================
        
        // Foto terbaru (5 terakhir)
        $recentFotos = DataFoto::with(['kategori', 'album'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        // Artikel draft terbaru (5 terakhir)
        $recentArtikelDrafts = Artikel::with('creator')
                                       ->orderBy('add_date', 'desc')
                                       ->limit(5)
                                       ->get();

        // Artikel publish terbaru (5 terakhir)
        $recentArtikelPublish = ArtikelPublish::with('editor')
                                                ->orderBy('edit_date', 'desc')
                                                ->limit(5)
                                                ->get();

        // ========================================
        // TOP CONTRIBUTORS
        // ========================================
        
        // User dengan artikel terbanyak
        $topArticleCreators = Artikel::select('add_by', DB::raw('count(*) as total'))
                                      ->whereNotNull('add_by')
                                      ->groupBy('add_by')
                                      ->orderBy('total', 'desc')
                                      ->limit(5)
                                      ->with('creator')
                                      ->get();
        // Render view dengan data statistik

       
        $jumlahPerKomisi = KomisiDpr::leftJoin('data_foto', 'komisi_dpr.id', '=', 'data_foto.komisi_dpr_id')
                ->select('komisi_dpr.id', 'komisi_dpr.nama_komisi', \DB::raw('COUNT(data_foto.id) as total_foto'))
                ->groupBy('komisi_dpr.id', 'komisi_dpr.nama_komisi')
                ->orderBy('total_foto', 'desc')
                ->limit(5)
                ->get();
        
        
        $jumlahPerKegiatan = DB::table('kategori_foto')
            ->leftJoin('data_foto', 'kategori_foto.id', '=', 'data_foto.kategorisasi_datatempo')
            ->select(
                'kategori_foto.id',
                'kategori_foto.k_name',
                DB::raw('COUNT(data_foto.id) as total_foto')
            )
            ->groupBy('kategori_foto.id', 'kategori_foto.k_name')
            ->orderByDesc('total_foto')
            ->limit(5)
            ->get();

        //anggota dpr menggunakan regex

        // 1) Kamus anggota
            $anggota = AnggotaDpr::select('id','nama')->get();
            $map = [];             // canonical name => ['id'=>.., 'nama'=>..]
            $counts = [];          // id => total
            foreach ($anggota as $a) {
                $key = $this->canonical($a->nama);
                $map[$key] = ['id' => $a->id, 'nama' => $a->nama];
                $counts[$a->id] = 0;
            }

        // 2) Scan data_foto
        $rows = DataFoto::query()
            ->whereNotNull('anggota_dpr')
            ->where('publish', 1) // opsional
            ->get(['id','anggota_dpr']);

        foreach ($rows as $row) {
            // Heuristik split: pecah kasar by koma, lalu normalisasi dan cocokkan dengan kamus
            $tokens = collect(explode(';', $row->anggota_dpr))
                ->map(fn($t) => trim($t))
                ->filter()
                ->values();

        // Gabungkan kembali token gelar ke nama sebelumnya (opsional, kalau kamu temukan pola sering)
        $names = [];
        $buf = '';
        foreach ($tokens as $t) {
                // heuristik sederhana: token pendek dominan gelar
                if ($buf === '') { $buf = $t; continue; }
                if (strlen(str_replace(['.',' '],'',$t)) <= 5) {
                    $buf .= ', '.$t; // anggap gelar -> merge
                } else {
                    $names[] = $buf;
                    $buf = $t;
                }
            }
            if ($buf !== '') $names[] = $buf;

            foreach ($names as $raw) {
                $key = $this->canonical($raw);
                if (isset($map[$key])) {
                    $counts[$map[$key]['id']]++;
                }
            }
        }

        // 3) Susun hasil
        /*
        $JumlahPerAnggotaDPR = collect($counts)
            ->map(fn($total, $id) => [
                'id' => $id,
                'nama' => $anggota->firstWhere('id', $id)->nama ?? (string)$id,
                'total_foto' => $total,
            ])
            ->sortByDesc('total_foto')
            ->values();
        */
        
        
        $JumlahPerAnggotaDPR = collect($counts)
            ->map(function ($total, $id) use ($anggota) {
                return [
                    'id'         => $id,
                    'nama'       => optional($anggota->firstWhere('id', $id))->nama ?? (string) $id,
                    'total_foto' => (int) $total,
                ];
            })
            ->filter(fn ($row) => ($row['total_foto'] ?? 0) > 0) // hanya > 0
            ->sortByDesc('total_foto')                            // urutkan desc
            ->take(5)                                             // ambil 5 teratas
            ->values();





        return view('dashboard', compact(
            'JumlahPerAnggotaDPR',
            'jumlahPerKegiatan',
            'jumlahPerKomisi',
            // Foto Stats
            'totalFoto',
            'fotoPublished',
            'fotoDraft',
            'fotoThisMonth',
            'totalFotoViews',
            'totalFotoDownloads',
            'totalFotoSize',
            
            // Artikel Draft Stats
            'totalArtikelDraft',
            'artikelDraftActive',
            'artikelDraftInactive',
            'artikelDraftDeleted',
            'artikelDraftThisMonth',
            
            // Artikel Publish Stats
            'totalArtikelPublish',
            'artikelPublishActive',
            'artikelPublishInactive',
            'artikelPublishThisMonth',
            
            // User Stats
            'totalUsers',
            'totalAdmin',
            'totalEditor',
            'totalUploader',
            'totalGuest',
            
            // Recent Activities
            'recentFotos',
            'recentArtikelDrafts',
            'recentArtikelPublish',
            'topArticleCreators'

        ));
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

    private function canonical(string $name): string {
        // Normalisasi (buang gelar umum prefix/suffix jika ada, samakan case)
        $n = ' '.trim($name).' ';
        $n = preg_replace(['/\\bprof\\.\\s*/i','/\\bdr\\.\\s*/i','/\\bdrs\\.\\s*/i','/\\bdra\\.\\s*/i','/\\bir\\.\\s*/i','/\\bhj\\.\\s*/i','/\\bh\\.\\s*/i'], '', $n);
        $parts = array_map('trim', explode(',', $n));
        $keep = [];
        foreach ($parts as $p) {
            if ($p === '') continue;
            // buang part yg “tampak” gelar suffix (opsional)
            if (preg_match('/^(s|m)[a-z\\. ]{0,6}$/i', str_replace('.', '', $p))) continue;
            $keep[] = $p;
        }
        $n = preg_replace('/\\s+/', ' ', trim(implode(' ', $keep)));
        return mb_strtoupper($n, 'UTF-8');
    }



}