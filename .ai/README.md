# `.ai/` — shared AI-agent context (Laravel Boost compatible)

| Path | Purpose | Read by |
|---|---|---|
| `guidelines/*.md` | Concise project rules in **Laravel Boost custom-guideline format**. Boost merges them into `CLAUDE.md` / `AGENTS.md` (and other agents' files) when `php artisan boost:install` / `boost:update` runs. | Laravel Boost, any agent (plain Markdown) |
| `handoff.md` | Running work log so one agent can continue where another stopped (limits, crashes, context loss). Newest entry first. | Every agent — read at start, update before stopping |

The canonical, detailed project guide is `/AGENTS.md`; `/CLAUDE.md` imports it. Keep facts in sync: when a convention changes, update `AGENTS.md` **and** the matching guideline here.

## Laravel Boost status

Laravel Boost (`laravel/boost`) is **not installed yet**. Version constraints checked against Packagist on 2026-10-11:

| Boost | Requires |
|---|---|
| 1.8.x (latest 1.x) | PHP ^8.1, `illuminate/support` ^10.49 \| ^11.45.3 \| ^12.41.1, `laravel/mcp` ^0.5 |
| 2.x | PHP ^8.2, Laravel ^11.45.3 \| ^12.41.1 \| ^13 |

This project is on **Laravel 10.48.22**, so Boost 1.8 needs a framework patch bump first. Only do this when the user asks (it changes `composer.json`/`composer.lock`):

```bash
composer require laravel/boost:^1.8 --dev -W   # also updates laravel/framework to >=10.49
php artisan test                               # confirm nothing broke
php artisan boost:install                      # pick agents: Claude Code, Codex, Cursor, …
```

What `boost:install` does with this folder:
- Reads every file in `.ai/guidelines/` and injects it (inside a `<laravel-boost-guidelines>` block) into the agent files it manages, e.g. `CLAUDE.md`, `AGENTS.md`. Hand-written content outside that block is preserved — after installing, remove duplicated text if the files become too long.
- Writes `boost.json` and MCP config (e.g. `.mcp.json`) that start `php artisan boost:mcp` (tools: app info, DB schema/queries, routes, logs, Tinker, docs search). Re-run `php artisan boost:update` after editing guidelines.

Safety when Boost's MCP is active: its database tools use the app's `.env` connection, which may be a real database (see AGENTS.md §11). Read-only queries only.
