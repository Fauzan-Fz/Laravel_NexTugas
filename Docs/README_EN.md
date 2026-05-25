# NexTugas – Technical Documentation

## 1. Project Overview

**NexTugas** is an Intelligent Task Management System built with modern architecture:

- **Framework**: Laravel 13 with AI Native capabilities
- **Database**: 64-bit Snowflake ID generation for distributed uniqueness
- **AI Integration**: OpenRouter API for intelligent task breakdown
- **UI Design**: Premium minimalist matte interface with dark mode support
- **Architecture**: Asynchronous queue processing for seamless UX

### Key Features

- **Snowflake IDs**: Distributed 64-bit unique identifiers (timestamp + worker + sequence)
- **AI Task Breakdown**: Automatically generates sub-tasks using OpenRouter AI
- **Real-time UI**: Alpine.js + Tailwind CSS for reactive components
- **Queue Processing**: Background workers prevent frontend freezing
- **Multi-theme**: Light/dark mode with matte design system

---

## 2. Environment Configuration

### OpenRouter API Key Setup

To enable AI features, add your OpenRouter API key to the `.env` file:

```env
OPENROUTER_API_KEY=your_actual_api_key_here
```

**How to obtain an API key:**
1. Visit [openrouter.ai](https://openrouter.ai)
2. Create an account or sign in
3. Navigate to **Keys** section
4. Generate a new API key
5. Copy and paste into your `.env` file

**Security note**: Never commit API keys to version control. The `.env` file is already included in `.gitignore`.

---

## 3. Database & Core Commands

### Command 1: Database Setup

```bash
php artisan migrate:fresh --seed
```

**What this command does:**

1. **Drops all existing tables** – Cleans the database completely
2. **Runs migrations** – Creates tables with Snowflake ID schema:
   - `users`: 64-bit unsigned primary key (no auto-increment)
   - `tasks`: 64-bit unsigned primary key + foreign key to users
   - `task_steps`: 64-bit unsigned primary key + foreign key to tasks
3. **Seeds test data** – Creates default testing account:
   - **User**: `Fauzan Firdaus`
   - **Email**: `fauzan@rpl.com`
   - **Password**: `password123`
   - **Tasks**: 6 sample RPL school assignments

**Why Snowflake IDs?**
- **64-bit integers** support 69 years of unique IDs
- **Distributed generation** – no database coordination needed
- **Time-ordered** – IDs sort chronologically
- **No collisions** – unique across multiple servers/workers

### Command 2: Queue Worker

```bash
php artisan queue:work
```

**What this command does:**

Runs a background worker process that listens for and executes queued jobs asynchronously.

**Why this is MANDATORY:**

| Feature | Without Queue Worker | With Queue Worker |
|---------|---------------------|-------------------|
| **AI Task Breakdown** | Frontend freezes during API call | Instant response, AI processes in background |
| **User Experience** | 3-5 second lag | Immediate feedback |
| **Error Handling** | Failures block the user | Retries happen automatically |
| **Scalability** | Single-threaded | Multiple workers possible |

**How the AI Center works:**

1. User creates a task in the UI
2. Task is saved to database immediately
3. `GenerateTaskStepsJob` is dispatched to queue
4. Queue worker picks up the job in background
5. OpenRouter API generates sub-tasks
6. Task steps are saved to `task_steps` table
7. User sees results on next page refresh

**Running the worker:**

Open a **separate terminal** and keep this command running:

```bash
php artisan queue:work --timeout=60
```

For production environments, use Supervisor or systemd to keep the worker running permanently.

---

## 4. System Architecture

```
┌─────────────────┐     ┌──────────────┐     ┌─────────────────┐
│   User Browser  │────▶│  Laravel App │────▶│   MySQL DB      │
│  (Alpine.js)    │     │   (PHP 8.4)  │     │ (Snowflake IDs) │
└─────────────────┘     └──────────────┘     └─────────────────┘
                               │
                               ▼
                        ┌──────────────┐
                        │  Queue Worker│
                        │  (Background)│
                        └──────────────┘
                               │
                               ▼
                        ┌──────────────┐
                        │  OpenRouter  │
                        │   AI API     │
                        └──────────────┘
```

---

## 5. File Structure

```
laravel_new_my_kisah/
├── app/
│   ├── Traits/
│   │   └── HasSnowflake.php          # 64-bit ID generator
│   ├── Models/
│   │   ├── User.php                  # HasSnowflake trait
│   │   ├── Task.php                  # HasSnowflake trait
│   │   └── TaskStep.php              # HasSnowflake trait
│   ├── Jobs/
│   │   └── GenerateTaskStepsJob.php  # AI processing job
│   └── Services/
│       └── OpenRouterService.php     # AI API client
├── database/
│   ├── migrations/                   # Snowflake schema
│   └── seeders/
│       └── TaskSeeder.php            # Test data
└── docs/
    ├── README_EN.md                  # This file
    └── README_ID.md                  # Indonesian version
```

---

## 6. Quick Start Checklist

- [ ] Copy `.env.example` to `.env`
- [ ] Add `OPENROUTER_API_KEY` to `.env`
- [ ] Run `php artisan migrate:fresh --seed`
- [ ] Open terminal 1: `php artisan serve`
- [ ] Open terminal 2: `php artisan queue:work`
- [ ] Access `http://localhost:8000`
- [ ] Login with `fauzan@rpl.com` / `password123`

---

## 7. Troubleshooting

### Issue: AI not generating steps
**Solution**: Ensure `php artisan queue:work` is running in a separate terminal.

### Issue: Snowflake ID errors
**Solution**: Run `php artisan migrate:fresh --seed` to rebuild with correct schema.

### Issue: API key not working
**Solution**: Verify `OPENROUTER_API_KEY` is set in `.env` and not cached. Run `php artisan config:clear`.

---

**Version**: 1.0.0  
**Last Updated**: May 2026  
**Author**: XI RPL
