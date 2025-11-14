<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TriviaQuestion extends Model
{
    protected $fillable = [
        'question',
        'correct_answer',
        'wrong_answers',
        'active',
    ];

    protected $casts = [
        'wrong_answers' => 'array',
        'active' => 'boolean',
    ];

    public function attempts(): HasMany
    {
        return $this->hasMany(TriviaAttempt::class);
    }

    /**
     * Get all answer options shuffled
     */
    public function getShuffledAnswers(): array
    {
        $answers = array_merge([$this->correct_answer], $this->wrong_answers);
        shuffle($answers);
        return $answers;
    }
}
