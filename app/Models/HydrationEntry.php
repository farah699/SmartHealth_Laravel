<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HydrationEntry extends Model
{
    protected $fillable = [
        'user_id', 'drink_type', 'amount_ml', 'entry_date', 'entry_time', 'notes'
    ];

    protected $casts = [
        'amount_ml' => 'decimal:2',
        'entry_date' => 'date',
        'entry_time' => 'datetime:H:i'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('entry_date', Carbon::today());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accesseurs
    public function getDrinkTypeLabelAttribute(): string
    {
        return match($this->drink_type) {
            'water' => '💧 Eau',
            'tea' => '🍵 Thé',
            'coffee' => '☕ Café',
            'herbal_tea' => '🌿 Tisane',
            'sparkling_water' => '🫧 Eau gazeuse',
            'other' => '🥤 Autre',
            default => 'Non défini'
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        if ($this->amount_ml >= 1000) {
            return round($this->amount_ml / 1000, 1) . ' L';
        }
        return $this->amount_ml . ' ml';
    }
}