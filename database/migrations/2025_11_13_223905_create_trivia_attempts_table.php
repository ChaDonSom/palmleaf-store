<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trivia_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guests
            $table->foreignId('trivia_question_id')->constrained()->onDelete('cascade');
            $table->boolean('correct')->default(false);
            $table->string('discount_code')->nullable(); // Generated discount code on success
            $table->date('attempt_date'); // Track daily attempts
            $table->timestamps();
            
            // Index for checking daily attempts
            $table->index(['user_id', 'attempt_date']);
            $table->index(['session_id', 'attempt_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trivia_attempts');
    }
};
