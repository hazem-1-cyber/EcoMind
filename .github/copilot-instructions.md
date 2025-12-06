Repository: EcoMind — quick agent guide

Purpose
- Give an AI coding agent the minimal, specific knowledge to be immediately productive in this PHP MVC-like codebase.

Key architecture (big picture)
- Pattern: Lightweight MVC-ish PHP (no framework). Code is organized into `controller/`, `model/`, and `view/`.
- DB bootstrap: `config.php` holds a `Config` class with `getConnexion()` returning a PDO to the `ecomind` database. Example: `config::getConnexion()`.
- Data flow: Controllers include `config.php` and model DTO classes, perform SQL using PDO, and return query results or echo HTML in some places (mix of responsibilities).

Important files & examples
- `config.php`: central DB connection. Example usage: `$db = config::getConnexion();`.
- `model/DonModel.php`: DTO `class Don` with getters/setters and a `show()` helper that echoes a details table.
- `controller/DonController.php`: methods `listDons()`, `getDon($id)`, `addDon(Don $d)`, `updateDon(Don $d,$id)`, `deleteDon($id)`. These use PDO prepared statements.
- Views: `view/FrontOffice/` and `view/BackOffice/`. JS assets are under `view/*/assets/js/` (examples: `addDon.js`, `paiement.js`) and contain client-side validations that mirror server-side expectations (min amounts, phone format, CP format).

Project-specific conventions & gotchas (be precise)
- File naming and includes: controllers use `include(__DIR__ . '/../config.php');` and `include(__DIR__ . '/../model/SomeModel.php');`. Pay attention to filename case — some files use different capitalization (e.g. `categorieModel.php` vs `CategorieModel.php`) which can break on case-sensitive hosts.
- Model classes are simple DTOs (no active record). Business logic lives in controllers.
- Some views or files have duplicated extensions (`consulterdonpersonnel.php.php`, `canceldon.php.php`, `updatedon.php.html`). Verify the correct path before editing or linking.
- `config.php` currently contains plaintext DB credentials for `localhost`/`root`. Don't assume environment variables are present — use `config.php` as the canonical source for DB connection in this repo.

How to run & debug locally (use these exact steps)
- Quick dev server (from project root):
  - `php -S localhost:8000`  # serve current directory, then open `http://localhost:8000/view/FrontOffice/` or BackOffice paths
- Debugging tips:
  - Enable errors in runtime: add `ini_set('display_errors', 1); error_reporting(E_ALL);` near top of `config.php` while debugging.
  - To inspect DB queries, wrap PDO execution in try/catch and log `$e->getMessage()` (pattern already used across controllers).

Common code patterns to follow when modifying this repo
- When you add controllers or models, follow existing naming: `XController.php` goes in `controller/`, model DTO in `model/` with the class matching the file's exported type.
- Use prepared statements for SQL (controllers already follow this). Return arrays from `fetch()` or PDOStatement for list methods to keep consistency with current callers.
- Views are mostly plain PHP/HTML files. If you add JS, place it under `view/*/assets/js/` and mimic the validation messages / IDs used by existing forms (e.g. `montant`, `email`, `tel`, `cp`).

When proposing changes, include these specifics in PRs
- Affected files: list exact `controller/`, `model/`, and `view/` paths.
- DB impact: include SQL for schema changes (table `dons` expected fields: at minimum `id`, `type_don`, `montant`, `email`, `association_id`, `statut`, `created_at`).
- Backwards compatibility: note how views reference files (double-extension anomalies) and warn about case-sensitivity on deployment.

What this file does NOT cover
- It doesn't prescribe CI, testing frameworks, or environment-management — repo has no test harness or composer.json. Add those only after agreement.

If anything is unclear or you want more detail (example: canonical `dons` table schema, missing view templates, or converting to a simple router), tell me which area to expand.
