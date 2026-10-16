# Version 2.1

**Status:** In progress  
**Base:** main (after version 2.0 public datalayer)

## Summary
Finish admin cleanup so Steve Weeks Music no longer needs shared `offthekitchen.com/classes` for Form, calendar, or AdminMain DB version checks; move leftover page-level SQL into the shared datalayer; ship the Connection latin1 charset fix.

## Changes

### Features
- **admin-cleanup** (version2.1-feature-admin-cleanup): Relocate admin UI helpers and finish datalayer leftovers.
  - What changed:
    - Moved `Form` into `admin/includes/Form.php` (loaded via admin CommonFunctions).
    - Pointed all admin `tc_calendar` includes at local `admin/calendar/classes/tc_calendar.php`.
    - AdminMain (and stale `images/AdminMain.php`) use PDO `Connection` for MySQL version.
    - ErrorReport payment/revenue checks live in `PaymentRepository` / `RevenueRepository`.
    - Expense/Revenue category XREF helpers use repository methods.
    - Shared `Connection.php` uses `charset=latin1` to preserve legacy UTF-8-in-latin1 text (Icelandic venues, etc.).
  - Why: Clear the path to decommission shared `classes` for this site, keep one data-access style, and fix garbled characters without waiting on a separate change.

- **vendor-report** (version2.1-feature-vendor-report): Admin Vendor Report for payments and expenses by vendor and by year.
  - What changed:
    - New `admin/VendorReport.php` with year + vendor filters; Vendor Data table (Payment / Expense / Net); Payments and Expenses by Year when Year = ALL.
    - ReportFilters vendor dropdown (ALL) and Reports menu links (responsive header, legacy header, Admin Main).
    - PaymentMaintenance `VENDOR_ID` query param now sets `selVendor` so vendor drill-down links work.
  - Why: Review vendor-related payments and expenses without the product matrix from Artist Report.

- **vendor-active** (version2.1-feature-vendor-active): Active/inactive flag for vendors, with Payment, Expense, and Vendor Report dropdown behavior.
  - What changed:
    - Vendor Maintenance ACTIVE checkbox (unchecked by default on new vendors).
    - `renderVendorDropDown` modes: entry (active + NONE + ALL), edit (active + NONE, keep current inactive), report (all vendors + ALL).
    - Payment/Expense search: NONE = no vendor; ALL = any vendor; add rejects ALL.
    - Vendor Report filter uses the shared dropdown method.
    - Companion offthekitchen branch `version1.0-feature-vendor-active`: `VENDOR.ACTIVE` on entity/repository; payment/expense `vendorUnassigned` search.
  - Why: Retire vendors without deleting history; keep new entry limited to active vendors while old records can keep an inactive assignment.

- **lookup-active-flags** (version2.1-feature-lookup-active-flags): Active flags for Category, Tax Category, and Revenue Type (same pattern as Vendor).
  - What changed:
    - ACTIVE checkbox on Category, Tax Category, and Revenue Type Maintenance (unchecked by default on new).
    - Shared dropdown modes for Tax Category, Category, and Revenue Type (entry/edit/report); report filters use those helpers.
    - Expense tax category and Revenue type: search ALL/NONE; add/update reject ALL and NONE (required).
    - Expense/Revenue category checkboxes show active only, plus assigned inactive while editing; Clear reloads active-only.
    - Companion offthekitchen branch `version1.1-feature-lookup-active-flags`: `ACTIVE` on Category/TaxCategory/RevenueType; expense `taxCategoryUnassigned`; revenue `revenueTypeUnassigned`.
  - Why: Retire lookups without deleting history; keep new entry limited to active values.

- **report-filter-defaults** (version2.1-feature-report-filter-defaults): Better defaults for Song Maintenance and tax reports; shared year dropdown.
  - What changed:
    - Song Maintenance CD search dropdown defaults to ALL (not SINGLES when unset).
    - Income Tax Report opens on the current year and auto-generates; Revenue/Expenses boxes have outer gutters and contained zebra rows.
    - Sales Tax Report opens on the current year and current quarter and auto-generates.
    - Shared `renderYearDropDown` (order + default; newest-first and current year by default); ReportFilters uses it for all year filters.
  - Why: Land on useful filters without extra clicks; keep year lists consistent across reports.

- **payment-year-royalty-error** (version2.1-feature-payment-year-royalty-error): Vendor Report YEAR→Payment Maintenance; royalty quantity on Error Report.
  - What changed:
    - Vendor Report payment drill-down links keep `YEAR` (and avoid duplicate `VENDOR_ID` when a vendor is selected).
    - Payment Maintenance accepts `YEAR` and searches via `paymentYear`.
    - `REVENUE_TYPE_ROYALTY` (2) in AdminSettings; Error Report flags royalty revenues with a quantity.
    - Companion offthekitchen branch `version1.2-feature-payment-year-royalty-error`: `findRevenueErrors` optional royalty type check.
  - Why: Year-scoped payment drill-down from Vendor Report; catch royalties that incorrectly carry a product quantity.

### Bugfixes
- **mileage-field-order** (version2.1-bugfix-mileage-field-order): Put DATE before MILEAGE on Mileage Maintenance.
  - What was wrong: MILEAGE appeared above DATE when the fields stacked (and before DATE in the form layout).
  - What fixed it: Swapped the DATE and MILEAGE field blocks so DATE is first, then MILEAGE, then REASON.

## Test notes
- Pages to check: AdminMain, Expense/Revenue maintenance (datepicker + category checkboxes), Error Report, one other Form+calendar maintenance page, Vendor Report (`admin/VendorReport.php`), Payment/Expense maintenance drill-downs from report amounts, Vendor Maintenance ACTIVE checkbox, Payment/Expense vendor dropdown add/search/edit modes, Mileage Maintenance field order (DATE then MILEAGE), Category/Tax Category/Revenue Type ACTIVE, Expense tax category and Revenue type dropdowns, Expense/Revenue inactive category checkboxes + Clear, Song Maintenance CD default ALL, Income Tax Report (current year + generate + section gutters), Sales Tax Report (current year/quarter + generate), any year-filter report for newest-first years, Vendor Report payment links with YEAR (+ single VENDOR_ID when vendor filtered), Payment Maintenance `?YEAR=`, Error Report royalty+quantity rows
- Special setup steps: Deploy updated offthekitchen datalayer (`Connection.php`, Payment/Revenue/Expense/Vendor/Category/TaxCategory/RevenueType repositories) with this release. `ACTIVE` columns on `VENDOR`, `CATEGORY`, `TAX_CATEGORY`, and `REVENUE_TYPE` must already exist (local and production). Shared `classes` folder is left in place for Thurdy. Coordinate merge of offthekitchen `version1.2-feature-payment-year-royalty-error` (and earlier `version1.1` / `version1.0` on main as needed) with this feature.
