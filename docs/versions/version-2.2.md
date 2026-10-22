# Version 2.2

**Status:** In progress
**Base:** main (after version 2.1)

## Summary
Let Song Maintenance edit the existing ISWC column and show that value on the Song Report.

## Changes

### Features
- **song-iswc** (version2.2-feature-song-iswc): Optional ISWC text on Song Maintenance and the Song Report.
  - What changed:
    - Song Maintenance has an ISWC text box directly under BMI Number (max 16 characters, blank allowed, no format check).
    - Song Report shows an ISWC label directly under BMI # for every song, including when the value is empty.
    - Companion offthekitchen branch `version1.3-feature-song-iswc`: `Song` and `SongRepository` read and write `SONG.ISWC`. A blank is stored as an empty string.
  - Why: Replace the placeholder ISWC codes with the real work codes and see them while reviewing songs.

### Bugfixes

## Test notes
- Pages to check: Song Maintenance (`admin/SongMaintenance.php`), Song Report (`admin/SongReport.php`), one public song page (should still load and should not show ISWC)
- Special setup steps: `SONG.ISWC` must already exist (`varchar(16)`, not nullable). Deploy the offthekitchen datalayer (`Song.php`, `SongRepository.php`) with this release. Coordinate merge of offthekitchen `version1.3-feature-song-iswc`.
