# AGENTS.md — extension-tao-test (taoTests)

> Shared pillars (standards, quality / `pr-ready-gate`, Make, commit/PR):
> [nextgen-stack `tao/AGENTS.md`](https://github.com/oat-sa/nextgen-stack/blob/main/tao/AGENTS.md)
> · local: [`../AGENTS.md`](../AGENTS.md).

## 01 — Project Context

**What / why:** `oat-sa/extension-tao-test` (id `taoTests`) owns the
format-agnostic **Test** abstraction: ontology, Tests library UI,
runner/previewer/provider registries.

Concrete authoring is usually `taoQtiTest`. **Not:** QTI Test Creator, live
delivery runner, or proctor monitor.

**Key directories / stack / constraints:**

```text
manifest.php
actions/
models/classes/          # TestsService, TestModel, registries
scripts/install/
views/js/controller/
test/
```

Entrypoint: `/taoTests/Tests/index`.

- Stack: PHP on tao-core/generis/items; FE AMD/Grunt; `@oat-sa/tao-test`; runners
  often upstream npm.
- Versions from manifests/CI only.

**Docs:** [`README.md`](README.md). Shared docs / decision-log rules → parent AGENTS.

## 02 — Standards & Conventions

Package-only below. Family patterns, quality SoT, `pr-ready-gate`, polar-star →
**parent AGENTS**.

**Patterns / structure:**

- Keep thin — registries + library chrome, not QTI authoring.
- Register providers via nearest `scripts/install/*`.

**Never do (this package):**

- Move QTI Creator or delivery runner here.
- Confuse with `taoQtiTestPreviewer` / `taoProctoring`; hand-edit loaders;
  duplicate engines.

**Ownership**

| Surface | Own? |
|---------|------|
| Tests library / import | **Yes** |
| QTI Test Creator | **No** (`taoQtiTest`) |
| Test-runner engine | **No** (npm) |
| Authoring preview | **No** (often `taoQtiTestPreviewer`) |

## 03 — Build & Test Commands

Shared Make / CI / readiness / commit policy → **parent AGENTS**
([commit/PR policy](https://oat-sa.atlassian.net/wiki/x/_oXmqQ)).

**This package** (from Composer platform root):

```bash
./vendor/bin/phpunit -c phpunit.xml.dist taoTests/test
npx grunt eslint:extensionreport --extension=taoTests --force
npx grunt taobundle --extension=taoTests
```
