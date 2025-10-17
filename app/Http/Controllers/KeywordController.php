<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KeywordController extends Controller
{
    /**
     * Search keywords for autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $term = $request->input('term', '');

        // Minimum 2 characters for search
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $keywords = Keyword::searchKeywords($term, 10);

        return response()->json($keywords);
    }

    /**
     * Get popular/recent keywords.
     */
    public function popular(): JsonResponse
    {
        $keywords = Keyword::getPopularKeywords(20);

        return response()->json($keywords);
    }

    /**
     * Store new keyword(s).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'keywords' => 'required|string',
        ]);

        try {
            Keyword::createFromString($request->keywords);

            return response()->json([
                'success' => true,
                'message' => 'Keywords saved successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save keywords: ' . $e->getMessage(),
            ], 500);
        }
    }
}
