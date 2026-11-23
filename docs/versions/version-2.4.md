# Version 2.4

**Status:** In progress
**Base:** main (after version 2.3)

## Summary
A public Copyright page states ownership, how to ask permission, and that the works may not be used to train or operate AI systems. The footer links to that page. Public pages carry machine-readable no-AI tags, and known AI crawlers are turned away in robots.txt while ordinary search indexing stays allowed.

## Changes

### Features
- **copyright-page** (version2.4-feature-copyright-page): Copyright notice, footer link, public no-AI meta tags, and AI-crawler rules.
  - What changed:
    - `copyright.php` shows the copyright notice, a permission contact (`steve@steveweeksmusic.com` and the contact page), and the AI-training and voice/style-cloning prohibition.
    - The footer keeps its existing copyright paragraph and adds a Copyright link to that page.
    - The shared public header adds `noai`, `noimageai`, and `tdm-reservation` meta tags.
    - `robots.txt` still allows general crawlers except `/PressKit/`, and disallows GPTBot, ChatGPT-User, OAI-SearchBot, Google-Extended, Google-CloudVertexBot, ClaudeBot, anthropic-ai, CCBot, FacebookBot, Meta-ExternalAgent, Applebot-Extended, Bytespider, Amazonbot, PerplexityBot, cohere-ai, Diffbot, and ImagesiftBot.
  - Why: Visitors and compliant crawlers need one clear reservation of Steve Weeks Music's works, including a ban on AI training and voice or style cloning, while performance schedules remain available to ordinary search engines.

### Bugfixes

## Test notes
- Pages to check: Copyright (`copyright.php`), Home (`index.php`), Schedule (`schedule.php`), Contact (`contact.php`), `robots.txt`
- Special setup steps: None. Confirm the Copyright page shows the current year and the AI paragraph word for word. Confirm the footer paragraph is unchanged and its Copyright link opens the new page. View source on a public page and confirm `noai`, `noimageai`, and `tdm-reservation`. Confirm `robots.txt` still lists the sitemap and `Disallow: /PressKit/` for general crawlers, and `Disallow: /` for each AI crawler above.
