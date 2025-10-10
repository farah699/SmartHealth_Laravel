<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireSession extends Model
{
    protected $table = 'questionnaire_sessions';
    
    protected $fillable = [
        'user_id',
        'phq9_score',
        'gad7_score',
        'phq9_interpretation',
        'gad7_interpretation',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'is_completed' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalScore(): int
    {
        return ($this->phq9_score ?? 0) + ($this->gad7_score ?? 0);
    }

    public function getOverallInterpretation(): string
    {
        $total = $this->getTotalScore();
        if ($total <= 8) return 'État psychologique stable';
        if ($total <= 18) return 'État psychologique à surveiller';
        if ($total <= 28) return 'État psychologique préoccupant';
        return 'État psychologique nécessitant une attention immédiate';
    }
}