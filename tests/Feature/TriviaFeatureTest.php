<?php

namespace Tests\Feature;

use App\Models\TriviaQuestion;
use App\Models\TriviaAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;
use App\Livewire\Components\TriviaChallenge;

class TriviaFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a sample trivia question
        TriviaQuestion::create([
            'question' => 'Who built the ark?',
            'correct_answer' => 'Noah',
            'wrong_answers' => ['Moses', 'Abraham', 'David'],
            'active' => true,
        ]);
    }

    public function test_trivia_component_renders(): void
    {
        Livewire::test(TriviaChallenge::class)
            ->assertStatus(200);
    }

    public function test_user_can_view_trivia_question(): void
    {
        Livewire::test(TriviaChallenge::class)
            ->call('openModal')
            ->assertSet('showModal', true)
            ->assertNotNull('question')
            ->assertNotEmpty('answers');
    }

    public function test_user_can_submit_correct_answer(): void
    {
        $question = TriviaQuestion::first();
        
        Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->correct_answer)
            ->call('submitAnswer')
            ->assertSet('isCorrect', true)
            ->assertSet('showResult', true)
            ->assertNotNull('discountCode');
        
        // Verify attempt was recorded
        $this->assertDatabaseHas('trivia_attempts', [
            'correct' => true,
            'trivia_question_id' => $question->id,
        ]);
    }

    public function test_user_can_submit_wrong_answer(): void
    {
        $question = TriviaQuestion::first();
        
        Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->wrong_answers[0])
            ->call('submitAnswer')
            ->assertSet('isCorrect', false)
            ->assertSet('showResult', true)
            ->assertNull('discountCode');
    }

    public function test_user_cannot_attempt_twice_in_same_day(): void
    {
        $question = TriviaQuestion::first();
        
        // First attempt
        TriviaAttempt::create([
            'session_id' => session()->getId(),
            'trivia_question_id' => $question->id,
            'correct' => false,
            'attempt_date' => Carbon::today(),
        ]);
        
        // Try to load component
        Livewire::test(TriviaChallenge::class)
            ->assertSet('hasAttemptedToday', true);
    }

    public function test_authenticated_user_can_attempt_trivia(): void
    {
        $user = User::factory()->create();
        $question = TriviaQuestion::first();
        
        $this->actingAs($user);
        
        Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->correct_answer)
            ->call('submitAnswer')
            ->assertSet('isCorrect', true);
        
        // Verify attempt is associated with user
        $this->assertDatabaseHas('trivia_attempts', [
            'user_id' => $user->id,
            'correct' => true,
        ]);
    }
}
