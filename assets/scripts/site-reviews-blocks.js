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

	var categories = [];
	var types = [];

	wp.apiFetch({ path: '/site-reviews/v1/categories'}).then( function( terms ) {
		categories.push(selectPlaceholder);
		$.each( terms, function( key, term ) {
			categories.push({ label: term.name, value: term.id });
		});
	});

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
						help: __( 'Limit reviews to those assigned to this post ID. You can also enter "post_id" to use the ID of the current page.', 'site-reviews' ),
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
						min: 1,
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
					el( 'div',
						{ className: 'components-base-control' },
						createGroupedCheckboxControl( '0', 'assigned_to', __( 'Hide the assigned to link (if shown)', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '1', 'author', __( 'Hide the author', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '2', 'avatar', __( 'Hide the avatar (if shown)', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '3', 'content', __( 'Hide the content', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '4', 'date', __( 'Hide the date', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '5', 'rating', __( 'Hide the rating', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '6', 'response', __( 'Hide the response', 'site-reviews' ), props ),
						createGroupedCheckboxControl( '7', 'title', __( 'Hide the title', 'site-reviews' ), props )
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
		description: __( 'Display your most recent reviews.', 'site-reviews' ),
		edit: edit,
		icon: 'star-half',
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
						help: __( 'Limit reviews to those assigned to this post ID. You can also enter "post_id" to use the ID of the current page.', 'site-reviews' ),
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
