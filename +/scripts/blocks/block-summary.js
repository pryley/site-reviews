/** global: GLSR */

(function( blocks, components, editor, element, i18n ) {
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
		title: {
			default: '',
			type: 'string',
		},
		type: {
			default: '',
			type: 'string',
		},
	};

	var categories = [];
	var types = [];

	wp.apiFetch({ path: '/site-reviews/v1/categories'}).then( function( terms ) {
		categories.push({ label: '- ' + __( 'Select', 'site-reviews' ) + ' -', value: '' });
		$.each( terms, function( key, term ) {
			categories.push({ label: term.name, value: term.id });
		});
	});

	wp.apiFetch({ path: '/site-reviews/v1/types'}).then( function( reviewtypes ) {
		if( reviewtypes.length < 2 )return;
		types.push({ label: '- ' + __( 'Select', 'site-reviews' ) + ' -', value: '' });
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

	var createGroupedCheckboxControl = function( id, key, label, props ) {
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

	var edit = function( props ) {
		props.attributes.post_id = $('#post_ID').val();
		return [
			el( Inspector,
				{ key: 'inspector' },
				el( PanelBody,
					{ title: __( 'Settings', 'site-reviews' ) },
					el( TextControl, {
						help: __( 'Add a custom heading.', 'site-reviews' ),
						label: __( 'Title', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ title: value });
						},
						type: 'text',
						value: props.attributes.title,
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
					el( SelectControl, {
						help: __( 'Limit reviews to a specific category.', 'site-reviews' ),
						label: __( 'Category', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ category: value });
						},
						options: categories,
						value: props.attributes.category,
					}),
					el( TextControl, {
						help: __( 'Limit reviews to those assigned to this post ID. You can also enter "post_id" to use the ID of the current page.', 'site-reviews' ),
						label: __( 'Assigned To', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ assigned_to: value });
						},
						type: 'text',
						value: props.attributes.assigned_to,
					}),
					el( RangeControl, {
						help: __( 'Limit reviews to a minimum rating.', 'site-reviews' ),
						label: __( 'Minimum Rating', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ rating: value });
						},
						min: 1,
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
					el( 'div',
						{ className: 'components-base-control' },
						createGroupedCheckboxControl( '0', 'if_empty', __( 'Hide if no reviews are found', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '1', 'bars', __( 'Hide the bars', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '3', 'rating', __( 'Hide the rating', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '4', 'stars', __( 'Hide the stars', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '5', 'summary', __( 'Hide the summary', 'site-reviews' ), props )
					)
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
		icon: 'star-half',
		// keywords: ['recent reviews'],
		save: function() { return null; },
		// supports: {
		// 	customClassName: false,
		// },
		title: 'Review Summary',
	});
})(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.element,
	window.wp.i18n
);
