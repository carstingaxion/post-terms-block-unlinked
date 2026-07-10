<?php
/**
 * Plugin Name:       Post Terms Block Unlinked
 * Plugin URI:        https://github.com/carstingaxion/post-terms-block-unlinked
 * Description:       Extends the core Post Terms block with GatherPress-aware super-powers: neutralise term links per block, and rewrite shadow-taxonomy term links to their source post permalinks.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Requires plugins:  gatherpress
 * Author:            carstenbach
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-terms-block-unlinked
 * Domain Path:       /languages
 *
 * @package PostTermsBlockUnlinked
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

// Constants.
define( 'POST_TERMS_BLOCK_UNLINKED_VERSION', current( get_file_data( __FILE__, array( 'Version' ), 'plugin' ) ) );
define( 'POST_TERMS_BLOCK_UNLINKED_FILE', __FILE__ );

/**
 * Adds the PostTermsBlockUnlinked namespace to the GatherPress autoloader.
 *
 * Hooks into the 'gatherpress_autoloader' filter so the GatherPress core
 * autoloader can resolve PostTermsBlockUnlinked\* class names to files
 * under this plugin's includes/classes/ directory.
 *
 * @since 0.1.0
 *
 * @param array<string, string> $namespaces Namespace-to-root-path map.
 * @return array<string, string> Modified map with PostTermsBlockUnlinked added.
 */
function post_terms_block_unlinked_autoloader( array $namespaces ): array {
	$namespaces['PostTermsBlockUnlinked'] = __DIR__;

	return $namespaces;
}
add_filter( 'gatherpress_autoloader', 'post_terms_block_unlinked_autoloader' );

/**
 * Initializes the plugin once all plugins are loaded.
 *
 * Boots only when GatherPress core is active, identified by the presence
 * of the GATHERPRESS_VERSION constant.
 *
 * @since 0.1.0
 * @return void
 */
function post_terms_block_unlinked_setup(): void {
	if ( defined( 'GATHERPRESS_VERSION' ) ) {
		\PostTermsBlockUnlinked\Block::get_instance();
		\PostTermsBlockUnlinked\Shadow_Links::get_instance();
	}
}
add_action( 'plugins_loaded', 'post_terms_block_unlinked_setup' );
