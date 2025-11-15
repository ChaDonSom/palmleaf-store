<div x-data="{ showModal: @entangle('showModal').live }">
    <!-- Trivia Button -->
    <button
        wire:click="openModal"
        class="h-16 text-sm font-medium transition"
        @if($hasAttemptedToday && $isCorrect)
            title="You've earned today's discount!"
        @elseif($hasAttemptedToday)
            title="Try again tomorrow!"
        @else
            title="Answer a Bible trivia question for a discount!"
        @endif
    >
        <div class="inline-flex items-center gap-2 px-4 py-2 border rounded-2xl transition
            @if($hasAttemptedToday && $isCorrect)
                bg-green-50 border-green-300 hover:bg-green-100
            @else
                bg-white border-slate-300 hover:bg-slate-50
            @endif
        ">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="hidden md:inline">Daily Trivia</span>
            @if($hasAttemptedToday && $isCorrect)
                <span class="ml-1 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-green-200 px-1.5 text-[10px] font-medium text-green-900">
                    ✓
                </span>
            @endif
        </div>
    </button>

    <!-- Trivia Modal -->
    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-on:click.self="$wire.closeModal()"
    >
        <!-- Scrim/Backdrop - only fades -->
        <div
            x-show="showModal"
            x-on:click="$wire.closeModal()"
            class="fixed inset-0 bg-black bg-opacity-50"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-out duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <!-- Modal body - fades and zooms -->
        <div
            x-show="showModal"
            class="relative w-full max-w-lg p-6 bg-white shadow-xl rounded-2xl"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-out duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold">Daily Bible Trivia</h3>
                <button
                    wire:click="closeModal"
                    class="transition-transform text-slate-500 hover:scale-110"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($hasAttemptedToday)
                <!-- Already Attempted Today -->
                <div class="space-y-4">
                    @if($isCorrect)
                        <div class="p-6 text-center border border-green-200 rounded-xl bg-green-50">
                            <div class="mb-2 text-4xl">🎉</div>
                            <h4 class="mb-2 text-lg font-semibold text-green-900">Congratulations!</h4>
                            <p class="mb-4 text-sm text-green-700">You've already earned today's discount!</p>

                            <div class="p-4 mb-4 font-mono text-lg font-bold bg-white border-2 border-green-300 rounded-lg text-slate-900">
                                {{ $discountCode }}
                            </div>

                            <p class="text-xs text-green-600">Use this code at checkout for 10% off your order!</p>
                        </div>
                    @else
                        <div class="p-6 text-center border rounded-xl border-slate-200 bg-slate-50">
                            <div class="mb-2 text-4xl">📅</div>
                            <h4 class="mb-2 text-lg font-semibold text-slate-900">Try Again Tomorrow</h4>
                            <p class="text-sm text-slate-700">You've already attempted today's trivia question. Come back tomorrow for another chance!</p>
                        </div>
                    @endif
                </div>
            @elseif(!$showResult)
                <!-- Question -->
                @if($question)
                    <div class="space-y-4">
                        <div class="p-4 border rounded-xl border-slate-200 bg-slate-50">
                            <p class="text-lg font-medium text-slate-900">{{ $question->question }}</p>
                        </div>

                        <div class="space-y-2">
                            @foreach($answers as $answer)
                                <label class="block">
                                    <input
                                        type="radio"
                                        wire:model.live="selectedAnswer"
                                        value="{{ $answer }}"
                                        class="hidden peer"
                                    >
                                    <div class="p-4 transition border-2 cursor-pointer rounded-xl border-slate-200 hover:border-sky-300 hover:bg-sky-50 peer-checked:border-sky-500 peer-checked:bg-sky-50">
                                        {{ $answer }}
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <button
                            wire:click="submitAnswer"
                            @if(!$selectedAnswer) disabled @endif
                            class="w-full px-6 py-3 text-sm font-medium transition rounded-2xl
                                @if($selectedAnswer)
                                    text-white bg-sky-500 hover:bg-sky-600
                                @else
                                    text-slate-400 bg-slate-200 cursor-not-allowed
                                @endif
                            "
                        >
                            Submit Answer
                        </button>
                    </div>
                @else
                    <div class="p-6 text-center border rounded-xl border-slate-200 bg-slate-50">
                        <p class="text-slate-700">No trivia questions available at the moment. Please check back later!</p>
                    </div>
                @endif
            @else
                <!-- Result -->
                <div class="space-y-4">
                    @if($isCorrect)
                        <div class="p-6 text-center border border-green-200 rounded-xl bg-green-50">
                            <div class="mb-2 text-4xl">🎉</div>
                            <h4 class="mb-2 text-lg font-semibold text-green-900">Correct!</h4>
                            <p class="mb-4 text-sm text-green-700">Great job! Here's your discount code:</p>

                            <div class="p-4 mb-4 font-mono text-lg font-bold bg-white border-2 border-green-300 rounded-lg text-slate-900">
                                {{ $discountCode }}
                            </div>

                            <p class="text-xs text-green-600">Use this code at checkout for 10% off your order!</p>
                        </div>
                    @else
                        <div class="p-6 text-center border border-red-200 rounded-xl bg-red-50">
                            <div class="mb-2 text-4xl">❌</div>
                            <h4 class="mb-2 text-lg font-semibold text-red-900">Not quite right</h4>
                            <p class="text-sm text-red-700">The correct answer was: <strong>{{ $question->correct_answer }}</strong></p>
                            <p class="mt-2 text-xs text-red-600">Try again tomorrow for another chance!</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Info -->
            <div class="mt-6 text-xs text-center text-slate-500">
                You can attempt one trivia question per day
            </div>
        </div>
    </div>
</div>
