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

### Bugfixes

## Test notes
- Pages to check: AdminMain, Expense/Revenue maintenance (datepicker + category checkboxes), Error Report, one other Form+calendar maintenance page
- Special setup steps: Deploy updated offthekitchen datalayer (`Connection.php`, Payment/Revenue/Expense repositories) with this release. Shared `classes` folder is left in place for Thurdy.
