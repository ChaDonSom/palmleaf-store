<header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 md:px-6">
        <div class="flex items-center gap-3">
            <button class="md:hidden grid h-10 w-10 place-items-center" x-data x-on:click="$dispatch('toggle-mobile-menu')">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-2xl bg-slate-900 text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <div class="text-xl font-extrabold tracking-tight">{{ config('app.name') }}</div>
                    <div class="text-xs text-slate-500">Faith‑forward apparel</div>
                </div>
            </a>
        </div>

        <!-- Desktop Search -->
        <div class="hidden w-full max-w-md items-center gap-2 md:flex">
            <div class="relative w-full">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <form method="GET" action="{{ route('search.view') }}">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $term) }}"
                        placeholder="Search products"
                        class="w-full rounded-2xl border border-slate-300 pl-9 pr-4 py-2 text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
                    />
                </form>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @livewire('components.cart')

            @if (Route::has('login'))
                <div class="hidden lg:block whitespace-nowrap">
                    @auth
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-slate-700 hover:text-slate-900 underline">{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-slate-700 hover:text-slate-900 underline">{{ __('Register') }}</a>
                        @endif
                    @endauth
                </div>
            @endif

            <!-- Settings Dropdown -->
            @if (Auth::user())
            <div class="ml-3 relative hidden lg:block whitespace-nowrap">
                <x-jet-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        @else
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    {{ Auth::user()->name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Account') }}
                        </div>

                        <x-jet-dropdown-link href="{{ route('profile.show') }}">
                            {{ __('Profile') }}
                        </x-jet-dropdown-link>

                        <x-jet-dropdown-link href="{{ route('orders') }}">
                            {{ __('My orders') }}
                        </x-jet-dropdown-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('API Tokens') }}
                            </x-jet-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf

                            <x-jet-dropdown-link href="{{ route('logout') }}"
                                     @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-jet-dropdown-link>
                        </form>
                    </x-slot>
                </x-jet-dropdown>
            </div>
            @endif

            <!-- Mobile Menu Button -->
            <div x-data="{ mobileMenu: false }">
                <button
                    x-on:click="mobileMenu = !mobileMenu"
                    class="grid flex-shrink-0 w-16 h-16 border-l border-gray-100 lg:hidden"
                >
                    <span class="sr-only">Toggle Menu</span>

                    <span class="place-self-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </span>
                </button>

                <div
                    x-cloak
                    x-transition
                    x-show="mobileMenu"
                    class="absolute right-0 top-auto z-50 w-screen p-4 sm:max-w-xs"
                >
                    <ul
                        x-on:click.away="mobileMenu = false"
                        class="py-1 bg-white border border-gray-100 shadow-xl rounded-xl"
                    >
                        @auth
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 transition">
                                {{ Auth::user()->name }}
                            </div>
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-jet-dropdown-link>

                            <x-jet-dropdown-link href="{{ route('orders') }}">
                                {{ __('My orders') }}
                            </x-jet-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-jet-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                        @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-jet-dropdown-link>
                            </form>
                        @else
                            <li>
                                <x-jet-dropdown-link href="{{ route('login') }}">
                                    {{ __('Login') }}
                                </x-jet-dropdown-link>
                            </li>
                            @if (Route::has('register'))
                                <x-jet-dropdown-link href="{{ route('register') }}">
                                    {{ __('Register') }}
                                </x-jet-dropdown-link>
                            @endif
                        @endauth
                        <li><hr></li>
                        @foreach ($this->collections as $collection)
                            <x-jet-dropdown-link
                                class="text-sm font-medium"
                                href="{{ route('collection.view', $collection->defaultUrl->slug) }}"
                            >
                                {{ $collection->translateAttribute('name') }}
                            </x-jet-dropdown-link>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
