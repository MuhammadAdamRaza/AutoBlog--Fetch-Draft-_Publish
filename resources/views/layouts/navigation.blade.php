<nav x-data="{ open: false }" class="relative z-10 border-b border-rule bg-paper">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[4.5rem] items-center justify-between gap-4 py-3">
            <div class="flex min-w-0 items-center gap-8">
                <a
                    href="{{ route('admin.posts.index') }}"
                    class="max-w-[13rem] font-serif text-lg font-semibold leading-tight tracking-tight text-ink sm:max-w-none sm:text-xl"
                >
                    {{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}
                </a>

                <div class="hidden items-center gap-1 sm:flex">
                    <a
                        href="{{ route('admin.posts.index') }}"
                        @class([
                            'rounded-full px-4 py-2 text-sm font-medium transition',
                            'bg-white text-ink shadow-sm' => request()->routeIs('admin.posts.*'),
                            'text-stone-600 hover:bg-white/70 hover:text-ink' => ! request()->routeIs('admin.posts.*'),
                        ])
                    >
                        Posts
                    </a>
                    <a href="{{ route('blog.index') }}" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 transition hover:bg-white/70 hover:text-ink">
                        View blog
                    </a>
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-full border border-rule bg-white px-3 py-2 text-sm font-medium text-ink transition hover:border-stone-400 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-paper">
                            <span class="grid size-8 place-items-center rounded-full bg-accent/10 text-xs font-semibold uppercase text-accent">
                                {{ \Illuminate\Support\Str::substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                            <svg class="size-4 text-stone-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center gap-2 sm:hidden">
                <span class="max-w-24 truncate text-sm font-medium text-stone-600">{{ Auth::user()->name }}</span>
                <button
                    type="button"
                    @click="open = ! open"
                    :aria-expanded="open.toString()"
                    aria-label="Toggle navigation"
                    class="grid size-10 shrink-0 place-items-center rounded-full border border-rule bg-white text-ink transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent"
                >
                    <svg x-show="! open" class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg x-show="open" x-cloak class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="open" x-transition class="border-t border-rule bg-white px-4 py-4 shadow-lg sm:hidden">
        <div class="mx-auto grid max-w-7xl gap-1">
            <a href="{{ route('admin.posts.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-ink transition hover:bg-paper">Posts</a>
            <a href="{{ route('blog.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-ink transition hover:bg-paper">View blog</a>
            <a href="{{ route('profile.edit') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-ink transition hover:bg-paper">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-rule pt-2">
                @csrf
                <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-accent transition hover:bg-paper">
                    Log out
                </button>
            </form>
        </div>
    </div>
</nav>
