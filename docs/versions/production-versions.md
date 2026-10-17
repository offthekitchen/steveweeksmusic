# Production versions on the Admin homepage

**Status:** In progress  
**Branch:** version_updates  
**Base:** main

## Summary
The Admin homepage Server Information section shows the site and datalayer versions that are deployed in production. The numbers are hardcoded. They change only when asked.

## Changes

### Features
- **admin-production-versions** (version_updates): Show deployed versions under the web server line, and remove the unused Admin homepage copy.
  - What changed:
    - `admin/AdminMain.php` shows `Admin version: 2.1` and `Datalayer version: 1.2` under the web server version.
    - Deleted unused `images/AdminMain.php`. Admin Home, login, and the headers already use `admin/AdminMain.php`.
  - Why: See the production versions from the Admin homepage without checking git. Keep those labels aligned with what is actually deployed.

### Bugfixes

## Update rule
Later number-only changes wait until requested. Each one is made on `version_updates` from `main`, reviewed, then committed, pushed, and opened as a pull request back to `main`. Those updates do not add another version-doc entry unless asked. Deploy is a separate request.

## Test notes
- Pages to check: Admin homepage (`admin/AdminMain.php`)
- Special setup steps: Confirm Server Information order is PHP, MySQL, web server, Admin version, Datalayer version. Confirm `images/AdminMain.php` is gone.
