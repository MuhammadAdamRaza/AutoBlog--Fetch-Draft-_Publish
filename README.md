# AutoBlog: Fetch, Draft & Publish

AutoBlog is a Laravel 13 editorial publishing platform with a clean, reader-first public journal and an authenticated admin control room that turns external source URLs into structured, editable blog drafts using OpenRouter LLMs.

---

## Key Features

### Public Journal (`/blog`)
- **Editorial Design System**: Warm off-white background (`#FAF9F6`), crisp white cards (`#FFFFFF`) with thin `1px` borders (`#E5E7EB`), charcoal typography (`#111827`), deep forest green accents (`#065F46`), and `Newsreader` + `Figtree` fonts.
- **Sticky Navigation & Search Modal**: Category navigation, instant search modal (`?q=...`), and one-click RSS feed URL copy in the header.
- **Hero & 3-Column Feed**: 60/40 split featured story at the top, topic filter strip (`All Posts`, `Latest`, `Tutorials`, `News`, `Saved`), and responsive 3-column article grid.
- **Interactive Reader Sections**:
  - **Saved Reading List**: Bookmark articles directly in the browser (`localStorage`) and filter by `Saved`.
  - **Quick Story Preview Modal**: Inspect a story's excerpt, category, reading time, and publish date without leaving the homepage.
  - **Reading Pace Explorer**: Switch between **Quick Reads (≤ 3 min)** and **Deep Dives (4+ min)** alongside a direct RSS subscribe box.
- **Focused Article View (`/blog/{slug}`)**: Centered `680px` reading column, reading-time estimate, **Source Reference** attribution link, JSON-LD structured data, and category-matched **Related Posts**.
- **Syndication & SEO**: `/feed`, `/rss.xml`, `/feeds`, and `/sitemap.xml`.

### Admin Control Room (`/admin/posts`)
- **URL-to-Draft Importer**: Paste any source URL and select **Category** (`Tech`, `AI`, `Business`, `Guides`, `Tutorials`, `News`), **OpenRouter Model** (`Claude 3.5 Sonnet`, `GPT-4o Mini`, `GPT-4o`, `Llama 3.1 70B`), and **Tone** (`Informative`, `Casual`, `News`, `Technical`).
- **Queue & Fetch Activity Log**: Asynchronous `GenerateDraftFromUrl` job tracks `PostFetchLog` status (`queued`, `processing`, `success`, `failed`) with one-click **Retry** for failed fetches.
- **Inline Category Switching**: Change or assign a post's category directly from the dropdown in the admin posts table before or after publishing.
- **40/60 Split-Screen Editor**: Review the read-only extracted source text (`source_body`) on the left while editing title, category, slug, cover image URL, excerpt, meta description, and article body on the right.

---

## Tech Stack

- **Backend**: PHP `^8.3`, Laravel `^13.17`, Laravel Breeze (authentication), Symfony DomCrawler / CSS Selector (HTML extraction)
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Vite
- **AI Drafting**: OpenRouter Chat Completions API (`App\Services\OpenRouterDraftWriter`)
- **Testing & Code Style**: Pest `^4.7`, Laravel Pint

---

## Local Setup

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your database settings and OpenRouter credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=autoblog
   DB_USERNAME=root
   DB_PASSWORD=

   QUEUE_CONNECTION=database

   OPENROUTER_API_KEY=your-openrouter-api-key
   OPENROUTER_API_URL=https://openrouter.ai/api/v1/chat/completions
   OPENROUTER_DEFAULT_MODEL=openai/gpt-4o-mini
   ```

3. **Run database migrations**
   ```bash
   php artisan migrate
   ```

4. **Build frontend assets**
   ```bash
   npm run build
   ```
   *(Or run `npm run dev` during active development.)*

5. **Start the application and queue worker**
   ```bash
   php artisan serve
   php artisan queue:work
   ```
   *(You can also use `composer run dev` where configured.)*

---

## Main Routes

| Area | Method & Path | Description |
| --- | --- | --- |
| Public | `GET /` → `/blog` | Public journal homepage (filterable by `category` and `q`) |
| Public | `GET /blog/{post}` | Single article reading view |
| Public | `GET /feeds` | RSS & sitemap overview page |
| Public | `GET /feed`, `GET /rss.xml` | RSS 2.0 feed (latest 20 published posts) |
| Public | `GET /sitemap.xml` | XML sitemap of published posts |
| Admin (`auth`) | `GET /admin/posts` | Control room with URL importer, queue logs, and posts table |
| Admin (`auth`) | `POST /admin/posts/fetch` | Dispatch `GenerateDraftFromUrl` job for a URL |
| Admin (`auth`) | `POST /admin/fetch-logs/{log}/retry` | Retry a failed URL fetch job |
| Admin (`auth`) | `GET /admin/posts/{post}/edit` | 40/60 split-screen editor with extracted source text |
| Admin (`auth`) | `PATCH /admin/posts/{post}/category` | Update post category inline from the dashboard |
| Admin (`auth`) | `POST /admin/posts/{post}/publish` | Publish a draft post |
| Admin (`auth`) | `POST /admin/posts/{post}/unpublish` | Move a published post back to draft |

---

## Testing & Code Style

Run the feature test suite with Pest:
```bash
vendor/bin/pest tests/Feature/AutoBlogTest.php
```
*(Note: `tests/Feature/AutoBlogTest.php` includes a production asset URL check that expects `public/hot` not to be present from an active `npm run dev` server.)*

Format modified PHP files with Laravel Pint:
```bash
vendor/bin/pint
```

---

## Key Project Files

- `app/Http/Controllers/BlogController.php` — Public journal listing, search, category filtering, article view, and feeds page
- `app/Http/Controllers/Admin/PostController.php` — Admin CRUD, URL fetch dispatch, retry, inline category updates, publish/unpublish
- `app/Services/ArticleFetcher.php` — HTML fetching and text extraction from external source URLs
- `app/Services/OpenRouterDraftWriter.php` — Structured editorial draft generation via OpenRouter
- `app/Jobs/GenerateDraftFromUrl.php` — Background job coordinating URL extraction, AI drafting, and `PostFetchLog` updates
- `app/Models/Post.php` & `app/Models/PostFetchLog.php` — Core models, categories, reading-time calculation, and fetch logs
- `resources/views/blog/` & `resources/views/admin/posts/` — Public reader views and admin control room views
