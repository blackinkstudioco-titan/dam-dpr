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


class ReportController extends Controller
{
    public function foto_akd(Request $request)
    {
        
        $perPage = $request->get('per_page', 25);
        //akd=komisi
      
        $akdFoto= KomisiDpr::leftJoin('data_foto', 'komisi_dpr.id', '=', 'data_foto.komisi_dpr_id')
                ->select('komisi_dpr.id', 'komisi_dpr.nama_komisi', \DB::raw('COUNT(data_foto.id) as total_foto'))
                ->groupBy('komisi_dpr.id', 'komisi_dpr.nama_komisi')
                ->orderBy('total_foto', 'desc');
        $akdFoto = $akdFoto->paginate($perPage)->withQueryString();
        
        return view('reports.general.akd', compact('akdFoto'));
    }
    public function artikel_akd(Request $request){
        $perPage = $request->get('per_page', 25);
        //akd=komisi
      
        $akdArtikel= KomisiDpr::leftJoin('artikel_publish', 'komisi_dpr.id', '=', 'artikel_publish.komisi_dpr_id')
                ->select('komisi_dpr.id', 'komisi_dpr.nama_komisi', \DB::raw('COUNT(artikel_publish.id) as total_foto'))
                ->groupBy('komisi_dpr.id', 'komisi_dpr.nama_komisi')
                ->orderBy('total_foto', 'desc');
        $akdArtikel = $akdArtikel->paginate($perPage)->withQueryString();
        
        return view('reports.general.akd_artikel', compact('akdArtikel'));
    }
    public function foto_kegiatan(Request $request){
      $perPage = $request->get('per_page', 25);
      $foto_kegiatan = DB::table('kategori_foto')
            ->leftJoin('data_foto', 'kategori_foto.id', '=', 'data_foto.kategorisasi_datatempo')
            ->select(
                'kategori_foto.id',
                'kategori_foto.k_name',
                DB::raw('COUNT(data_foto.id) as total_foto')
            )
            ->groupBy('kategori_foto.id', 'kategori_foto.k_name')
            ->orderByDesc('total_foto');
           
      $foto_kegiatan = $foto_kegiatan->paginate($perPage)->withQueryString();
      return view('reports.general.foto_kegiatan', compact('foto_kegiatan'));
    }
    public function artikel_kegiatan(Request $request){
      $perPage = $request->get('per_page', 25);
      $artikel_kegiatan = DB::table('kategori_foto')
            ->leftJoin('artikel_publish', 'kategori_foto.id', '=', 'artikel_publish.kategori_id')
            ->select(
                'kategori_foto.id',
                'kategori_foto.k_name',
                DB::raw('COUNT(artikel_publish.id) as total_foto')
            )
            ->groupBy('kategori_foto.id', 'kategori_foto.k_name')
            ->orderByDesc('total_foto');
           
      $artikel_kegiatan = $artikel_kegiatan->paginate($perPage)->withQueryString();
      return view('reports.general.artikel_kegiatan', compact('artikel_kegiatan'));
    }

    public function foto_dpr(Request $request){

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
        
        
        $foto_dpr= collect($counts)
            ->map(function ($total, $id) use ($anggota) {
                return [
                    'id'         => $id,
                    'nama'       => optional($anggota->firstWhere('id', $id))->nama ?? (string) $id,
                    'total_foto' => (int) $total,
                ];
        })
            ->filter(fn ($row) => ($row['total_foto'] ?? 0) > 0) // hanya > 0
            ->sortByDesc('total_foto')                            // urutkan desc
            ->values();
        
        return view('reports.general.foto_dpr', compact('foto_dpr'));

    }

    public function artikel_dpr(Request $request){

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
        $rows = ArtikelPublish::query()
            ->whereNotNull('anggota_dpr')
            ->where('active', 1) // opsional
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
        
        
        $foto_dpr= collect($counts)
            ->map(function ($total, $id) use ($anggota) {
                return [
                    'id'         => $id,
                    'nama'       => optional($anggota->firstWhere('id', $id))->nama ?? (string) $id,
                    'total_foto' => (int) $total,
                ];
        })
            ->filter(fn ($row) => ($row['total_foto'] ?? 0) > 0) // hanya > 0
            ->sortByDesc('total_foto')                            // urutkan desc
            ->values();
        
        return view('reports.general.artikel_dpr', compact('foto_dpr'));

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
?>