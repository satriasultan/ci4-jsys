
# PHP CODEIGNITER 4 DEVELOPMENT RULES

## FRAMEWORK

- Use existing CodeIgniter 4 architecture.
- Follow existing project conventions.
- Do not introduce another framework.
- Do not upgrade dependencies unless requested.

## FILE INSPECTION

Prioritize:
- app/Controllers/
- app/Models/
- app/Views/
- app/Config/Routes.php
- app/Helpers/ (only if relevant)

Do not read the entire app directory.

## CODING

- Use PHP 8.2 compatible syntax unless specified otherwise.
- Follow existing naming conventions.
- Reuse existing models and helpers.
- Avoid duplicate database queries.
- Use Query Builder where appropriate.
- Use transactions for multi-step database operations.
- Validate all user inputs.
- Use proper error handling.
- Avoid unnecessary abstraction.

## JAVASCRIPT

- Inspect only the JS file related to the module.
- Do not scan node_modules.
- Do not rebuild frontend assets unless requested.
- Preserve existing AJAX patterns.

## MODIFICATION

- Change only the requested functionality.
- Preserve existing business logic.
- Do not rewrite entire controllers unnecessarily.
- Do not modify unrelated modules.
- Explain any required changes to other files.

## OUTPUT

- Show modified file names.
- Provide concise change summaries.
- Avoid unnecessary documentation.
