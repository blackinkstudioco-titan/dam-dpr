<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ========================================
     * RELATIONSHIPS
     * ========================================
     */

    /**
     * Artikel yang dibuat oleh user ini (draft)
     */
    public function artikelCreated()
    {
        return $this->hasMany(Artikel::class, 'add_by', 'id');
    }

    /**
     * Artikel yang diedit oleh user ini (draft)
     */
    public function artikelEdited()
    {
        return $this->hasMany(Artikel::class, 'edit_by', 'id');
    }

    /**
     * Artikel publish yang dibuat oleh user ini
     */
    public function artikelPublishCreated()
    {
        return $this->hasMany(ArtikelPublish::class, 'add_by', 'id');
    }

    /**
     * Artikel publish yang diedit oleh user ini
     */
    public function artikelPublishEdited()
    {
        return $this->hasMany(ArtikelPublish::class, 'edit_by', 'id');
    }

    /**
     * ========================================
     * HELPER METHODS
     * ========================================
     */

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }


    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is editor
     */
    public function isEditor()
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is uploader
     */
    public function isUploader()
    {
        return $this->role === 'uploader';
    }

    /**
     * Get role badge color for display
     */
    public function getRoleBadgeColorAttribute()
    {
        return match($this->role) {
            'admin' => 'red',
            'editor' => 'blue',
            'uploader' => 'green',
            'guest' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get formatted role name
     */
    public function getRoleNameAttribute()
    {
        return ucfirst($this->role);
    }
}