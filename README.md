# QuizGo

Students turn a topic, pasted text or a PDF into AI-generated flashcard decks and study them three ways (flip cards, multiple choice, identification). Teachers run classrooms, write or AI-generate quizzes, assign them, and see proctored results and rankings. Admins create teacher accounts.

**Stack:** Laravel 13 (PHP 8.3+), MySQL, Blade + Alpine.js + Tailwind (Vite), Laravel Reverb for real-time notifications, Google Gemini for generation, poppler + Tesseract for reading PDFs.

## Local setup

Requirements: PHP 8.3+, Composer, Node 20+, MySQL, and for PDF uploads `poppler-utils` (`pdftotext`, `pdftoppm`) and `tesseract` on your PATH.

```bash
composer setup                 # install, copy .env, generate key, migrate, build assets
```

Then fill in `.env`:

| Setting | What it's for |
|---|---|
| `DB_*` | MySQL connection |
| `GEMINI_API_KEY` | Flashcard and quiz generation |
| `REVERB_*` | Real-time "your deck/quiz is ready" toasts |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | The admin account created by `php artisan db:seed` |
| `UPLOADS_DISK` | Where uploads wait for the queue worker; use a shared disk (e.g. `s3`) if the worker runs in a separate container |

Create the admin, then run everything (web server, queue worker, logs, Vite):

```bash
php artisan db:seed
composer dev
php artisan reverb:start       # in another terminal, for real-time notifications
```

Students sign up at `/signup`. Teachers sign in at `/teacher/signin` with accounts the admin creates.

## Tests

```bash
composer test
```

Tests run on SQLite in memory and fake the queue, Gemini and storage where needed.

## How the code is organised

| Where | What |
|---|---|
| `routes/web.php` | All routes, grouped by role (`role:student`, `role:teacher`, `role:admin` middleware) |
| `app/Http/Controllers` | Thin controllers: validate, authorize, call a service, respond |
| `app/Http/Requests` | Form Requests for the larger forms (quiz creation, AI generation, profile) |
| `app/Policies` | Who may view or change each model (decks, quizzes, classrooms, attempts…) |
| `app/Services` | Business logic: grading, rankings, quiz creation, generation dispatch, Gemini prompts, PDF text extraction |
| `app/Jobs` | `GenerateFlashcards` and `GenerateQuiz`, both built on `GenerationJob` |
| `app/Models/Generation.php` | Progress of every background AI generation; polled at `/api/generations/{uuid}` |
| `resources/js` | Alpine setup, Echo listeners, and the quiz-taking proctoring/timer (`quiz-take.js`) |

Quiz timing is enforced on the server: an attempt starts when the quiz page opens, and answers that arrive after the time limit or due date (plus a 60-second grace period) score zero.

## Deploying

- Run `php artisan migrate --force` on every release.
- Run a queue worker (`php artisan queue:work`); AI generation only happens there.
- Run `php artisan reverb:start` (or point `REVERB_*` at a hosted Reverb/Pusher) for real-time notifications.
- Empty the queue before deploying a release that renames or changes job classes.
