@guest
    <form wire:submit.prevent="saveUser">
        <div class="bg-white border border-gray-100 rounded-xl">
            <div class="flex items-center h-16 px-6 border-b border-gray-100">
                <h3 class="text-lg font-medium">
                    Sign up (optional)
                </h3>

                @if ($currentStep > $step)
                    <button
                        class="px-5 py-2 ml-auto text-sm font-medium text-gray-600 rounded-3xl hover:bg-gray-100 hover:text-gray-700"
                        type="button" wire:click.prevent="$set('currentStep', {{ $step }})">
                        Edit
                    </button>
                @endif


            </div>
            <div class="p-6">
                @if ($step == $currentStep)
                    <div class="grid grid-cols-6 gap-4">
                        <label
                            class="flex items-center p-2 rounded-3xl cursor-pointer hover:bg-gray-50 col-span-12 sm:col-span-3">
                            <input class="w-5 h-5 text-green-600 border-gray-100 rounded-3xl focus:ring-green-600"
                                type="checkbox" value="1" wire:model="signup" />

                            <span class="ml-2 text-xs font-medium">
                                {{ __('Use these details to sign up for a new user account') }}
                            </span>
                        </label>

                        @if ($signup)
                            <p class="col-span-12">
                                {{ __('Please provide a password for your new user account.') }}
                            </p>

                            <x-jet-validation-errors class="col-span-12" />

                            <x-input.group class="col-span-12" label="{{ __('Password') }}">
                                <x-input.text id="password" class="block mt-1 w-full max-w-xs" type="password" name="password" required autocomplete="new-password" wire:model.defer="password" />
                            </x-input.group>

                            <x-input.group class="col-span-12" label="{{ __('Confirm Password') }}">
                                <x-input.text id="password_confirmation" class="block mt-1 w-full max-w-xs" type="password" name="password_confirmation" required autocomplete="new-password" wire:model.defer="password_confirmation" />
                            </x-input.group>

                            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                <div class="col-span-3">
                                    <x-jet-label for="terms">
                                        <div class="flex items-center">
                                            <x-jet-checkbox name="terms" id="terms"/>

                                            <div class="ml-2">
                                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </x-jet-label>
                                </div>
                            @endif

                            <div class="flex items-center justify-end col-span-12">
                                <x-jet-button>
                                    {{ __('Register') }}
                                </x-jet-button>
                            </div>
                        @endif

                        <div class="mt-6 text-right col-span-12">
                            @if($signup)
                            @else
                                <button
                                    class="px-5 py-3 text-sm font-medium text-white bg-green-700 rounded-3xl hover:bg-green-600"
                                    type="submit" wire:key="submit_btn" wire:loading.attr="disabled" wire:target="saveUser">
                                    <span wire:loading.remove wire:target="saveUser">
                                        {{ $signup ? __('Save') : __('Next') }}
                                    </span>

                                    <span wire:loading wire:target="saveUser">
                                        <span class="inline-flex items-center">
                                            Saving

                                            <x-icon.loading />
                                        </span>
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
@endguest
