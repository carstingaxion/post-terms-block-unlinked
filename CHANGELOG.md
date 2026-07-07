# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased](https://github.com/carstingaxion/post-terms-block-unlinked/compare/0.1.0...HEAD)

## [0.1.0](https://github.com/carstingaxion/post-terms-block-unlinked/compare/0.1.0...0.1.0) - 2026-07-07

- Filter-based approach using `render_block_core/post-terms`.
- Neutralises term links via `WP_HTML_Tag_Processor` — preserves all existing classes and attributes.
- Stylesheet enqueued on demand, only when the block is rendered with a rewrite-disabled taxonomy.
- Adds `has-ptbu-unlinked` modifier class to the block wrapper for CSS targeting.
