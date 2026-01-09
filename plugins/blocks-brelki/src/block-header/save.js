import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';


export default function save({attributes}) {
	const { memberLink, cartLink } = attributes;
	return (
		<div { ...useBlockProps.save() }>
			<div className="header-links">
				<InnerBlocks.Content />
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
		</div>
	);
}
