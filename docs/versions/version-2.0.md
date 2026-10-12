# Version 2.0

**Status:** In progress  
**Base:** main

## Summary
Finish moving Steve Weeks Music onto the shared PDO datalayer. Admin was already migrated; this release covers the public site.

## Changes

### Features
- **public-datalayer** (version2.0-feature-public-datalayer): Migrate remaining public pages and includes off legacy `CLASS_DIR` entity classes onto `\Datalayer\*Repository`.
  - What changed: `includes/commonFunctions.php`, carousels, and public pages (schedule, music, CD/song/lyrics, reviews/awards, search, Availability, wish, RingRoad, etc.) use shared repositories and camelCase entity properties. Dead Review/Award includes removed from promo pages. Local settings define `REVIEWS_DIR`. Shared datalayer gains `PerformanceRepository::findDistinctYears()` (deploy with offthekitchen datalayer).
  - Why: One data-access style across the site and sibling sites, matching admin and Colorado Sessions.

### Bugfixes

## Test notes
- Pages to check: home, Schedule, Music, CD, Song, Lyrics, Reviews, Awards, Performance, Search, Availability, Wish, RingRoad
- Special setup steps: `DATALAYER_DIR` must point at the shared datalayer; deploy updated `PerformanceRepository.php` with `findDistinctYears()` for schedule year tabs
