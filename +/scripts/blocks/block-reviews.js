/** global: GLSR */

(function( blocks, components, editor, element, i18n ) {
	'use strict';
	var __ = i18n.__;
	var blockName = GLSR.nameprefix + '/reviews';
	var el = element.createElement;
	var Inspector = editor.InspectorControls;
	var AdvancedInspector = editor.InspectorAdvancedControls;
	var PanelBody = components.PanelBody;
	var ServerSideRender = components.ServerSideRender;
	var TextControl = components.TextControl;
	var ToggleControl = components.ToggleControl;
	var RangeControl = components.RangeControl;
	var SelectControl = components.SelectControl;
	var selectPlaceholder = { label: '- ' + __( 'Select', 'site-reviews' ) + ' -', value: '' };

	var categories = [];
	wp.apiFetch({ path: '/site-reviews/v1/categories'}).then( function( terms ) {
		categories.push(selectPlaceholder);
		$.each( terms, function( key, term ) {
			categories.push({ label: term.name, value: term.id });
		});
	});

	var types = [];
	wp.apiFetch({ path: '/site-reviews/v1/types'}).then( function( reviewtypes ) {
		if( reviewtypes.length < 2 )return;
		types.push(selectPlaceholder);
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
		count: {
			default: 5,
			type: 'number',
		},
		hide: {
			default: '',
			type: 'string',
		},
		id: {
			default: '',
			type: 'string',
		},
		pagination: {
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
			default: '',
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
						help: __( 'Pagination should only be enabled once per page.', 'site-reviews' ),
						label: __( 'Pagination', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ pagination: value });
						},
						options: [
							selectPlaceholder,
							{ label: __( 'Enabled', 'site-reviews' ), value: 'true' },
							{ label: __( 'Enabled (using ajax)', 'site-reviews' ), value: 'ajax' },
						],
						value: props.attributes.pagination,
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
					el( RangeControl, {
						help: __( 'The number of reviews to show.', 'site-reviews' ),
						label: __( 'Review Count', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ count: value });
						},
						min: 1,
						max: 50,
						value: props.attributes.count,
					}),
					el( ToggleControl, {
						help: __( 'The schema should only be enabled once per page.', 'site-reviews' ),
						label: __( 'Enable the schema?', 'site-reviews' ),
						checked: props.attributes.schema,
						onChange: function( value ) {
							props.setAttributes({ schema: value });
						},
					}),
					checkboxesControl.apply( null, [GLSR.hideoptions.site_reviews, props] )
				)
			),
			el( AdvancedInspector,
				null,
				el( TextControl, {
					label: __( 'Custom ID', 'site-reviews' ),
					onChange: function( value ) {
						props.setAttributes({ id: value });
					},
					type: 'text',
					value: props.attributes.id,
				})
			),
			el( ServerSideRender, {
				block: blockName,
				attributes: props.attributes,
			})
		];
	};

	blocks.registerBlockType( blockName, {
		attributes: attributes,
		category: GLSR.nameprefix,
		description: __( 'Display your most recent reviews.', 'site-reviews' ),
		edit: edit,
		icon: el( components.SVG, {
			width: '22px',
			height: '22px',
			viewBox: '0 0 22 22',
			xmlns: 'http://www.w3.org/2000/svg',
		}, el( components.Path, {
			d: 'M11 2l-3 6-6 .75 4.13 4.62-1.13 6.63 6-3 6 3-1.12-6.63 4.12-4.62-6-.75-3-6zm0 2.24l2.34 4.69 4.65.58-3.18 3.56.87 5.15-4.68-2.34v-11.64zm3.681-3.54h2.592c1.449 0 2.232.648 2.232 1.823 0 1.071-.819 1.782-2.102 1.827l2.075 2.651h-1.26l-2.007-2.651h-.513v2.651h-1.017v-6.3zm2.565.954h-1.548v1.773h1.548c.819 0 1.202-.297 1.202-.905 0-.599-.405-.869-1.202-.869z',
		})),
		// keywords: ['recent reviews'],
		save: function() { return null; },
		title: 'Latest Reviews',
	});
})(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.element,
	window.wp.i18n
);
