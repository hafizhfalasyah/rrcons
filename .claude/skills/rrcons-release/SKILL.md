---
name: rrcons-release
description: Commit, push and open/merge pull requests for RRCONS on both GitHub remotes (solveit-id/rrcons and hafizhfalasyah/rrcons), which have unrelated git histories. Use only when the user explicitly asks to commit, push, create a PR or merge.
disable-model-invocation: true
---

# RRCONS release flow (dual remote)

| Remote | Repo | Local branch → remote branch |
|---|---|---|
| `solveit` | `solveit-id/rrcons` | `solveit-staging` → `staging` |
| `origin` | `hafizhfalasyah/rrcons` | `staging` → `staging`, `main` → `main` |

The histories are **unrelated** (no common ancestor). Content is identical except `README.md` (origin has a custom README). Port commits with `git cherry-pick`; never merge one remote into the other; never sync README unless asked.

## 0. Pre-flight
```bash
git fetch --all --prune
git status -sb && git branch -vv
php artisan test     # must be green
```
Make sure the working branch is based on the latest `solveit/main` (`git merge --ff-only solveit/main` if behind and clean).

## 1. Commit (on `solveit-staging`)
- Group changes into logical commits. Format: `(add|update|fix|perf|test|chore) lowercase imperative summary` — e.g. `(update) remove channel pending tabs and align delete confirmation modal`.
- **No `Co-Authored-By` trailer.** No secrets, no `.env*`, no `public/hot`.
- Stage explicit paths (`git add <paths>`), not `git add -A`, and review `git diff --staged --stat`.

## 2. solveit-id/rrcons
```bash
git push solveit solveit-staging:staging
gh pr create --repo solveit-id/rrcons --base main --head staging --title "<summary>" --body "<what/why + verification>"
gh pr view <n> --repo solveit-id/rrcons --json mergeable,mergeStateStatus
gh pr merge <n> --repo solveit-id/rrcons --merge
```

## 3. hafizhfalasyah/rrcons
```bash
git checkout staging
git merge --ff-only origin/main            # staging starts from the latest main
git cherry-pick <sha1> <sha2> ...          # same commits, oldest first
git push origin staging
gh pr create --repo hafizhfalasyah/rrcons --base main --head staging --title "<summary>" --body "<...>"
gh pr merge <n> --repo hafizhfalasyah/rrcons --merge
```
On a cherry-pick conflict (usually only `README.md`): keep origin's README unless the change is meant for it, `git add`, `git cherry-pick --continue`.

## 4. Sync local branches
```bash
git fetch --all --prune
git checkout main && git merge --ff-only origin/main
git checkout solveit-staging && git merge --ff-only solveit/main
```
Return to the branch the user was on. Report PR URLs, merge commits and test results. Append an entry to `.ai/handoff.md`.

PR bodies: plain description, no co-author lines. If a PR is not mergeable, resolve conflicts on `staging` (merge `main` into `staging` for that remote), re-run tests, push, then merge.
