# MCCP — AGENTS.md

## Project

Multi-Channel Content Processor (MCCP). Full-stack technical test: create messages, AI-summarize via OpenAI, distribute to Email/Slack/SMS channels async.

**Stack:** Laravel 12 (PHP 8.4) / React 19 (Vite) / PostgreSQL 16 / Nginx / Docker Compose

## Current state

**Fase 1** (Docker + boilerplate), **Fase 2** (Backend Laravel), and **Fase 3** (Frontend React) are complete.

The full architecture is implemented:
- `app/Actions/CreateMessageAction.php` — orchestrates AI → save → dispatch job
- `app/Contracts/ChannelInterface.php` — strategy contract for channels
- `app/DTOs/` — `MessageDTO`, `ChannelResultDTO`
- `app/Factories/ChannelFactory.php` — resolves channel by string name
- `app/Integrations/` — `OpenAI/OpenAIClient`, `Channels/{Email,Slack,Sms}Channel`
- `app/Jobs/ProcessMessageChannelsJob.php` — async queue job
- `app/Services/` — `AiSummaryService`, `ChannelProcessorService`
- `app/Models/` — `Message` (hasMany DeliveryLog), `DeliveryLog` (belongsTo Message)
- `app/Http/Controllers/ApiController.php` — store + index
- `app/Http/Requests/StoreMessageRequest.php` — validation
- `routes/api.php` — API routes (loaded via `bootstrap/app.php`)
- `database/migrations/` — `messages` and `delivery_logs` tables

Fases 4–6 are pending.

## Architecture plan (simplified Clean Architecture)

See `docs/plan-de-trabajo.md` for the full plan and `docs/contexto.md` for requirements.

Key patterns:
- **Strategy Pattern** for channels (`ChannelInterface`, `EmailChannel`, `SlackChannel`, `SmsChannel`, `ChannelFactory`)
- **DTOs** (`MessageDTO`, `ChannelResultDTO`) instead of raw arrays
- **Laravel Jobs** (database queue, no Redis) for async channel processing
- No CQRS, Event Sourcing, Repository Pattern, or Hexagonal Architecture

## Docker dev environment

```yaml
# docker-compose.yml — 5 services on shared mccp_network
nginx    :8080 → proxies /api → php:9000, / → frontend:5173
php      :9000  Laravel FPM (auto-migrates on start via docker-entrypoint.sh)
queue           php artisan queue:work --sleep=3 --tries=3 --timeout=90
postgres :5432  PostgreSQL 16 with healthcheck
frontend :5173  Vite dev server (hot reload with polling)
```

**Start:** `docker-compose up -d`
**Stop:** `docker-compose down`
**View logs:** `docker-compose logs -f <service>`
**Run artisan in container:** `docker-compose exec php php artisan <cmd>`
**Access:** http://localhost:8080

## Key commands (inside backend container or locally)

| Command | What it does |
|---------|-------------|
| `composer setup` | Full initial setup (composer install, .env, key:generate, migrate, npm install + build) |
| `composer dev` | Runs all dev servers concurrently: Laravel serve + queue worker + logs (pail) + Vite |
| `composer test` | Runs PHPUnit (`artisan config:clear && artisan test`) |
| `php artisan test` | Run tests directly |
| `php artisan test --filter=SomeTest` | Single test class |
| `php artisan migrate` | Run pending migrations |
| `php artisan queue:listen --tries=1 --timeout=0` | Run queue worker in foreground |

## Tests

- **PHPUnit** with SQLite in-memory (`phpunit.xml` sets `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
- Tests live in `tests/Feature/` and `tests/Unit/`, namespaced `Tests\`
- No frontend test framework configured yet (no Jest, no Vitest)
- Mock external services (OpenAI, Slack webhook) — do not call real APIs in tests

## API endpoints

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/health` | Health check (already implemented in `ApiDocController`) |
| POST | `/api/messages` | Create message, AI-summarize, dispatch channel job |
| GET | `/api/messages` | List message history with delivery_logs |

## Frontend conventions

- No router library — simple `useState`-based page switching in `App.jsx`
- No state management library — `useState` only
- No CSS framework — `index.css` CSS variables + inline `style` objects
- Axios instance in `src/api.js` with base URL from `import.meta.env.VITE_API_URL`
- Vite proxy: `/api` → `http://nginx:80` during dev (in Docker)

## Environment variables

| Variable | Default | Required for |
|----------|---------|-------------|
| `OPENAI_API_KEY` | — | AI summary generation |
| `OPENAI_MODEL` | `gpt-4o-mini` | OpenAI model |
| `SLACK_WEBHOOK_URL` | — | Slack channel POST |
| `VITE_API_URL` | `http://localhost:8080/api` | Frontend Axios base URL |

Copy `.env.example` to `.env` and fill secrets.

## Repo structure

```
/
├── backend/          Laravel 12 app (PHP 8.4)
│   ├── app/          Http/, Models/, Providers/ (+ planned: Actions, Contracts, DTOs, Factories, Integrations, Jobs, Services)
│   ├── config/
│   ├── database/     migrations, factories, seeders
│   ├── routes/       web.php (API routing goes here — no api.php file)
│   └── tests/        Feature/, Unit/
├── frontend/         React 19 / Vite
│   └── src/          main.jsx, App.jsx, api.js, pages/ (SendMessage, History)
├── nginx/conf.d/     default.conf
└── docker-compose.yml
```

## Notes

- `routes/web.php` handles all API routes (Laravel 12 merged `api.php` into `web.php` under `/api` prefix; verify current approach)
- Queue driver is `database` — the `jobs` table migration already exists
- `docs/` is gitignored (contains PDFs and planning notes)
- `.env` is gitignored; `.env.example` has defaults for DB and placeholder values for API keys
- The `docker-entrypoint.sh` auto-installs Laravel, runs `composer install`, generates APP_KEY, runs migrations on container start
- Frontend `vite.config.js` uses `usePolling: true` for hot reload in Docker (Windows/WSL compat)
