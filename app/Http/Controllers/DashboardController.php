<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artikel;
use App\Models\ArtikelPublish;
use App\Models\DataFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return view('dashboard', compact(
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
}