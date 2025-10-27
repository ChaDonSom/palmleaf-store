<header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
    <div class="flex items-center justify-between gap-3 px-4 py-3 mx-auto max-w-7xl md:px-6">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="grid text-white h-9 w-9 place-items-center rounded-2xl bg-slate-900">
                    <x-icons.shirt />
                </div>
                <div class="leading-tight">
                    <div class="text-xl font-extrabold tracking-tight">{{ config('app.name') }}</div>
                    <div class="text-xs text-slate-500">Faith‑forward apparel</div>
                </div>
            </a>
        </div>

        <!-- Desktop Search -->
        <div class="items-center hidden w-full max-w-md gap-2 md:flex">
            <div class="relative w-full">
                <x-icons.search class="absolute -translate-y-1/2 left-3 top-1/2 text-slate-400" />
                <form method="GET" action="{{ route('search.view') }}">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $term) }}"
                        placeholder="Search products"
                        class="w-full py-2 pr-4 text-sm border rounded-2xl border-slate-300 pl-9 focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
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
                        <a href="{{ route('login') }}" class="text-sm underline text-slate-700 hover:text-slate-900">{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm underline text-slate-700 hover:text-slate-900">{{ __('Register') }}</a>
                        @endif
                    @endauth
                </div>
            @endif

            <!-- Settings Dropdown -->
            @if (Auth::user())
            <div class="relative hidden ml-3 lg:block whitespace-nowrap">
                <x-jet-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                <img class="object-cover w-8 h-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        @else
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                                    {{ Auth::user()->name }}

                                    <x-icons.chevron-down class="ml-2 -mr-0.5" />
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
                        <x-icons.menu />
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
                            <div class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition border border-transparent rounded-md">
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
