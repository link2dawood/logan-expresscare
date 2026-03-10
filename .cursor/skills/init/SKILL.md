---
name: init
description: Analyzes project dynamics and codebase structure—tech stack, entry points, conventions, and key flows. Use when the user invokes /init, starts a new session, or asks to analyze, understand, or get an overview of the project or codebase.
---

# Init — Project & Codebase Analysis

When the user runs **/init** or asks to analyze/understand the project, perform a structured codebase analysis and report back.

## Workflow

1. **Map the layout**
   - List top-level directories and notable files (config, entry scripts, docs).
   - Identify whether the repo is a single app, monorepo, or multiple apps in one tree.

2. **Detect tech stack**
   - Look for: `composer.json`, `package.json`, `requirements.txt`, `go.mod`, `Cargo.toml`, etc.
   - Note main language(s), frameworks, and major dependencies.

3. **Find entry points**
   - Web: `index.php`, `public/`, `routes/`, main JS entry, `app/` bootstrap.
   - CLI: bin scripts, `artisan`, `npm run` / `yarn` scripts.
   - APIs: route files, OpenAPI/Swagger, key controllers.

4. **Infer conventions**
   - Naming (PSR, modules, components).
   - Config location and env usage.
   - Where assets, tests, and migrations live.

5. **Summarize dynamics**
   - How requests flow (e.g. route → controller → model).
   - External touchpoints (mail, queues, DB, third-party APIs) if visible.
   - Any README, CONTRIBUTING, or env.example that clarifies setup.

## Output Format

Use this structure so the analysis is easy to reuse:

```markdown
# Project: [name or root path]

## Layout
- [Bullet summary of top-level structure and app boundaries]

## Stack
- **Language(s):** …
- **Frameworks / runtime:** …
- **Notable deps:** …

## Entry points
- **Web:** …
- **CLI / scripts:** …
- **API:** …

## Conventions
- [Naming, config, tests, key dirs]

## Flow (high level)
- [How the app runs / how a typical request or job is handled]

## Notes
- [Env, docs, quirks, or follow-up to explore]
```

Keep the report concise. Prefer scanning real files over guessing; if something is unclear, say so in Notes. One run should give enough context for productive follow-up work.
