<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'candidate_id', 'pack_id', 'phone_number', 'operator',
        'transaction_ref', 'amount', 'votes_count',
        'status', 'failure_reason', 'paid_at', 'ip_address', 'user_agent',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    // Relations
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(VotePack::class, 'pack_id');
    }

    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }

    // Scopes
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Helpers
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'success' => 'Réussi',
            'pending' => 'En attente',
            'failed'  => 'Échoué',
            default   => $this->status,
        };
    }

    public function getOperatorLabelAttribute(): string
    {
        return match($this->operator) {
            'mtn'  => 'MTN Mobile Money',
            'moov' => 'Moov Money',
            default => $this->operator,
        };
    }
}