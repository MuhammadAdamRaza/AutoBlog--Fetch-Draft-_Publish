@csrf

<div class="space-y-6">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-ink">Title</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $post->title) }}"
            required
            maxlength="255"
            class="w-full rounded-xl border-rule bg-white px-4 py-3 text-ink shadow-sm focus:border-accent focus:ring-accent"
        >
        @error('title')
            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="excerpt" class="mb-2 block text-sm font-medium text-ink">Excerpt</label>
        <textarea
            id="excerpt"
            name="excerpt"
            rows="3"
            maxlength="500"
            class="w-full rounded-xl border-rule bg-white px-4 py-3 text-ink shadow-sm focus:border-accent focus:ring-accent"
        >{{ old('excerpt', $post->excerpt) }}</textarea>
        @error('excerpt')
            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="meta_description" class="mb-2 block text-sm font-medium text-ink">SEO description</label>
        <input
            id="meta_description"
            name="meta_description"
            type="text"
            value="{{ old('meta_description', $post->meta_description) }}"
            maxlength="160"
            class="w-full rounded-xl border-rule bg-white px-4 py-3 text-ink shadow-sm focus:border-accent focus:ring-accent"
        >
        @error('meta_description')
            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="body" class="mb-2 block text-sm font-medium text-ink">Article</label>
        <textarea
            id="body"
            name="body"
            rows="18"
            required
            class="w-full rounded-xl border-rule bg-white px-4 py-3 font-serif leading-7 text-ink shadow-sm focus:border-accent focus:ring-accent"
        >{{ old('body', $post->body) }}</textarea>
        @error('body')
            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button
            type="submit"
            class="rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
        >
            {{ $post->exists ? 'Save changes' : 'Save draft' }}
        </button>
        <a
            href="{{ route('admin.posts.index') }}"
            class="rounded-full border border-rule px-5 py-3 text-sm font-semibold text-ink transition hover:bg-paper"
        >
            Cancel
        </a>
    </div>
</div>
