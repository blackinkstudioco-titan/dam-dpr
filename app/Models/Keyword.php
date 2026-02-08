<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'keyword';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'k_word',
    ];

    /**
     * Search keywords by term.
     */
    public static function searchKeywords(string $term, int $limit = 10): array
    {
        return self::where('k_word', 'like', "%{$term}%")
            ->orderBy('k_word', 'asc')
            ->limit($limit)
            ->pluck('k_word')
            ->toArray();
    }

    /**
     * Create or get existing keywords from comma-separated string.
     */
    public static function createFromString(?string $keywords): void
    {
        if (!$keywords) {
            return;
        }

        // Split by comma and clean each keyword
        $keywordArray = array_filter(
            array_map('trim', explode(';', $keywords)),
            fn($item) => !empty($item)
        );

        foreach ($keywordArray as $keyword) {
            // Insert or ignore if exists
            self::firstOrCreate(['k_word' => $keyword]);
        }
    }

    /**
     * Get popular keywords (most used).
     */
    public static function getPopularKeywords(int $limit = 20): array
    {
        // This would require a count from data_foto table
        // For now, just return latest keywords
        return self::orderBy('created_at', 'desc')
            ->limit($limit)
            ->pluck('k_word')
            ->toArray();
    }
}
