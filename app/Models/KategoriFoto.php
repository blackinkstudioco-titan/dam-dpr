<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriFoto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'kategori_foto';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'k_name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Get dropdown options for select input.
     */
    public static function getDropdownOptions(): array
    {
        return self::orderBy('k_name', 'asc')
            ->pluck('k_name', 'id')
            ->toArray();
    }

    /**
     * Get all categories ordered by name.
     */
    public static function getAllOrdered()
    {
        return self::orderBy('k_name', 'asc')->get();
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where('k_name', 'like', "%{$search}%");
    }

    /**
     * Relationship: has many data foto.
     */
    public function dataFoto()
    {
        return $this->hasMany(DataFoto::class, 'kategorisasi_datatempo', 'id');
    }

    /**
     * Get count of photos in this category.
     */
    public function getFotoCountAttribute(): int
    {
        return $this->dataFoto()->count();
    }

    /**
     * Get published photos count.
     */
    public function getPublishedFotoCountAttribute(): int
    {
        return $this->dataFoto()->where('publish', true)->count();
    }
}
