<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFoto;
use App\Models\Artikel;
use App\Models\ArtikelPublish;
use App\Models\KategoriFoto;
use App\Services\ImageService;
use Illuminate\Support\Str;

class FrontEndController extends Controller
{
    public function index(Request $request)
    {
        //$latestPhotos = Photo::latest()->take(8)->get();
        //$latestArticles = Article::latest()->take(8)->get();
        $query = DataFoto::query()->with('kategori:id,k_name');
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $dataFoto = $query->paginate($request->get('per_page', 8))
                         ->withQueryString();

        $artikel = ArtikelPublish::where('active', 1)
            ->orderByDesc('id')
            ->paginate(12);

        return view('front-end.home', [
            'dataFoto' => $dataFoto,
            'artikels' => $artikel,
        ]);
    }
    public function artikel(Request $request){
      
     $artikel = ArtikelPublish::where('active', 1)
            ->orderByDesc('id')
            ->paginate(12);

      //dd( $artikel);

      return view('front-end.artikel', [
          'artikels' => $artikel
      ]);
    }
    public function read_artikel($url_title="",$id){
      
      $artikel = ArtikelPublish::where('id', $id)
            ->where('active', 1)
            ->firstOrFail();

      //dd($artikel);
      return view('front-end.artikel-show', compact('artikel'));
    }
    public function foto(Request $request){
      //$latestPhotos = Photo::latest()->take(8)->get();
      //$latestArticles = Article::latest()->take(8)->get();
      $query = DataFoto::query()->with('kategori:id,k_name');
      // Sort
      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'desc');
      $query->orderBy($sortBy, $sortOrder);

      $dataFoto = $query->paginate($request->get('per_page', 12))
                       ->withQueryString();
      return view('front-end.foto-index', [
          'dataFoto' => $dataFoto,
      ]);
    }
    public function show($url_title="",DataFoto $dataFoto){
      // Load relationship
      $dataFoto->load('kategori:id,k_name');
      $dataFoto->incrementView();

      $url_title = str_replace('-', ' ', $url_title);
      $keywords = array_filter(explode(' ', $url_title));

      $query = DataFoto::query()
          ->where('id', '!=', $dataFoto->id)
          ->where(function($q) use ($keywords) {
              foreach ($keywords as $keyword) {
                  $q->orWhere('judul', 'LIKE', "%{$keyword}%");
              }
          });

      $fotoTerkait = $query->orderBy('created_at', 'desc')
      ->paginate(8)
      ->withQueryString();

      return view('front-end.foto-show', [
          'dataFoto' => $dataFoto,
          'fotoTerkait' => $fotoTerkait,
      ]);
    }
    public function search(Request $request){
      $query = DataFoto::query()->with('kategori:id,k_name');
      
      // Search functionality
      if ($request->filled('q')) {
          $query->search($request->q);
      }

      // Filter by publish status
      if ($request->filled('status')) {
          match($request->status) {
              'published' => $query->where('publish', true),
              'unpublished' => $query->where('publish', false),
              default => null
          };
      }

      // Filter by category
      if ($request->filled('kategori')) {
          $query->where('kategorisasi_datatempo', $request->kategori);
      }

      // Filter by date range
      if ($request->filled(['start_date', 'end_date'])) {
          $query->dateRange($request->start_date, $request->end_date);
      }

      // Filter by subject
      if ($request->filled('subyek')) {
          $query->bySubject($request->subyek);
      }

      // Sort
      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'desc');
      $query->orderBy($sortBy, $sortOrder);

      $dataFoto = $query->paginate($request->get('per_page', 12))
                       ->withQueryString();

      return view('front-end.search-result', [
          'dataFoto' => $dataFoto,
          'kategoriFoto' => KategoriFoto::all(),
      ]);

    }

}
