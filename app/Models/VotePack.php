<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VotePack extends Model
{
    protected $table = 'vote_packs';

    protected $fillable = ['name', 'price_fcfa', 'votes_count', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'pack_id');
    }

    public function getValueLabelAttribute(): string
    {
        return "{$this->price_fcfa} FCFA = {$this->votes_count} vote(s)";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('price_fcfa');
    }
}