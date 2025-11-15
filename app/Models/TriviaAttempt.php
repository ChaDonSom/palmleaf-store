<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriviaAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'trivia_question_id',
        'correct',
        'discount_code',
        'attempt_date',
    ];

    protected $casts = [
        'correct' => 'boolean',
        'attempt_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(TriviaQuestion::class, 'trivia_question_id');
    }
}
