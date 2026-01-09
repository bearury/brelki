import { __ } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { memberLink, cartLink } = attributes;
	return (
		<>


				<InspectorControls>
					<PanelBody title={ __('Header Settings', 'blocks-brelki') }>
						<TextControl
							label={ __('Member Link', 'blocks-brelki') }
							value={ memberLink }
							onChange={ ( value ) => setAttributes( { memberLink: value } ) }
						/>
						<TextControl
							label={ __('Cart Link', 'blocks-brelki') }
							value={ cartLink }
							onChange={ ( value ) => setAttributes( { cartLink: value } ) }
						/>
					</PanelBody>
				</InspectorControls>

			<p { ...useBlockProps() }>
				<div className="header-links">
					<InnerBlocks />
					<div className='right-sections'>
						<div className='header-search'></div>
						<div className='header-mode-switcher'></div>
						{cartLink && (
							<div className='header-cart-link'>
								<a href ={ cartLink }>Cart Link</a>
							</div>
						)}
						<div className='header-mode-switcher'></div>
						<div className='header-member-link'>
							<a href ={ memberLink ? memberLink : '#' }>Member Area</a>
						</div>
					</div>
				</div>
			</p>
		</>
	);
}
