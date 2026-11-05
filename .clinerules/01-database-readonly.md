
# STRICT DATABASE MODIFICATION PROHIBITION

## GENERAL RULE

You are strictly prohibited from modifying
the database structure, database objects,
database logic, and database records.

## PROHIBITED DATABASE OPERATIONS

NEVER EXECUTE OR APPLY:

1. ALTER TABLE
2. CREATE TABLE
3. DROP TABLE
4. TRUNCATE TABLE
5. CREATE OR REPLACE FUNCTION
6. CREATE OR REPLACE PROCEDURE
7. CREATE OR REPLACE TRIGGER
8. ALTER FUNCTION
9. ALTER PROCEDURE
10. ALTER TRIGGER
11. DROP FUNCTION
12. DROP PROCEDURE
13. DROP TRIGGER
14. INSERT
15. UPDATE
16. DELETE
17. MERGE

## TRIGGER AND FUNCTION PROTECTION

- Never modify existing PostgreSQL triggers.
- Never modify existing PostgreSQL functions.
- Never modify stored procedures.
- Never recreate database functions or triggers.
- Never change trigger definitions or execution logic.
- Never modify database dependencies.

## ALLOWED OPERATIONS

- SELECT queries.
- Read database schema.
- Inspect table columns.
- Inspect indexes and constraints.
- Read trigger definitions.
- Read function and procedure definitions.
- Analyze SQL execution errors.
- Generate SQL scripts as text.
- Suggest database structure improvements.

## SQL GENERATION POLICY

If database modification is requested:

1. Analyze the requirement.
2. Generate the proposed SQL script as text only.
3. Explain the expected changes.
4. DO NOT execute the SQL.
5. DO NOT modify database files or migration files
   unless explicitly requested for source-code drafting.

## FINAL RESTRICTION

Database modification is NEVER permitted.

Do not bypass these restrictions under any circumstances.
