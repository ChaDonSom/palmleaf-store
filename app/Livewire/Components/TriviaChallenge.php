<?php

namespace App\Livewire\Components;

use App\Models\TriviaQuestion;
use App\Models\TriviaAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Carbon\Carbon;
use Lunar\Models\Discount;

class TriviaChallenge extends Component
{
    public $question;
    public $answers = [];
    public $selectedAnswer = null;
    public $showResult = false;
    public $isCorrect = false;
    public $discountCode = null;
    public $hasAttemptedToday = false;
    public $showModal = false;

    public function mount()
    {
        $this->checkDailyAttempt();
        
        if (!$this->hasAttemptedToday) {
            $this->loadQuestion();
        }
    }

    public function loadQuestion()
    {
        // Get a random active question
        $this->question = TriviaQuestion::where('active', true)
            ->inRandomOrder()
            ->first();
        
        if ($this->question) {
            $this->answers = $this->question->getShuffledAnswers();
        }
    }

    public function checkDailyAttempt()
    {
        $today = Carbon::today();
        $userId = Auth::id();
        $sessionId = session()->getId();

        $query = TriviaAttempt::where('attempt_date', $today);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $attempt = $query->first();
        
        if ($attempt) {
            $this->hasAttemptedToday = true;
            
            if ($attempt->correct) {
                $this->isCorrect = true;
                $this->discountCode = $attempt->discount_code;
                $this->showResult = true;
            }
        }
    }

    public function submitAnswer()
    {
        if (!$this->selectedAnswer || !$this->question) {
            return;
        }

        $this->isCorrect = $this->selectedAnswer === $this->question->correct_answer;
        $this->showResult = true;

        // Generate discount code and create Lunar discount if correct
        if ($this->isCorrect) {
            $this->discountCode = 'TRIVIA-' . strtoupper(Str::random(8));
            $this->createLunarDiscount($this->discountCode);
        }

        // Record the attempt
        TriviaAttempt::create([
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'trivia_question_id' => $this->question->id,
            'correct' => $this->isCorrect,
            'discount_code' => $this->discountCode,
            'attempt_date' => Carbon::today(),
        ]);

        $this->hasAttemptedToday = true;
    }

    /**
     * Create a Lunar discount for the trivia code
     */
    protected function createLunarDiscount(string $code): void
    {
        try {
            Discount::create([
                'name' => 'Daily Bible Trivia Discount',
                'handle' => strtolower($code),
                'coupon' => $code,
                'type' => 'coupon',
                'starts_at' => now(),
                'ends_at' => now()->addDays(7), // Valid for 7 days
                'max_uses' => 1, // Single use
                'priority' => 1,
                'stop' => false,
                'data' => [
                    'percentage' => 10, // 10% discount
                ],
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the trivia attempt
            \Log::error('Failed to create Lunar discount: ' . $e->getMessage());
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.components.trivia-challenge');
    }
}
