<?php
/**
 * Block render filter for Post Terms Block Unlinked.
 *
 * Hooks into the core/post-terms render output and neutralises term links
 * when the queried taxonomy has URL rewrites disabled.
 *
 * @package PostTermsBlockUnlinked
 * @since   0.1.0
 */

declare(strict_types=1);

namespace PostTermsBlockUnlinked;

use GatherPress\Core;
use WP_Block;
use WP_HTML_Tag_Processor;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

/**
 * Filters the core/post-terms block output and enqueues the stylesheet.
 *
 * @since 0.1.0
 */
class Block {

	use Core\Traits\Singleton;

	/**
	 * Constructor — registers hooks.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Registers all hooks.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	protected function setup_hooks(): void {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'init', array( $this, 'register_block_attributes' ), 100 );
		add_filter( 'render_block_core/post-terms', array( $this, 'filter_render' ), 10, 3 );
	}

	/**
	 * Registers the custom neutraliseLinks attribute on the core/post-terms block.
	 *
	 * Runs at priority 100 so core has already registered the block type.
	 *
	 * When true the render filter replaces term hrefs with "#" and adds
	 * the .has-ptbu-unlinked modifier class to the block wrapper.
	 * When false (default) the block output is left completely untouched.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function register_block_attributes(): void {
		$registry   = \WP_Block_Type_Registry::get_instance();
		$block_type = $registry->get_registered( 'core/post-terms' );

		if ( $block_type ) {
			$block_type->attributes['neutraliseLinks'] = array(
				'type'    => 'boolean',
				'default' => false,
			);
		}
	}

	/**
	 * Filters the rendered output of core/post-terms.
	 *
	 * When the neutraliseLinks block attribute is true, replaces term hrefs
	 * with "#" and adds a modifier class to the wrapper so the cursor can
	 * be reset to default via CSS.
	 *
	 * All existing attributes — classes (including is-style-* variants set
	 * in the editor), inline styles, and data-* attributes — are preserved
	 * exactly because every mutation goes through WP_HTML_Tag_Processor
	 * rather than regex substitution.
	 *
	 * @since 0.1.0
	 * @param string   $block_content The block HTML.
	 * @param array    $block         The parsed block data.
	 * @param WP_Block $instance      The block instance.
	 * @return string Filtered block HTML.
	 */
	public function filter_render( string $block_content, array $block, WP_Block $instance ): string {
		// Only act when the editor has explicitly enabled link neutralisation.
		if ( empty( $block['attrs']['neutraliseLinks'] ) ) {
			return $block_content;
		}

		$taxonomy = isset( $block['attrs']['term'] )
			? sanitize_key( $block['attrs']['term'] )
			: 'category';

		// Enqueue the stylesheet on demand — only when this branch actually
		// runs. Styles enqueued after wp_head are printed by print_late_styles()
		// in wp_footer, which is correct for a small rule like this.
		wp_enqueue_style(
			'ptbu-unlinked',
			plugin_dir_url( POST_TERMS_BLOCK_UNLINKED_FILE ) . 'build/index.css',
			array(),
			POST_TERMS_BLOCK_UNLINKED_VERSION
		);

		// Use WP_HTML_Tag_Processor for all mutations so every existing
		// attribute — classes, inline styles, data-* — is preserved exactly.
		$processor = new WP_HTML_Tag_Processor( $block_content );

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
					// Neutralise the link: replace the destination with "#" so
					// the element stays in place and inherits all editor styles
					// (link color, typography, border…), but points nowhere.
					$processor->set_attribute( 'href', '#' );
					break;
			}
		}

		return $processor->get_updated_html();
	}

	/**
	 * Enqueues the editor script and stylesheet.
	 *
	 * The script registers the inspector panel for every core/post-terms
	 * block instance. The stylesheet ensures the editor preview is accurate
	 * for taxonomies with rewrites disabled.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		$asset_file = plugin_dir_path( POST_TERMS_BLOCK_UNLINKED_FILE ) . 'build/index.asset.php';
		$asset      = file_exists( $asset_file )
			? require $asset_file
			: array(
				'dependencies' => array(),
				'version'      => POST_TERMS_BLOCK_UNLINKED_VERSION,
			);

		wp_enqueue_script(
			'ptbu-editor',
			plugin_dir_url( POST_TERMS_BLOCK_UNLINKED_FILE ) . 'build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations(
			'ptbu-editor',
			'post-terms-block-unlinked'
		);

		wp_enqueue_style(
			'ptbu-unlinked',
			plugin_dir_url( POST_TERMS_BLOCK_UNLINKED_FILE ) . 'build/index.css',
			array(),
			$asset['version']
		);
	}
}
