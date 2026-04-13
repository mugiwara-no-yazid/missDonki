<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    protected $fillable = ['name', 'number', 'photo_path', 'bio', 'is_active', 'total_votes'];

    protected $casts = ['is_active' => 'boolean'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : asset('images/placeholder.png');
    }

    // Incrémente le compteur dénormalisé de façon atomique
    public function incrementVotes(int $count): void
    {
        $this->increment('total_votes', $count);
    }

    // Scope pour les candidates actives triées par votes
    public function scopeRanked($query)
    {
        return $query->where('is_active', true)->orderByDesc('total_votes');
    }
}