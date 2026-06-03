# MCCP — Multi-Channel Content Processor

Full-stack application that creates messages, generates AI-powered summaries via **Google Gemini**, and distributes them asynchronously through **Email**, **Slack**, and **SMS** channels.

**Stack:** Laravel 12 (PHP 8.4) · React 19 (Vite) · PostgreSQL 16 · Nginx · Docker Compose

---

## Architecture

Simplified Clean Architecture with clear separation of concerns:

```
Actions → Services → Integrations → Models
```

### Design Patterns

| Pattern | Where |
|---------|-------|
| **Strategy** | `ChannelInterface` + `EmailChannel`, `SlackChannel`, `SmsChannel` |
| **DTO** | `MessageDTO`, `ChannelResultDTO` for typed data exchange |
| **Queue Job** | `ProcessMessageChannelsJob` for async channel processing |

### Flow

```
User (React)
  │ POST /api/messages { title, content, channels }
  ▼
CreateMessageAction
  ├─► AiSummaryService → GeminiClient
  │     ├─ Success: summary (≤100 chars)
  │     └─ Failure: return 422, no channels processed
  ├─► Message::create() → saves to DB
  └─► ProcessMessageChannelsJob::dispatch()
         ├─► EmailChannel → simulated (laravel.log)
         ├─► SlackChannel → real POST to webhook
         └─► SmsChannel   → simulated SOAP XML (laravel.log)
                  └─► DeliveryLog per channel (success/failure)
```

### Folder Structure

```
backend/
├── app/
│   ├── Actions/              → CreateMessageAction.php
│   ├── Contracts/            → ChannelInterface.php
│   ├── DTOs/                 → MessageDTO.php, ChannelResultDTO.php
│   ├── Factories/            → ChannelFactory.php
│   ├── Integrations/
│   │   ├── Gemini/           → GeminiClient.php
│   │   └── Channels/         → EmailChannel, SlackChannel, SmsChannel
│   ├── Jobs/                 → ProcessMessageChannelsJob.php
│   ├── Services/             → AiSummaryService.php, ChannelProcessorService.php
│   ├── Http/                 → Controllers, Requests
│   └── Models/               → Message.php, DeliveryLog.php
├── config/
├── database/migrations/
├── routes/                   → api.php
└── tests/
frontend/
└── src/
    ├── api.js                → Axios instance
    ├── pages/
    │   ├── SendMessage.jsx   → Form (title, content, channels)
    │   └── History.jsx       → Table (date, title, summary, status)
    └── styles/
        ├── App.styles.js
        ├── SendMessage.styles.js
        └── History.styles.js
```

---

## Quick Start

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/) (v2 or standalone)

### Setup

```bash
# 1. Clone and enter the project
git clone <repo-url> mccp
cd mccp

# 2. Copy environment file (already exists, but verify)
cp backend/.env.example backend/.env

# 3. Configure API keys
# Set your Gemini API key and Slack webhook URL as environment variables:
```

**Windows (PowerShell):**
```powershell
$env:GEMINI_API_KEY="your-gemini-api-key"
$env:SLACK_WEBHOOK_URL="https://webhook.site/your-unique-id"
docker compose up -d
```

**Linux/macOS:**
```bash
export GEMINI_API_KEY="your-gemini-api-key"
export SLACK_WEBHOOK_URL="https://webhook.site/your-unique-id"
docker compose up -d
```

Or create a `.env` file in the project root with:

```env
GEMINI_API_KEY=your-gemini-api-key
SLACK_WEBHOOK_URL=https://webhook.site/your-unique-id
```

Then run:

```bash
docker compose up -d
```

### Access

| Service | URL |
|---------|-----|
| **Frontend** | http://localhost:8080 |
| **API** | http://localhost:8080/api |
| **Health** | http://localhost:8080/api/health |

---

## Environment Variables

| Variable | Default | Required |
|----------|---------|----------|
| `GEMINI_ENABLED` | `true` | ❌ Set to `false` to bypass AI and test channels without API key |
| `GEMINI_API_KEY` | — | ✅ AI summaries (Google Gemini) |
| `GEMINI_MODEL` | `gemini-2.0-flash-lite` | ❌ |
| `SLACK_WEBHOOK_URL` | — | ⬜ Slack channel (optional, webhook.site for testing) |
| `DB_DATABASE` | `mccp_db` | ❌ |
| `DB_USERNAME` | `mccp_user` | ❌ |
| `DB_PASSWORD` | `mccp_password` | ❌ |

---

## API Endpoints

| Method | Path | Purpose |
|--------|------|---------|
| `GET` | `/api/health` | Health check with DB connection status |
| `POST` | `/api/messages` | Create message → AI summarize → dispatch job |
| `GET` | `/api/messages` | List messages with delivery logs |

### POST /api/messages

```json
{
  "title": "My Title",
  "content": "Full message content to be summarized by AI...",
  "channels": ["email", "slack", "sms"]
}
```

**Success (201):**
```json
{
  "message": "Message created successfully",
  "data": {
    "id": 1,
    "title": "My Title",
    "summary": "AI-generated summary...",
    "status": "pending",
    "delivery_logs": []
  }
}
```

**AI Failure (422):**
```json
{
  "message": "AI processing failed",
  "error": "Gemini API error: ..."
}
```

---

## Running Tests

```bash
# All tests (PHPUnit with SQLite in-memory)
docker compose exec php php artisan test

# Single test class
docker compose exec php php artisan test --filter=MessageTest

# With coverage report
docker compose exec php php artisan test --coverage
```

Tests mock external services:
- **Gemini API** → `Http::fake()` intercepts `generativelanguage.googleapis.com/*`
- **Channels** → Mocked via `Mockery` in unit tests

---

## Channel Behaviors

| Channel | Type | Behavior |
|---------|------|----------|
| **Email** | Simulated | Logs payload to `storage/logs/laravel.log` |
| **Slack** | Real HTTP POST | Sends JSON to `SLACK_WEBHOOK_URL` (webhook.site / Beeceptor) |
| **SMS** | Simulated (SOAP) | Generates XML, logs to `storage/logs/laravel.log` |

---

## Screenshots

> _Add screenshots here after testing with your Gemini API key._

| Screen | Preview |
|--------|---------|
| **Send Message** | `docs/screenshots/send-message.png` |
| **History Dashboard** | `docs/screenshots/history.png` |
| **Slack Webhook** | `docs/screenshots/slack-webhook.png` |
| **Email Log** | `docs/screenshots/email-log.png` |
| **SMS SOAP Log** | `docs/screenshots/sms-log.png` |

---

## Viewing Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f php
docker compose logs -f queue
docker compose logs -f frontend

# Email log entry
docker compose exec php tail storage/logs/laravel.log | grep EmailChannel

# Slack log entry
docker compose exec php tail storage/logs/laravel.log | grep SlackChannel

# SMS SOAP log entry
docker compose exec php tail storage/logs/laravel.log | grep SmsChannel
```

---

## Useful Commands

```bash
# Enter the PHP container
docker compose exec php bash

# Run migrations
docker compose exec php php artisan migrate

# Reset database
docker compose exec php php artisan migrate:fresh

# Watch queue worker logs
docker compose logs -f queue

# Rebuild containers after code changes
docker compose build php queue
docker compose up -d
```

---

## License

MIT
