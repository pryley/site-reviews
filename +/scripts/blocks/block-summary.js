/** global: GLSR */

(function( blocks, components, editor, element, i18n, $ ) {
	'use strict';
	var __ = i18n.__;
	var el = element.createElement;
	var Inspector = editor.InspectorControls;
	var PanelBody = components.PanelBody;
	var ServerSideRender = components.ServerSideRender;
	var TextControl = components.TextControl;
	var ToggleControl = components.ToggleControl;
	var RangeControl = components.RangeControl;
	var SelectControl = components.SelectControl;
	var selectPlaceholder = { label: '- ' + __( 'Select', 'site-reviews' ) + ' -', value: '' };

	var categories = [];
	wp.apiFetch({ path: '/site-reviews/v1/categories?per_page=50'}).then( function( terms ) {
		categories.push( selectPlaceholder );
		$.each( terms, function( key, term ) {
			categories.push({ label: term.name, value: term.id });
		});
	});

	var types = [];
	wp.apiFetch({ path: '/site-reviews/v1/types?per_page=50'}).then( function( reviewtypes ) {
		if( reviewtypes.length < 2 )return;
		types.push( selectPlaceholder );
		$.each( reviewtypes, function( key, type ) {
			types.push({ label: type.name, value: type.slug });
		});
	});

	var toggleHide = function( key, isChecked, props ) {
		var hide = _.without( props.attributes.hide.split(','), '' );
		if( isChecked ) {
			hide.push( key );
		}
		else {
			hide = _.without( hide, key );
		}
		props.setAttributes({ hide: hide.toString() });
	};

	var checkboxControl = function( id, key, label, props ) {
		return el( 'div',
			{ className: 'components-base-control__field' },
			el( 'input', {
				checked: props.attributes.hide.split(',').indexOf( key ) > -1,
				className: 'components-checkbox-control__input',
				id: 'inspector-checkbox-control-hide-' + id,
				type: 'checkbox',
				value: 1,
				onChange: function( ev ) {
					toggleHide( key, ev.target.checked, props );
				},
			}),
			el( 'label', {
				className: 'components-checkbox-control__label',
				htmlFor: 'inspector-checkbox-control-hide-' + id,
			}, label )
		);
	};

	var checkboxesControl = function( checkboxes, props ) {
		var i = 0;
		var elements = ['div', { className: 'components-base-control' }];
		for( var name in checkboxes ) {
			if( !checkboxes.hasOwnProperty( name ))continue;
			elements.push( checkboxControl( i, name, checkboxes[name], props ));
			i++;
		}
		return el.apply( null, elements );
	};

	var attributes = {
		assigned_to: {
			default: '',
			type: 'string',
		},
		category: {
			default: '',
			type: 'string',
		},
		className: {
			default: '',
			type: 'string',
		},
		hide: {
			default: '',
			type: 'string',
		},
		post_id: {
			default: '',
			type: 'string',
		},
		rating: {
			default: 1,
			type: 'number',
		},
		schema: {
			default: false,
			type: 'boolean',
		},
		type: {
			default: 'local',
			type: 'string',
		},
	};

	var edit = function( props ) {
		props.attributes.post_id = $('#post_ID').val();
		return [
			el( Inspector,
				{ key: 'inspector' },
				el( PanelBody,
					{ title: __( 'Settings', 'site-reviews' ) },
					el( TextControl, {
						help: __( 'Limit reviews to those assigned to this post ID. You can also enter "post_id" to use the ID of the current page, or "parent_id" to use the ID of the parent page.', 'site-reviews' ),
						label: __( 'Assigned To', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ assigned_to: value });
						},
						type: 'text',
						value: props.attributes.assigned_to,
					}),
					el( SelectControl, {
						help: __( 'Limit reviews to a specific category.', 'site-reviews' ),
						label: __( 'Category', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ category: value });
						},
						options: categories,
						value: props.attributes.category,
					}),
					el( SelectControl, {
						help: __( 'Limit reviews to a specific type.', 'site-reviews' ),
						label: __( 'Type', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ type: value });
						},
						options: types,
						value: props.attributes.type,
					}),
					el( RangeControl, {
						help: __( 'Limit reviews to a minimum rating.', 'site-reviews' ),
						label: __( 'Minimum Rating', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ rating: value });
						},
						min: 0,
						max: 5,
						value: props.attributes.rating,
					}),
					el( ToggleControl, {
						help: __( 'The schema should only be enabled once per page.', 'site-reviews' ),
						label: __( 'Enable the schema?', 'site-reviews' ),
						checked: props.attributes.schema,
						onChange: function( value ) {
							props.setAttributes({ schema: value });
						},
					}),
					checkboxesControl.apply( null, [GLSR.hideoptions.site_reviews_summary, props] )
				)
			),
			el( ServerSideRender,
				{
					block: GLSR.nameprefix + '/summary',
					attributes: props.attributes,
				}
			)
		];
	};

	blocks.registerBlockType( GLSR.nameprefix + '/summary', {
		attributes: attributes,
		category: GLSR.nameprefix,
		description: __( 'Display a summary of your reviews.', 'site-reviews' ),
		edit: edit,
		// icon: 'star-half',
		icon: el( components.SVG, {
			width: '22px',
			height: '22px',
			viewBox: '0 0 22 22',
			xmlns: 'http://www.w3.org/2000/svg',
		}, el( components.Path, {
			d: 'M11 2l-3 6-6 .75 4.13 4.62-1.13 6.63 6-3 6 3-1.12-6.63 4.12-4.62-6-.75-3-6zm0 2.24l2.34 4.69 4.65.58-3.18 3.56.87 5.15-4.68-2.34v-11.64zm8.415-2.969l-.518.824c-.536-.342-1.13-.54-1.769-.54-.842 0-1.418.365-1.418.941 0 .522.491.725 1.31.842l.437.059c1.022.14 2.03.563 2.03 1.733 0 1.283-1.161 1.985-2.525 1.985-.855 0-1.881-.284-2.534-.846l.554-.81c.432.396 1.247.693 1.976.693.824 0 1.472-.351 1.472-.932 0-.495-.495-.725-1.418-.851l-.491-.068c-.936-.131-1.868-.572-1.868-1.742 0-1.265 1.121-1.967 2.484-1.967.918 0 1.643.257 2.277.68z',
		})),
		// keywords: ['recent reviews'],
		save: function() { return null; },
		// supports: {
		// 	customClassName: false,
		// },
		title: 'Summary',
	});
})(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.element,
	window.wp.i18n,
	window.jQuery
);
