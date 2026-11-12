@csrf

<div class="space-y-5">
    <div class="grid gap-5 sm:grid-cols-3">
        <div class="sm:col-span-2">
            <label for="title" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Title</label>
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $post->title) }}"
                required
                maxlength="255"
                class="w-full rounded-md border-rule bg-paper px-3.5 py-2.5 text-sm text-ink focus:border-accent focus:ring-accent"
            >
            @error('title')
                <p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Category</label>
            <select
                id="category"
                name="category"
                class="w-full rounded-md border-rule bg-paper px-3 py-2.5 text-sm text-ink focus:border-accent focus:ring-accent"
            >
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" @selected(old('category', $post->category ?: 'Tech') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
            @error('category')
                <p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="excerpt" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Excerpt (2-line summary)</label>
        <textarea
            id="excerpt"
            name="excerpt"
            rows="2"
            maxlength="500"
            class="w-full rounded-md border-rule bg-paper px-3.5 py-2.5 text-sm text-ink focus:border-accent focus:ring-accent"
        >{{ old('excerpt', $post->excerpt) }}</textarea>
        @error('excerpt')
            <p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="meta_description" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">SEO Meta Description</label>
            <input
                id="meta_description"
                name="meta_description"
                type="text"
                value="{{ old('meta_description', $post->meta_description) }}"
                maxlength="160"
                class="w-full rounded-md border-rule bg-paper px-3.5 py-2.5 text-sm text-ink focus:border-accent focus:ring-accent"
            >
            @error('meta_description')
                <p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="cover_image" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Cover Image URL (optional)</label>
            <input
                id="cover_image"
                name="cover_image"
                type="url"
                value="{{ old('cover_image', $post->cover_image) }}"
                placeholder="https://..."
                maxlength="500"
                class="w-full rounded-md border-rule bg-paper px-3.5 py-2.5 text-sm text-ink focus:border-accent focus:ring-accent"
            >
            @error('cover_image')
                <p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="body" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Article Body</label>
        <textarea
            id="body"
            name="body"
            rows="18"
            required
            class="w-full rounded-md border-rule bg-paper px-4 py-3 font-serif text-base leading-7 text-ink focus:border-accent focus:ring-accent"
        >{{ old('body', $post->body) }}</textarea>
        @error('body')
            <p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center gap-3 border-t border-rule pt-4">
        <button
            type="submit"
            class="rounded-md bg-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
        >
            {{ $post->exists ? 'Save changes' : 'Save draft' }}
        </button>
        <a
            href="{{ route('admin.posts.index') }}"
            class="rounded-md border border-rule bg-white px-5 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper"
        >
            Cancel
        </a>
    </div>
</div>
