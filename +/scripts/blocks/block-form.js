/** global: GLSR */

(function( blocks, components, editor, element, i18n ) {
	'use strict';
	var __ = i18n.__;
	var blockName = GLSR.nameprefix + '/form';
	var el = element.createElement;
	var Inspector = editor.InspectorControls;
	var AdvancedInspector = editor.InspectorAdvancedControls;
	var PanelBody = components.PanelBody;
	var ServerSideRender = components.ServerSideRender;
	var TextControl = components.TextControl;
	var SelectControl = components.SelectControl;
	var selectPlaceholder = { label: '- ' + __( 'Select', 'site-reviews' ) + ' -', value: '' };

	var attributes = {
		assign_to: {
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
		id: {
			default: '',
			type: 'string',
		},
	};

	var categories = [];

	wp.apiFetch({ path: '/site-reviews/v1/categories'}).then( function( terms ) {
		categories.push(selectPlaceholder);
		$.each( terms, function( key, term ) {
			categories.push({ label: term.name, value: term.id });
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
		return [
			el( Inspector,
				{ key: 'inspector' },
				el( PanelBody,
					{ title: __( 'Settings', 'site-reviews' ) },
					el( TextControl, {
						help: __( 'Assign reviews to a post ID. You can also enter "post_id" to use the ID of the current page.', 'site-reviews' ),
						label: __( 'Assign To', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ assign_to: value });
						},
						type: 'text',
						value: props.attributes.assign_to,
					}),
					el( SelectControl, {
						help: __( 'Assign reviews to a category.', 'site-reviews' ),
						label: __( 'Category', 'site-reviews' ),
						onChange: function( value ) {
							props.setAttributes({ category: value });
						},
						options: categories,
						value: props.attributes.category,
					}),
					el( 'div',
						{ className: 'components-base-control' },
						createGroupedCheckboxControl( '0', 'content', __( 'Hide the review field', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '1', 'email', __( 'Hide the email field', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '2', 'name', __( 'Hide the name field', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '3', 'rating', __( 'Hide the rating field', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '4', 'terms', __( 'Hide the terms field', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '5', 'title', __( 'Hide the title field', 'site-reviews' ), props )
					)
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
		description: __( 'Display a review submission form.', 'site-reviews' ),
		edit: edit,
		icon: 'star-half',
		// keywords: ['recent reviews'],
		save: function() { return null; },
		title: 'Submit a Review',
	});
})(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.element,
	window.wp.i18n
);
