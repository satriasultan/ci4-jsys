
# CONTEXT AND TOKEN EFFICIENCY RULES

## 1. NEVER SCAN THESE DIRECTORIES

Never recursively read, scan, or analyze:

- node_modules/
- vendor/
- .git/
- .idea/
- writable/cache/
- writable/logs/
- writable/session/
- public/uploads/
- public/assets/
- dist/
- build/
- coverage/
- tmp/
- temp/

Do not inspect dependency source code unless
the user explicitly requests it or debugging
requires a specific dependency file.

## 2. PROJECT SCANNING

- Never scan the entire project by default.
- Never list every file in the repository.
- Never read all PHP files.
- Never read all JavaScript files.
- Never read all SQL files.
- Never analyze unrelated modules.

Only inspect files directly related to the task.

## 3. TARGETED FILE READING

When fixing a bug:

1. Identify the relevant controller.
2. Identify the relevant model.
3. Identify the relevant view.
4. Identify the relevant JavaScript.
5. Inspect only related database structures.
6. Read additional files only when necessary.

Do not read unrelated modules.

## 4. AVOID DUPLICATE CONTEXT

- Do not reread unchanged files.
- Reuse previously inspected code.
- Do not repeat completed analysis.
- Do not regenerate entire files unnecessarily.
- Avoid repeatedly inspecting the same database schema.

## 5. RESPONSE EFFICIENCY

- Keep explanations concise.
- Do not summarize the entire project.
- Do not repeat existing code.
- Show only relevant changes.
- Avoid unnecessary examples.

## 6. FILE MODIFICATION

- Modify only requested files.
- Preserve existing coding conventions.
- Do not refactor unrelated code.
- Do not create duplicate functions.
- Do not modify configuration without permission.

## 7. DATABASE

- Follow 01-database-readonly.md.
- Never execute database modifications.
- Generate SQL scripts as text only.

## 8. DEFAULT BEHAVIOR

Use minimal context.
Use targeted inspection.
Prefer precise code changes.
Optimize token consumption.
