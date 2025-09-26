Financas — Laravel 11 Multi‑Tenant Finance App

Overview
- Laravel 11, PHP 8.2+, MySQL, Redis (optional), Docker.
- Multi‑tenant por workspace (tenant) com RBAC simples (OWNER, ADMIN, MEMBER).
- Contas separadas e conjuntas, cartões de crédito, faturas, recorrências, metas, relatórios e importação CSV.
- API autenticada com Sanctum; Swagger em `/api/docs`.

Quick Start (Docker)
- Copy `.env.example` to `.env` and set `APP_KEY` (or run `php artisan key:generate`).
- Run: `docker compose up -d --build`
- Enter container and install vendors if not already: `docker compose exec app composer install`
- Migrate + seed: `docker compose exec app php artisan migrate --seed`
- Queue worker: `docker compose exec app php artisan queue:work`
- Scheduler (optional if using system cron): `docker compose exec app php artisan schedule:work`
- App: `http://localhost` • API: `http://localhost/api` • Docs: `http://localhost/api/docs`

.env
- The repo `.env` is set with the values you provided for local/dev.
- `.env.example` ships without real secrets: replace `DB_*` accordingly before deploying.

Auth
- Endpoints: `/api/auth/register`, `/api/auth/login`, `/api/auth/refresh`, `/api/auth/forgot`, `/api/auth/reset`.
- Use `Authorization: Bearer <token>` for authenticated routes.

Multi‑Tenant
- Middleware `App\Http\Middleware\SetTenant` resolve o tenant do usuário (ou `X-Tenant-ID`).
- `tenant_id` aplicado via escopo global (`App\Traits\BelongsToTenant`).
- Policies asseguram acesso à conta apenas para `account_members`.

API Highlights
- Accounts: `GET/POST /api/accounts`, `GET/PATCH /api/accounts/{id}`, `POST /api/accounts/{id}/members`
- Cards: `POST/GET /api/cards`, `GET /api/cards/{id}`, `POST /api/cards/{id}/purchase`, `POST /api/cards/{id}/close`
- Invoices: `GET /api/cards/{id}/invoices`, `POST /api/invoices/{id}/pay`
- Categories: CRUD `/api/categories`
- Transactions: CRUD `/api/transactions` (suporta `splits` e `tags`)
- Recurrences: CRUD `/api/recurrences`
- Goals: CRUD `/api/goals`, progresso: `/api/goals/{id}/progress`
- Reports: `/api/reports/cashflow`, `/api/reports/by-category`, `/api/reports/balance-summary`, `/api/reports/goals`
- Import: `/api/import/csv`

Background Jobs & Schedule
- Recorrências: job `ProcessRecurrences` a cada 15 min.
- Fechamento de faturas: job `CloseInvoices` diariamente (usa `closing_day`).
- Alertas de metas: job `GoalAlerts` diariamente.
- Ativos via `php artisan schedule:work`.

Testing (CI)
- GitHub Actions workflow roda `phpunit` (Feature).
- Local: `php artisan test`.

Notes
- Swagger annotations em `app/OpenApi` e controladores; gere com `php artisan l5-swagger:generate`.
- Email via `MAIL_MAILER=log` no dev; altere em produção.

