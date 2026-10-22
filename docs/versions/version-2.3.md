# Version 2.3

**Status:** In progress
**Base:** main (after version 2.2)

## Summary
Song Maintenance search returns only songs whose ISWC matches the value entered. The local Admin home phpMyAdmin link opens the upgraded local phpMyAdmin folder.

## Changes

### Features

### Bugfixes
- **iswc-search** (version2.3-bugfix-iswc-search): ISWC is applied as a search filter on Song Maintenance.
  - What was wrong: Search stored and displayed ISWC, but never sent it to the query. A search with only ISWC filled in returned every song, including songs with a different ISWC.
  - What fixed it:
    - Song Maintenance adds the trimmed ISWC to the search criteria when the box is not blank.
    - Companion offthekitchen branch `version1.4-bugfix-iswc-search`: `SongRepository::find` matches that value with a case-insensitive equality check on the trimmed stored ISWC. A blank ISWC still does not filter. Other callers that do not pass `iswc` are unchanged.
- **local-phpmyadmin** (version2.3-bugfix-local-phpmyadmin): Local Admin home phpMyAdmin link uses the upgraded WAMP folder.
  - What was wrong: After the local phpMyAdmin upgrade, Admin home still linked to `http://localhost/phpmyadmin/`.
  - What fixed it: When `ENVIRONMENT` is `LOCAL`, the link is `http://localhost/phpmyadmin5.2.3/`. The production URL is unchanged.

## Test notes
- Pages to check: Song Maintenance (`admin/SongMaintenance.php`), Music (`music.php`), Admin home (`admin/AdminMain.php`)
- Special setup steps: Deploy the offthekitchen datalayer (`SongRepository.php`) with this release. Coordinate merge of offthekitchen `version1.4-bugfix-iswc-search`. Confirm `T-123.123.123` does not list “A Santa Claus Like Me” (`T-123.123.456`), and that `T-123.123.456` does. On local Admin home, phpMyAdmin opens `http://localhost/phpmyadmin5.2.3/`.
