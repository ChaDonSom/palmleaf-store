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
use Lunar\Models\Discount;
use Lunar\Models\Currency;
use Lunar\Models\Country;
use Lunar\Models\TaxClass;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\DiscountTypes\AmountOff;

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
        $component = Livewire::test(TriviaChallenge::class)
            ->call('openModal')
            ->assertSet('showModal', true);
        
        $this->assertNotNull($component->get('question'));
        $this->assertNotEmpty($component->get('answers'));
    }

    public function test_user_can_submit_correct_answer(): void
    {
        $question = TriviaQuestion::first();
        
        $component = Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->correct_answer)
            ->call('submitAnswer')
            ->assertSet('isCorrect', true)
            ->assertSet('showResult', true);
        
        $this->assertNotNull($component->get('discountCode'));
        
        // Verify attempt was recorded
        $this->assertDatabaseHas('trivia_attempts', [
            'correct' => true,
            'trivia_question_id' => $question->id,
        ]);
    }

    public function test_user_can_submit_wrong_answer(): void
    {
        $question = TriviaQuestion::first();
        
        $component = Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->wrong_answers[0])
            ->call('submitAnswer')
            ->assertSet('isCorrect', false)
            ->assertSet('showResult', true);
        
        $this->assertNull($component->get('discountCode'));
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

    public function test_guest_user_can_apply_trivia_discount_code(): void
    {
        $question = TriviaQuestion::first();
        
        // Submit correct answer as guest and get discount code
        $component = Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->correct_answer)
            ->call('submitAnswer')
            ->assertSet('isCorrect', true);
        
        $discountCode = $component->get('discountCode');
        $this->assertNotNull($discountCode);
        
        // Verify discount was created in database
        $this->assertDatabaseHas('lunar_discounts', [
            'coupon' => $discountCode,
        ]);

        // Get the created discount and verify max_uses_per_user is null
        $discount = Discount::where('coupon', $discountCode)->first();
        $this->assertNotNull($discount);
        $this->assertNull($discount->max_uses_per_user, 'max_uses_per_user should be null to allow guest usage');
        $this->assertEquals(1, $discount->max_uses, 'max_uses should be 1 for single use');
    }

    public function test_authenticated_user_can_apply_trivia_discount_code(): void
    {
        $user = User::factory()->create();
        $question = TriviaQuestion::first();
        
        $this->actingAs($user);
        
        // Submit correct answer as authenticated user and get discount code
        $component = Livewire::test(TriviaChallenge::class)
            ->set('selectedAnswer', $question->correct_answer)
            ->call('submitAnswer')
            ->assertSet('isCorrect', true);
        
        $discountCode = $component->get('discountCode');
        $this->assertNotNull($discountCode);
        
        // Verify discount was created in database
        $this->assertDatabaseHas('lunar_discounts', [
            'coupon' => $discountCode,
        ]);

        // Get the created discount and verify max_uses_per_user is null
        $discount = Discount::where('coupon', $discountCode)->first();
        $this->assertNotNull($discount);
        $this->assertNull($discount->max_uses_per_user, 'max_uses_per_user should be null to allow guest usage');
    }
}
