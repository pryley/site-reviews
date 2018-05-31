<?php

return [
	'settings.general.require.approval' => [
		'default' => 'yes',
		'description' => __( 'Set the status of new review submissions to pending.', 'site-reviews' ),
		'label' => __( 'Require approval', 'site-reviews' ),
		'type' => 'yes_no',
	],
	'settings.general.require.login' => [
		'default' => 'no',
		'description' => __( 'Only allow review submissions from registered users.', 'site-reviews' ),
		'label' => __( 'Require login', 'site-reviews' ),
		'type' => 'yes_no',
	],
	'settings.general.require.login_register' => [
		'default' => 'no',
		'depends' => ['settings.general.require.login' => 'yes'],
		'description' => sprintf( __( 'Show a link for a new user to register. The %s Membership option must be enabled in General Settings for this to work.', 'site-reviews' ),
			glsr( 'Modules\Html\Builder' )->a( __( 'Anyone can register' ), ['href' => admin_url( 'options-general.php' )] )
		),
		'label' => __( 'Show registration link', 'site-reviews' ),
		'type' => 'yes_no',
	],
	'settings.general.notification' => [
		'default' => 'none',
		'label' => __( 'Notifications', 'site-reviews' ),
		'options' => [
			'none' => __( 'Do not send review notifications', 'site-reviews' ),
			'default' => __( 'Send to administrator', 'site-reviews' ).' <code>'.(string)get_option( 'admin_email' ).'</code>',
			'custom' => __( 'Send to one or more email addresses', 'site-reviews' ),
			'webhook' => __( 'Send to <a href="https://slack.com/">Slack</a>', 'site-reviews' ),
		],
		'type' => 'radio',
	],
	'settings.general.notification_email' => [
		'default' => '',
		'depends' => ['settings.general.notification' => 'custom'],
		'label' => __( 'Send notification emails to', 'site-reviews' ),
		'placeholder' => __( 'Separate multiple emails with a comma', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.general.webhook_url' => [
		'class' => 'regular-text code',
		'default' => '',
		'depends' => ['settings.general.notification' => 'webhook'],
		'description' => sprintf( __( 'To send notifications to Slack, create a new %s and then paste the provided Webhook URL in the field above.', 'site-reviews' ),
			glsr( 'Modules\Html\Builder' )->a( __( 'Incoming WebHook', 'site-reviews' ), ['href' => 'https://slack.com/apps/new/A0F7XDUAZ-incoming-webhooks'] )
		),
		'label' => __( 'Webhook URL', 'site-reviews' ),
		'type' => 'url',
	],
	'settings.general.notification_message' => [
		'default' => glsr( 'Modules\Html' )->buildTemplate( 'templates/email-notification' ),
		'depends' => ['settings.general.notification' => ['custom', 'default', 'webhook']],
		'description' => __(
			'To restore the default text, save an empty template. '.
			'If you are sending notifications to Slack then this template will only be used as a fallback in the event that <a href="https://api.slack.com/docs/attachments">Message Attachments</a> have been disabled.'.
			'<br>Available template tags:'.
			'<br><code>{review_rating}</code> The review rating number (1-5)'.
			'<br><code>{review_title}</code> The review title'.
			'<br><code>{review_content}</code> The review content'.
			'<br><code>{review_author}</code> The review author'.
			'<br><code>{review_email}</code> The email of the review author'.
			'<br><code>{review_ip}</code> The IP address of the review author'.
			'<br><code>{review_link}</code> The link to edit/view a review',
			'site-reviews'
		),
		'label' => __( 'Notification template', 'site-reviews' ),
		'rows' => 10,
		'type' => 'code',
	],
	'settings.reviews.date.format' => [
		'default' => '',
		'description' => sprintf( __( 'The default date format is the one set in your %s.', 'site-reviews' ),
			glsr( 'Modules\Html\Builder' )->a( __( 'WordPress settings', 'site-reviews' ), ['href' => admin_url( 'options-general.php' )] )
		),
		'label' => __( 'Date Format', 'site-reviews' ),
		'options' => [
			'' => __( 'Use the default date format', 'site-reviews' ),
			'relative' => __( 'Use a relative date format', 'site-reviews' ),
			'custom' => __( 'Use a custom date format', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.reviews.date.custom' => [
		'class' => 'regular-text code',
		'default' => get_option( 'date_format' ),
		'depends' => ['settings.reviews.date.format' => 'custom'],
		'description' => __( 'Enter a custom date format (<a href="https://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>).', 'site-reviews' ),
		'label' => __( 'Custom Date Format', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.reviews.assigned_links.enabled' => [
		'default' => 'no',
		'description' => __( 'Display a link to the assigned post of a review.', 'site-reviews' ),
		'label' => __( 'Enable Assigned Links', 'site-reviews' ),
		'type' => 'yes_no',
	],
	'settings.reviews.avatars.enabled' => [
		'default' => 'no',
		'description'  => __( 'Display reviewer avatars. These are generated from the email address of the reviewer using <a href="https://gravatar.com">Gravatar</a>.', 'site-reviews' ),
		'label' => __( 'Enable Avatars', 'site-reviews' ),
		'type'  => 'yes_no',
	],
	'settings.reviews.excerpt.enabled' => [
		'default' => 'no',
		'description' => __( 'Display an excerpt instead of the full review.', 'site-reviews' ),
		'label' => __( 'Enable Excerpts', 'site-reviews' ),
		'type' => 'yes_no',
	],
	'settings.reviews.excerpt.length' => [
		'class' => 'small-text',
		'default' => 55,
		'depends' => ['settings.reviews.excerpt.enabled' => 'yes'],
		'description' => __( 'Set the excerpt word length.', 'site-reviews' ),
		'label' => __( 'Excerpt Length', 'site-reviews' ),
		'type' => 'number',
	],
	'settings.schema.type.default' => [
		'default' => 'LocalBusiness',
		'description' => __( 'This is the default schema type for the item being reviewed. You can override this option on a per-post/page basis by adding a <code>schema_type</code> metadata value using <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a>.', 'site-reviews' ),
		'label' => __( 'Default Schema Type', 'site-reviews' ),
		'options' => [
			'LocalBusiness' => __( 'Local Business', 'site-reviews' ),
			'Product' => __( 'Product', 'site-reviews' ),
			'custom' => __( 'Custom', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.schema.type.custom' => [
		'class' => 'regular-text',
		'default' => '',
		'depends' => ['settings.schema.type.default' => 'custom'],
		'description' => __( 'Google supports review ratings for the following schema content types: Local businesses, Movies, Books, Music, and Products. <a href="https://schema.org/docs/schemas.html">View more information on schema types here</a>.', 'site-reviews' ),
		'label' => __( 'Custom Schema Type', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.schema.name.default' => [
		'default' => 'post',
		'description' => __( 'This is the default name of the item being reviewed. You can override this option on a per-post/page basis by adding a <code>schema_name</code> metadata value using <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a>.', 'site-reviews' ),
		'label' => __( 'Default Name', 'site-reviews' ),
		'options' => [
			'post' => __( 'Use the assigned or current page title', 'site-reviews' ),
			'custom' => __( 'Enter a custom title', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.schema.name.custom' => [
		'class' => 'regular-text',
		'default' => '',
		'depends' => ['settings.schema.name.default' => 'custom'],
		'label' => __( 'Custom Name', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.schema.description.default' => [
		'default' => 'post',
		'description' => __( 'This is the default description for the item being reviewed. You can override this option on a per-post/page basis by adding a <code>schema_description</code> metadata value using <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a>.', 'site-reviews' ),
		'label' => __( 'Default Description', 'site-reviews' ),
		'options' => [
			'post' => __( 'Use the assigned or current page excerpt', 'site-reviews' ),
			'custom' => __( 'Enter a custom description', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.schema.description.custom' => [
		'class' => 'regular-text',
		'default' => '',
		'depends' => ['settings.schema.description.default' => 'custom'],
		'label' => __( 'Custom Description', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.schema.url.default' => [
		'default' => 'post',
		'description' => __( 'This is the default URL for the item being reviewed. You can override this option on a per-post/page basis by adding a <code>schema_url</code> metadata value using <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a>.', 'site-reviews' ),
		'label' => __( 'Default URL', 'site-reviews' ),
		'options' => [
			'post' => __( 'Use the assigned or current page URL', 'site-reviews' ),
			'custom' => __( 'Enter a custom URL', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.schema.url.custom' => [
		'class' => 'regular-text',
		'default' => '',
		'depends' => ['settings.schema.url.default' => 'custom'],
		'label' => __( 'Custom URL', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.schema.image.default' => [
		'default' => 'post',
		'description' => __( 'This is the default image for the item being reviewed. You can override this option on a per-post/page basis by adding a <code>schema_image</code> metadata value using <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a>.', 'site-reviews' ),
		'label' => __( 'Default Image', 'site-reviews' ),
		'options' => [
			'post' => __( 'Use the featured image of the assigned or current page', 'site-reviews' ),
			'custom' => __( 'Enter a custom image URL', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.schema.image.custom' => [
		'class' => 'regular-text',
		'default' => '',
		'depends' => ['settings.schema.image.default' => 'custom'],
		'label' => __( 'Custom Image URL', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.submissions.required' => [
		'default' => ['title','content','name','email'],
		'label' => __( 'Required Fields', 'site-reviews' ),
		'options' => [
			'title' => __( 'Title', 'site-reviews' ),
			'content' => __( 'Review', 'site-reviews' ),
			'name' => __( 'Name', 'site-reviews' ),
			'email' => __( 'Email', 'site-reviews' ),
		],
		'type' => 'checkbox',
	],
	'settings.submissions.akismet' => [
		'default' => 'no',
		'description' => __( 'the <a href="https://akismet.com" target="_blank">Akismet plugin</a> integration provides spam-filtering for your reviews. In order for this setting to have any affect, you will need to first install and activate the Akismet plugin and set up a WordPress.com API key.', 'site-reviews' ),
		'label' => __( 'Enable Akismet Integration', 'site-reviews' ),
		'type' => 'yes_no',
	],
	'settings.submissions.recaptcha.integration' => [
		'default' => '',
		'description' => __( 'Invisible reCAPTCHA is a free anti-spam service from Google. To use it, you will need to <a href="https://www.google.com/recaptcha/admin" target="_blank">sign up</a> for an API key pair for your site. If you are already using a reCAPTCHA plugin listed here, please select it; otherwise choose "Use reCAPTCHA".', 'site-reviews' ),
		'label' => __( 'Invisible reCAPTCHA', 'site-reviews' ),
		'options' => [
			'' => __( 'Do not use reCAPTCHA', 'site-reviews' ),
			'custom' => __( 'Use reCAPTCHA', 'site-reviews' ),
			'invisible-recaptcha' => __( 'Use 3rd-party plugin: Invisible reCaptcha', 'site-reviews' ),
		],
		'type' => 'select',
	],
	'settings.submissions.recaptcha.key' => [
		'class' => 'regular-text code',
		'default' => '',
		'depends' => ['settings.submissions.recaptcha.integration' => 'custom'],
		'label' => __( 'Site Key', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.submissions.recaptcha.secret' => [
		'class' => 'regular-text code',
		'default' => '',
		'depends' => ['settings.submissions.recaptcha.integration' => 'custom'],
		'label' => __( 'Site Secret', 'site-reviews' ),
		'type' => 'text',
	],
	'settings.submissions.recaptcha.position' => [
		'default' => 'bottomleft',
		'depends' => ['settings.submissions.recaptcha.integration' => 'custom'],
		'label' => __( 'Badge Position', 'site-reviews' ),
		'options' => [
			'bottomleft' => 'Bottom Left',
			'bottomright' => 'Bottom Right',
			'inline' => 'Inline',
		],
		'type' => 'select',
	],
	'settings.submissions.blacklist.entries' => [
		'default' => '',
		'description' => __( 'When a review contains any of these words in its title, content, name, email, or IP address, it will be rejected. One word or IP address per line. It will match inside words, so "press" will match "WordPress".', 'site-reviews' ),
		'label' => __( 'Review Blacklist', 'site-reviews' ),
		'rows' => 10,
		'type' => 'code',
	],
	'settings.submissions.blacklist.action' => [
		'default' => 'unapprove',
		'description' => __( 'Choose the action that should be taken when a review is blacklisted.', 'site-reviews' ),
		'label' => __( 'Blacklist Action', 'site-reviews' ),
		'options' => [
			'unapprove' => __( 'Require approval', 'site-reviews' ),
			'reject' => __( 'Reject submission', 'site-reviews' ),
		],
		'type' => 'select',
	],
];
