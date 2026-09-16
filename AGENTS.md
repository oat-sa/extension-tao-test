# AGENTS.md — extension-tao-test (taoTests)

## Purpose

`oat-sa/extension-tao-test` (extension id `taoTests`) owns the format-agnostic **Test** abstraction: Test ontology, Tests library UI, and registries for runner plugins/providers/previewers.

A concrete runner/creator implementation is required for real authoring — usually `oat-sa/extension-tao-testqti` (`taoQtiTest`). This package is **not** the QTI Test Creator, live delivery runner, or proctor monitor.

## Shared platform agent rules

Common readiness / context-budget / Definition of Done / family anti-patterns /
verify-by-change-type conventions for TAO PHP extensions live in the installed
**`tao`** package (`oat-sa/tao-core`) `AGENTS.md`. Read that file when present
in the platform install.

This file covers **only** ownership and workflows specific to this package.
Do **not** require any external monorepo checkout or workstation-only note paths.


## Stack

Do **not** hardcode dependency or runtime versions in this file.

- PHP on `tao-core` / `generis` / `extension-tao-item` (see `composer.json`)
- FE: AMD + Grunt; `@oat-sa/tao-test` + runners via `views/package.json`
- Maps `@oat-sa/tao-test-runner` for consumers — engine work often upstream
- Versions from composer / package.json / CI only

## Core Rules

- **Follow existing patterns first** in this package.
- **Prefer TDD** for behavior changes unless docs/config-only.
- **Prefer minimal, local changes.** No broad refactors unless requested.
- **Preserve license headers** — sibling-style **`GPL-2.0-only`** (see `composer.json`); do not auto-migrate to SPDX dual-license.
- **Update tests** when behavior changes.
- **Do not weaken** CI / lint / test / CodeRabbit gates.
- For shared agent discipline (context budget, DoD, family anti-patterns), follow the installed **`tao`** (`oat-sa/tao-core`) `AGENTS.md`.


## Structure

```text
manifest.php
actions/
models/classes/          # TestsService, TestModel, runner/preview registries
scripts/install/         # RegisterFrontendPaths, previewer/provider services
views/js/controller/
test/
```

Entrypoints: Tests library menu → `/taoTests/Tests/index`; install scripts register FE paths and registries.

## UI layer

| Surface | Own? | Where |
|---------|------|--------|
| Tests library / import | **Yes** | `actions/` + FE controllers |
| QTI Test Creator / XML editor | **No** | `taoQtiTest` |
| Modern test-runner engine | **No** | npm `@oat-sa/tao-test-runner*` (+ QTI boot) |
| Authoring preview adapters | **No** | often `taoQtiTestPreviewer` |

## Conventions

- Keep this package thin — registries and library chrome, not QTI authoring.
- Sync `structures.xml` / `routes.js` / PHP when changing library screens.
- Do not hand-edit generated bundles; rebuild with Grunt.
- Register new runner/preview providers via nearest `scripts/install/*` pattern.

## Testing

- PHPUnit under `test/`; run from **platform root** against `taoTests/test/...`.
- FE grunt eslint/test with `--extension=taoTests` when JS changes.

Discover the **platform root** (Composer application with `vendor/bin/phpunit`) from the environment — do not assume a particular monorepo path.

## Commands

```bash
./vendor/bin/phpunit -c phpunit.xml.dist taoTests/test
npx grunt taobundle --extension=taoTests
npx grunt eslint:extensionreport --extension=taoTests --force
npx grunt taotest --extension=taoTests
```

## Hard rules / Constraints

- Do not move QTI Test Creator or delivery runner code into this repo.
- Do not confuse with `taoQtiTestPreviewer` (authoring preview) or `taoProctoring`.
- Respect npm runner package ownership for engine bugs.
- Never commit `.ai/` or `.cursor/`.

## Anti-patterns

- Hand-edit `*.min.js` loaders.
- Duplicate test-runner engines in-tree.
- Weaken CI / invent dependency pins.

Also follow family anti-patterns in the installed **`tao`** (`oat-sa/tao-core`) `AGENTS.md`.

## Agent notes (`.ai/`)

Local, **gitignored** branch-scoped notes. Do **not** commit `.ai/`. Durable
rules stay in this file and in tao-core `AGENTS.md` for shared conventions.

Write a **polar-star** under `.ai/work/<slug>/` plus supporting docs; prefer
re-reading those files over chat-only memory.

```text
.ai/work/<branch-slug>/   # injective: `%`→`%25`, `_`→`%5F`, `/`→`_`
.ai/current                # symlink to active work dir
.ai/archive/*.tar.gz
```

Enable once per clone:

```bash
git config core.hooksPath .githooks
```

After `git branch -d` / prune: `scripts/ai-notes-gc.sh`  
Optional: `scripts/ai-notes-gc.sh --self-test`.


## Definition of Done

Satisfy **tao-core** Definition of Done / Readiness conventions when available, plus this package’s Hard rules. Minimal local checklist:

1. Package-specific AC / polar-star addressed.
2. Diff stays in this package unless the task requires otherwise.
3. TDD evidence for behavior changes (or docs/config-only exception).
4. License headers updated (`GPL-2.0-only` sibling style).
5. `.ai/` notes updated when decisions matter.
6. `pr-ready-gate` (or tao-core readiness fallback) passed with real command output.

## Skills ([oat-sa/skills](https://github.com/oat-sa/skills))

1. Search / load skills from **[oat-sa/skills](https://github.com/oat-sa/skills)** first.
2. Prefer reusing shared skills over inventing a parallel local skill.
3. Create a new skill only when nothing suitable exists.

**Must-have for implementation / PR prep:** [`pr-ready-gate`](https://github.com/oat-sa/skills/tree/feat/pr-ready-gate/pr-ready-gate)
(branch pin while testing). If the skill cannot be loaded, use the same criteria
as **`tao` / tao-core AGENTS Readiness gate**: tests + lint on touched scope +
local CodeRabbit with **zero critical / zero major**.

## Readiness gate (before “done” / before opening a PR)

Prefer skill `pr-ready-gate`. Fallback: follow **tao-core** `AGENTS.md` Readiness
gate / Definition of Done, plus this package’s Hard rules. Report real command
results. Docs / hooks / `AGENTS.md`-only changes: `bash -n` on touched shell +
CodeRabbit on the diff; skip irrelevant suites explicitly.


## Pointers

- `README.md` — package overview
- `composer.json` / `LICENSE` — license and Composer deps
- `views/package.json` — FE pins (if present)
- Installed **`tao`** package `AGENTS.md` (`oat-sa/tao-core`) — shared agent conventions
- [oat-sa/skills](https://github.com/oat-sa/skills) — shared skills; [`pr-ready-gate`](https://github.com/oat-sa/skills/tree/feat/pr-ready-gate/pr-ready-gate) (branch pin while testing)
- `.coderabbit.yaml` → remote `oat-sa/tao-code-quality` `coderabbit/php/authoring/v1`
- `.github/workflows/*` — PR CI
- `.githooks/post-checkout` + `scripts/ai-notes-gc.sh` — local `.ai/` lifecycle

## Default Agent Behavior

1. Read this file, then `.ai/current` / polar-star for the branch.
2. Read installed **`tao`** (`oat-sa/tao-core`) `AGENTS.md` for shared gates when available.
3. Check [oat-sa/skills](https://github.com/oat-sa/skills) before inventing procedures; use `pr-ready-gate` for implementation/PR prep.
4. Prefer TDD; keep diffs minimal and inside this package.
5. Respect UI/ownership tables above; avoid Anti-patterns.
6. Update `.ai/` as decisions land; verify with platform-root commands; do not weaken CI.
