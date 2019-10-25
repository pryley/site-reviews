/** global: GLSR */

(function( blocks, components, editor, element, i18n, $ ) {
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

	var categories = [];
	wp.apiFetch({ path: '/site-reviews/v1/categories?per_page=50'}).then( function( terms ) {
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

	var edit = function( props ) {
		return [
			el( Inspector,
				{ key: 'inspector' },
				el( PanelBody,
					{ title: __( 'Settings', 'site-reviews' ) },
					el( TextControl, {
						help: __( 'Assign reviews to a post ID. You can also enter "post_id" to use the ID of the current page, or "parent_id" to use the ID of the parent page.', 'site-reviews' ),
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
					checkboxesControl.apply( null, [GLSR.hideoptions.site_reviews_form, props] )
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
		example: {},
		icon: el( components.SVG, {
			width: '22px',
			height: '22px',
			viewBox: '0 0 22 22',
			xmlns: 'http://www.w3.org/2000/svg',
		}, el( components.Path, {
			d: 'M11 2l-3 6-6 .75 4.13 4.62-1.13 6.63 6-3 6 3-1.12-6.63 4.12-4.62-6-.75-3-6zm0 2.24l2.34 4.69 4.65.58-3.18 3.56.87 5.15-4.68-2.34v-11.64zm8.28-.894v.963h-3.272v2.691h-1.017v-6.3h4.496v.963h-3.479v1.683h3.272z',
		})),
		// keywords: ['recent reviews'],
		save: function() { return null; },
		title: 'Submit a Review',
	});
})(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.element,
	window.wp.i18n,
	window.jQuery
);
