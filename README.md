# Post Terms Block Unlinked

**Contributors:** carstenbach  
**Tags:** post-terms  
**Tested up to:** 6.9  
**Stable tag:** 0.1.1  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

[![Build, test & measure](https://github.com/carstingaxion/post-terms-block-unlinked/actions/workflows/build-test-measure.yml/badge.svg?branch=main)](https://github.com/carstingaxion/post-terms-block-unlinked/actions/workflows/build-test-measure.yml)

## Description

This lightweight plugin enhances the core `core/post-terms` block without replacing it. It uses the `render_block_core/post-terms` filter to intercept the block's output and conditionally neutralise term links when the taxonomy does not support archive URLs.

**Motivation:**

- https://github.com/carstingaxion/gatherpress-productions/issues/2
- https://github.com/WordPress/gutenberg/issues/50904
- https://github.com/WordPress/gutenberg/pull/50946

**How it works:**

- When the selected taxonomy has `rewrite` **enabled** (the default for categories, tags, etc.), the block output is left completely untouched — terms remain as linked anchors pointing to their archive pages.
- When the selected taxonomy has `rewrite` **disabled** (common for internal or organisational taxonomies), the plugin replaces each term link's `href` with `#` and suppresses the pointer cursor, keeping all other editor-applied styles intact.

**Why keep the `<a>` element?**

Replacing links with `<span>` elements would discard all editor-applied link styles — color, typography, border, spacing. By keeping the `<a>` element and only neutralising the `href`, every style the editor applied cascades through unchanged.

**Why this approach?**

By hooking into the existing core block instead of creating a separate custom block, all core block supports are preserved:

- ✅ Color (text, background, gradients, link color)
- ✅ Typography (font size, font family, weight, letter spacing, etc.)
- ✅ Spacing (margin, padding)
- ✅ Border (radius, color, width, style)
- ✅ Separator character
- ✅ Taxonomy selector
- ✅ Block styles and `className` (including `is-style-*` variations)
- ✅ Full compatibility with Query Loop
- ✅ Editor preview styling

No new block to learn — just activate the plugin and the core Post Terms block becomes smarter.

## Requirements

- WordPress 6.2 or later
- PHP 7.4 or later

## Installation

1. Upload the `post-terms-block-unlinked` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. That's it. The core Post Terms block will now automatically unlink terms for taxonomies with rewrite disabled.

## Frequently Asked Questions

**Do I need to change anything in the editor?**

No. The plugin works automatically. Just use the core Post Terms block as you normally would. If the selected taxonomy has rewrite disabled, the frontend output will show unlinked terms.

**Which taxonomies are affected?**

Only taxonomies where `rewrite` is explicitly set to `false`. Standard taxonomies like categories and tags have rewrite enabled by default and will continue to show as linked anchors.

**Does this work inside Query Loop blocks?**

Yes, fully. The filter runs on every instance of the `core/post-terms` block regardless of context.

**Can I customise the styles?**

Yes. The block wrapper receives the class `has-ptbu-unlinked` when in unlinked mode. Target `.has-ptbu-unlinked a` in your theme's CSS to override the default `cursor: default` or add further styling.

**Is the stylesheet always loaded?**

No. The stylesheet is enqueued on demand inside the render filter — only when a `core/post-terms` block with a rewrite-disabled taxonomy is actually present on the page. It is printed in the footer via WordPress's late-style mechanism.

## Changelog

All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md).

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).