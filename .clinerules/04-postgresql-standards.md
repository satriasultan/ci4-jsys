
# POSTGRESQL DEVELOPMENT STANDARDS

## DATABASE ENVIRONMENT

- PostgreSQL is the primary database.
- Follow existing schema conventions.
- Respect existing table relationships.
- Preserve existing constraints and indexes.

## QUERY OPTIMIZATION

- Avoid SELECT * unless necessary.
- Use appropriate WHERE conditions.
- Avoid loading unnecessary records.
- Use LIMIT when inspecting sample data.
- Avoid retrieving entire transaction tables.
- Prefer targeted EXPLAIN when analyzing performance.

## DATABASE INSPECTION

Allowed:
- SELECT
- Information schema inspection
- Read table definitions
- Read function definitions
- Read trigger definitions
- EXPLAIN

## STRICT RESTRICTION

Never execute:
- ALTER
- CREATE
- DROP
- TRUNCATE
- INSERT
- UPDATE
- DELETE
- Trigger modifications
- Function modifications
- Procedure modifications

Generate SQL modification scripts as text only.
