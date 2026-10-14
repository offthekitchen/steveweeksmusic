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

### Bugfixes

## Test notes
- Pages to check: AdminMain, Expense/Revenue maintenance (datepicker + category checkboxes), Error Report, one other Form+calendar maintenance page, Vendor Report (`admin/VendorReport.php`), Payment/Expense maintenance drill-downs from report amounts, Vendor Maintenance ACTIVE checkbox, Payment/Expense vendor dropdown add/search/edit modes
- Special setup steps: Deploy updated offthekitchen datalayer (`Connection.php`, Payment/Revenue/Expense/Vendor repositories) with this release. `VENDOR.ACTIVE` column must already exist (local and production). Shared `classes` folder is left in place for Thurdy. Coordinate merge of offthekitchen `version1.0-feature-vendor-active` with this feature.
