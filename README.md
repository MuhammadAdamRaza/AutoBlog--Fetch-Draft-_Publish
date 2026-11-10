# AutoBlog: Fetch, Draft & Publish

AutoBlog is a Laravel application for collecting public article sources, preparing editable AI-assisted drafts, and publishing reviewed posts to a public blog.

## Features

- Public blog with search, article pages, related posts, reading time, and share links
- Authenticated post management with draft, edit, publish, and unpublish actions
- Fetch an article from a public URL and prepare an editable draft with OpenRouter
- RSS feed at `/rss.xml` and sitemap at `/sitemap.xml`
- Database-backed queue for article fetching

## Requirements

- PHP 8.3 or later with the SQLite and required Laravel extensions
- Composer
- Node.js and npm
- An OpenRouter API key to generate AI drafts

## Local setup (Windows PowerShell)

Open PowerShell in this project folder and run:

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File -Path database\database.sqlite
php artisan key:generate
php artisan migrate
npm ci
npm run build
```

Open `.env` and add your OpenRouter key:

```dotenv
OPENROUTER_API_KEY=your_openrouter_api_key
OPENROUTER_DEFAULT_MODEL=openai/gpt-4o-mini
```

Keep the API key private. Never upload `.env` or paste a real API key into GitHub.

Start the web application:

```powershell
php artisan serve
```

In a second PowerShell window, from the same project folder, start the queue worker:

```powershell
php artisan queue:work
```

Open `http://127.0.0.1:8000`. Register an account through the app to sign in, then manage posts at `/admin/posts`.

## Fetching an article

1. Sign in and open the admin posts page.
2. Paste a public article URL into **Fetch a source URL** and submit it.
3. Keep `php artisan queue:work` running while the article is processed.
4. Open the resulting draft, review and edit it, then publish when ready.

The source site must be publicly reachable. OpenRouter must accept the selected model request and the account must have sufficient API credits. A remaining usage percentage does not necessarily mean API credits are available. If OpenRouter rejects a request, check its account credits, model access, and request activity.

## Tests

```powershell
php artisan test
```

## Uploading to GitHub

Create an empty repository on GitHub, then run these commands from this project folder (replace the URL with your repository URL):

```powershell
git init
git add .
git commit -m "Initial AutoBlog project"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
git push -u origin main
```

The `.gitignore` excludes `.env`, dependencies, and generated build output. Commit source code and lockfiles, not API keys, local databases, or log files.
