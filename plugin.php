<?php
/**
 * Plugin Name:       Post Terms Block Unlinked
 * Description:       Hooks into the core Post Terms block to render terms as non-linked elements when the taxonomy has URL rewrites disabled.
 * Version:           0.1.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            carstenbach
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-terms-block-unlinked
 *
 * @package PostTermsBlockUnlinked
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filters the rendered output of core/post-terms.
 *
 * When the queried taxonomy has rewrite disabled, neutralises the term links
 * by replacing their href with "#" and adds a modifier class to the wrapper
 * so the cursor can be reset to default via CSS.
 *
 * All existing attributes — classes (including is-style-* set in the editor),
 * inline styles, and data-* attributes — are preserved exactly because every
 * mutation goes through WP_HTML_Tag_Processor rather than regex substitution.
 *
 * @param string   $block_content The block HTML.
 * @param array    $block         The full block, including name and attributes.
 * @param WP_Block $instance      The block instance.
 * @return string  Filtered block HTML.
 */
if ( ! function_exists( 'ptbu_filter_post_terms_render' ) ) {
	function ptbu_filter_post_terms_render( string $block_content, array $block, WP_Block $instance ): string {
		$taxonomy = isset( $block['attrs']['term'] ) ? sanitize_key( $block['attrs']['term'] ) : 'category';

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return $block_content;
		}

		$tax_obj = get_taxonomy( $taxonomy );

		// If rewrite is enabled, leave the core output untouched.
		if ( ! empty( $tax_obj->rewrite ) ) {
			return $block_content;
		}

		// Enqueue the stylesheet on demand — only when this branch actually
		// runs. Styles enqueued after wp_head are printed by print_late_styles()
		// in wp_footer, which is correct for a small rule like this.
		wp_enqueue_style(
			'ptbu-unlinked',
			plugin_dir_url( __FILE__ ) . 'assets/badges.css',
			array(),
			'0.1.0'
		);

		// Use WP_HTML_Tag_Processor for all mutations so every existing
		// attribute — classes, inline styles, data-* — is preserved exactly.
		$processor = new \WP_HTML_Tag_Processor( $block_content );

		while ( $processor->next_tag() ) {
			switch ( $processor->get_tag() ) {

				case 'DIV':
					// Add the unlinked-mode modifier to the block wrapper.
					// add_class() appends safely; it never drops existing classes.
					if ( $processor->has_class( 'taxonomy-' . $taxonomy ) ) {
						$processor->add_class( 'has-ptbu-unlinked' );
					}
					break;

				case 'A':
					// Neutralise the link: replace the destination with "#" so the
					// element stays in place and inherits all editor styles (link
					// color, typography, border…), but points nowhere.
					$processor->set_attribute( 'href', '#' );
					break;
			}
		}

		return $processor->get_updated_html();
	}
}
add_filter( 'render_block_core/post-terms', 'ptbu_filter_post_terms_render', 10, 3 );

/**
 * Enqueues the stylesheet in the block editor so the preview is accurate
 * when editing templates that contain core/post-terms for non-rewrite taxonomies.
 */
if ( ! function_exists( 'ptbu_enqueue_editor_styles' ) ) {
	function ptbu_enqueue_editor_styles(): void {
		wp_enqueue_style(
			'ptbu-unlinked-editor',
			plugin_dir_url( __FILE__ ) . 'assets/badges.css',
			array(),
			'0.1.0'
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'ptbu_enqueue_editor_styles' );
