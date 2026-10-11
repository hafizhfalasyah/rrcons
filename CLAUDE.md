# CLAUDE.md — RRCONS

The full, canonical project guide is `AGENTS.md` (shared with Codex and other agents) and is imported below.
Keep project knowledge in `AGENTS.md` / `.ai/guidelines/`; keep this file for Claude Code specifics only.

@AGENTS.md

---

## Claude Code specifics

### Session start / handoff
- Run the `rrcons-handoff` skill (or follow AGENTS.md §0): read `.ai/handoff.md`, check `git status -sb` / `git branch -vv`, run `php artisan test`.
- Before ending a session or when context is getting full, update `.ai/handoff.md` so another agent (Claude Code, Codex, …) can continue.

### Project skills (`.claude/skills/`)
| Skill | Use it when |
|---|---|
| `rrcons-handoff` | Starting a session, resuming someone else's work, or writing the handoff log before stopping |
| `rrcons-verify` | Before saying a change is done (tests, CSS build check, translation/route checks) |
| `rrcons-bilingual-content` | Adding/changing a CMS field or section (id/en columns, model, validation, admin modal, public view, lang files) |
| `rrcons-release` | The user asks to commit / push / open or merge PRs on both remotes (user-invoked only) |

### Project subagents (`.claude/agents/`)
- `rrcons-reviewer` — read-only review of the current diff against the rules in AGENTS.md §13.

### Settings (`.claude/settings.json`)
- Allowed without prompting: tests, route list, front CSS build, read-only git.
- Asks first: `git push`, any `php artisan migrate*` (incl. `migrate:status`), `php artisan rrcons:*`.
- Denied: reading `.env*` secrets, `migrate:fresh|reset|refresh`, `db:wipe`, `db:seed`.
- Personal overrides go in `.claude/settings.local.json` (git-ignored).

### Environment notes
- Windows host; both PowerShell and Git Bash are available. Paths contain spaces (`D:\PROYEK\PROYEK SOLVE IT\WEB\rrcons`) — always quote them.
- Laravel 13.35 on PHP 8.5.7 runs natively on this machine (`php artisan serve`, `php artisan test`); Docker is optional. After dependency changes run `php artisan optimize:clear`.
- Commit messages: `(type) lowercase summary`, **no `Co-Authored-By` trailer** (explicit user preference).
