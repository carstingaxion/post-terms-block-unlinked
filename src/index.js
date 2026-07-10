import './badges.scss';

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Returns true when the block is core/post-terms.
 *
 * The panel is intentionally shown for every core/post-terms instance,
 * regardless of which taxonomy it displays — settings added here apply
 * to any term block, not just GatherPress-specific variations.
 *
 * @param {Object} props Block props passed to the edit component.
 * @return {boolean}
 */
function isPostTermsBlock( props ) {
	return props.name === 'core/post-terms';
}

/**
 * Adds a "Post Terms" inspector panel to every core/post-terms block.
 *
 * The panel is a placeholder for settings that will be introduced as the
 * plugin grows. For now it surfaces a short description of what this
 * plugin does so editors know it is active.
 */
const withPostTermsPanel = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( ! isPostTermsBlock( props ) ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Post Terms Block Unlinked', 'post-terms-block-unlinked' ) }
						initialOpen={ true }
					>
						<ToggleControl
							label={ __( 'Neutralise links', 'post-terms-block-unlinked' ) }
							help={ __(
								'Replace term links with inert elements on the frontend. Enable when the taxonomy has URL rewrites disabled.',
								'post-terms-block-unlinked'
							) }
							checked={ !! props.attributes.neutraliseLinks }
							onChange={ ( value ) =>
								props.setAttributes( { neutraliseLinks: value } )
							}
							__nextHasNoMarginBottom
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'withPostTermsPanel' );

addFilter(
	'editor.BlockEdit',
	'post-terms-block-unlinked/inspector-panel',
	withPostTermsPanel
);
