<nav x-data="{ open: false }" class="relative z-10 h-16 border-b border-rule bg-white">
    <div class="mx-auto flex h-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-6">
            <a
                href="{{ route('admin.posts.index') }}"
                class="flex items-center gap-2.5 font-serif text-lg font-bold tracking-tight text-ink"
            >
                <span class="grid size-8 place-items-center rounded bg-accent font-sans text-xs font-bold text-white" aria-hidden="true">A</span>
                <span class="truncate">{{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}</span>
            </a>

            <div class="hidden items-center gap-1 sm:flex">
                <a
                    href="{{ route('admin.posts.index') }}"
                    @class([
                        'rounded-md px-3 py-1.5 text-sm font-medium transition',
                        'bg-paper text-ink border border-rule' => request()->routeIs('admin.posts.*'),
                        'text-gray-600 hover:bg-paper hover:text-ink' => ! request()->routeIs('admin.posts.*'),
                    ])
                >
                    Posts
                </a>
                <a href="{{ route('blog.index') }}" class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-paper hover:text-ink">
                    View blog
                </a>
                <a href="{{ route('feed') }}" class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-paper hover:text-ink">
                    RSS Feed
                </a>
            </div>
        </div>

        <div class="hidden items-center gap-3 sm:flex">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-2.5 rounded-md border border-rule bg-paper px-3 py-1.5 text-sm font-medium text-ink transition hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-accent">
                        <span class="grid size-6 place-items-center rounded bg-accent/10 text-xs font-bold uppercase text-accent">
                            {{ \Illuminate\Support\Str::substr(Auth::user()->name, 0, 1) }}
                        </span>
                        <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                        <svg class="size-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
            <span class="max-w-24 truncate text-sm font-medium text-gray-600">{{ Auth::user()->name }}</span>
            <button
                type="button"
                @click="open = ! open"
                :aria-expanded="open.toString()"
                aria-label="Toggle navigation"
                class="grid size-9 shrink-0 place-items-center rounded-md border border-rule bg-paper text-ink transition hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-accent"
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

    <div x-cloak x-show="open" x-transition class="border-t border-rule bg-white px-4 py-4 sm:hidden">
        <div class="mx-auto grid max-w-7xl gap-1">
            <a href="{{ route('admin.posts.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-ink hover:bg-paper">Posts</a>
            <a href="{{ route('blog.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-ink hover:bg-paper">View blog</a>
            <a href="{{ route('profile.edit') }}" class="rounded-md px-3 py-2 text-sm font-medium text-ink hover:bg-paper">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-rule pt-2">
                @csrf
                <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-accent hover:bg-paper">
                    Log out
                </button>
            </form>
        </div>
    </div>
</nav>
