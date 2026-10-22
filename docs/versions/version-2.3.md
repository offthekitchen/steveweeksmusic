# Version 2.3

**Status:** In progress
**Base:** main (after version 2.2)

## Summary
Song Maintenance search returns only songs whose ISWC matches the value entered.

## Changes

### Features

### Bugfixes
- **iswc-search** (version2.3-bugfix-iswc-search): ISWC is applied as a search filter on Song Maintenance.
  - What was wrong: Search stored and displayed ISWC, but never sent it to the query. A search with only ISWC filled in returned every song, including songs with a different ISWC.
  - What fixed it:
    - Song Maintenance adds the trimmed ISWC to the search criteria when the box is not blank.
    - Companion offthekitchen branch `version1.4-bugfix-iswc-search`: `SongRepository::find` matches that value with a case-insensitive equality check on the trimmed stored ISWC. A blank ISWC still does not filter. Other callers that do not pass `iswc` are unchanged.

## Test notes
- Pages to check: Song Maintenance (`admin/SongMaintenance.php`), Music (`music.php`)
- Special setup steps: Deploy the offthekitchen datalayer (`SongRepository.php`) with this release. Coordinate merge of offthekitchen `version1.4-bugfix-iswc-search`. Confirm `T-123.123.123` does not list “A Santa Claus Like Me” (`T-123.123.456`), and that `T-123.123.456` does.
