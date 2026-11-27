# Specification - bankimport

Baseline specification of the existing behaviour of the BankImport
module (v2.8.4), written when the CI was set up: the document states what
exists today and is the reference for every commit and test. Anything
not stated here is out of scope.

## Purpose and context

BankImport imports CSV files exported by a bank into Dolibarr and helps
reconcile them with the existing bank entries, creating the missing
ones. It is a Dolibarr module (descriptor: core/modules/modBankImport.class.php,
module id 104020) deployed as htdocs/custom/bankimport.

## Hard constraints

- Target platform: Dolibarr 16.0 minimum (descriptor
  need_dolibarr_version); the CI exercises it against Dolibarr 24.x with
  PHP 8.3.
- The module requires the abricot library (TPDOdb, TObjetStd) deployed
  as htdocs/custom/abricot; the CI pins a public mirror of it.
- No Dolibarr core file modification; tabs only.
- Activation must stay replayable (idempotent statements and guards).

## Roles

- Dolibarr administrator: activates the module, edits its constants
  (admin/bankimport_setup.php).
- Accounting user holding the bankimport read right (104021): imports
  files, reads the statement pages.

## Functional requirements

- FR-1 Bank menu entry (left menu of the Bank main menu, visible with
  the bankimport read right) opening /bankimport/import.php.
- FR-2 Import form (import.php): bank account, statement number, date
  range, CSV file, separator, column mapping, date format and header
  presence; the defaults come from the module constants
  (BANKIMPORT_MAPPING, BANKIMPORT_SEPARATOR, BANKIMPORT_DATE_FORMAT,
  BANKIMPORT_HEADER).
- FR-3 Comparison (import.php, action compare): the CSV transactions
  are loaded and compared with the bank account lines of the period;
  the result is presented for reconciliation and the missing entries
  can be created.
- FR-4 Statement page (releve.php): bank statement report of an
  account, added as a tab of the bank account card when
  BANKIMPORT_HISTORY_IMPORT is set (descriptor tabs).
- FR-5 Import history (class TBankImportHistory, its table created by
  script/create-maj-base.php played at activation) so past imports can
  be consulted.
- FR-6 Setup page (admin/bankimport_setup.php, admin only) editing the
  module constants; about page (admin/bankimport_about.php).
- FR-7 Permission: one right, "Import bancaire" (bankimport read),
  granted through the user and group permission tabs.

## Non-functional requirements

- NFR-1 Activation replay: the table-creation statements replay without
  error on an already-installed instance (off/on cycle).
- NFR-2 CSRF: every POST form carries the token.
- NFR-3 Translations: every UI string in the lang files (fr_FR first,
  en_US second); no hardcoded string in the pages.
- NFR-4 CI: lint (php -l, pre-commit), static analysis (phpstan, phan,
  from generated baselines), instance tests and page smoke against a
  real Dolibarr - see .github/workflows.
- NFR-5 The module keeps working with the abricot library pinned by the
  CI; no call beyond what that pin provides.

## Acceptance criteria summary

Activation on a Dolibarr 24.0.4 demo instance succeeds; the import page
and both admin pages answer 200 with their marker and no PHP error; the
pre-commit hooks pass over the whole tree; the analysers pass from their
committed baselines and fail on new findings.

## Out of scope

- Formats other than CSV (OFX, QIF, bank-specific parsers).
- Scheduled or automatic imports.
- Multi-entity specifics of the module.
- Any redesign of the legacy UI (tpl templates, abricot widgets).
- Upgrading or refactoring the abricot library itself.
