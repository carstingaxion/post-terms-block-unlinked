# Post Terms Block Unlinked

**Contributors:** carstenbach  
**Tags:** post-terms, gatherpress  
**Requires plugins:** gatherpress  
**Tested up to:** 6.9  
**Stable tag:** 0.1.1  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

[![Build, test & measure](https://github.com/carstingaxion/post-terms-block-unlinked/actions/workflows/build-test-measure.yml/badge.svg?branch=main)](https://github.com/carstingaxion/post-terms-block-unlinked/actions/workflows/build-test-measure.yml)

## Description

Extends the core Post Terms block with GatherPress-aware super-powers: neutralise term links per block, and rewrite shadow-taxonomy term links to their source post permalinks — without replacing the block or sacrificing any of its native styling controls.

No new block to learn — just activate the plugin and the core Post Terms block gains new capabilities.

## Super-powers

### 1 · Neutralise Links

A toggle in the block sidebar ("Post Terms Block Unlinked → Neutralise links") lets editors disable term links on a per-block basis.

When enabled:

- Each term link's `href` is replaced with `#` on the frontend.
- The `has-ptbu-unlinked` modifier class is added to the block wrapper, which resets the cursor and removes underline decoration.
- The `<a>` element is kept intact so all editor-applied link styles (color, typography, border, spacing) cascade through unchanged.
- The frontend stylesheet is loaded on demand — only when at least one block on the page has the toggle enabled.

When disabled (the default), the block output is left completely untouched.

### 2 · Shadow-taxonomy source links

GatherPress shadow-source taxonomies (`_gatherpress_venue`, `_gatherpress_production`, etc.) are always registered with `rewrite=false`, so WordPress has no archive URL for their terms. The core `core/post-terms` block renders those terms as anchors pointing to non-existent term archives.

This super-power automatically detects when a Post Terms block is displaying a GP shadow taxonomy and rewrites each term's href to the **source post's own permalink** — so a venue term links to its venue post, a production term links to its production post, and so on.

- Detection is taxonomy-driven: any taxonomy whose slug starts with `_` and whose derived post type declares `gatherpress-shadow-source` support is treated as a shadow taxonomy.
- No block attribute or editor toggle required — the rewrite happens automatically for every shadow-taxonomy Post Terms block on the frontend.
- Runs at filter priority 5, before the Neutralise Links filter (priority 10), so both super-powers can be combined on the same block.

## Requirements

- WordPress 6.4 or later
- PHP 7.4 or later
- GatherPress

## Installation

1. Ensure GatherPress is installed and active.
2. Upload the `post-terms-block-unlinked` folder to `/wp-content/plugins/`.
3. Activate the plugin through the **Plugins** screen in WordPress.

## Frequently Asked Questions

**Does this work inside Query Loop blocks?**

Yes, fully. The filter runs on every instance of `core/post-terms` regardless of context, including nested Query Loop blocks.

**Can I customise the unlinked styles?**

Yes. Target `.has-ptbu-unlinked a` in your theme's CSS to override or extend the default `cursor: default` / no-underline rules.

**Is the frontend stylesheet always loaded?**

No. It is enqueued on demand inside the render filter — only when at least one block on the current page has "Neutralise links" enabled. It is printed in the footer via WordPress's late-style mechanism.

## Changelog

All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md).

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
