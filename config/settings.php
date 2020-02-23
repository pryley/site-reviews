<?php

return [
    'settings.general.style' => [
        'default' => 'default',
        'description' => __('Site Reviews relies on the CSS of your theme to style the submission form. If your theme does not provide proper CSS rules for form elements and you are using a WordPress plugin/theme or CSS Framework listed here, please try selecting it, otherwise choose "Site Reviews (default)".', 'site-reviews'),
        'label' => __('Plugin Style', 'site-reviews'),
        'options' => [
            'bootstrap_4' => 'CSS Framework: Bootstrap 4',
            'bootstrap_4_custom' => 'CSS Framework: Bootstrap 4 (Custom Forms)',
            'contact_form_7' => 'Plugin: Contact Form 7 (v5)',
            'ninja_forms' => 'Plugin: Ninja Forms (v3)',
            'wpforms' => 'Plugin: WPForms Lite (v1)',
            'default' => __('Site Reviews (default)', 'site-reviews'),
            'minimal' => __('Site Reviews (minimal)', 'site-reviews'),
            'divi' => 'Theme: Divi (v3)',
            'materialize' => 'Theme: Materialize',
            'twentyfifteen' => 'Theme: Twenty Fifteen',
            'twentyseventeen' => 'Theme: Twenty Seventeen',
            'twentynineteen' => 'Theme: Twenty Nineteen',
        ],
        'type' => 'select',
    ],
    'settings.general.require.approval' => [
        'default' => 'no',
        'description' => __('Set the status of new review submissions to "unapproved".', 'site-reviews'),
        'label' => __('Require Approval', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.require.login' => [
        'default' => 'no',
        'description' => __('Only allow review submissions from registered users.', 'site-reviews'),
        'label' => __('Require Login', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.require.login_register' => [
        'default' => 'no',
        'depends_on' => [
            'settings.general.require.login' => 'yes',
        ],
        'description' => sprintf(__('Show a link for a new user to register. The %s Membership option must be enabled in General Settings for this to work.', 'site-reviews'),
            '<a href="'.admin_url('options-general.php#users_can_register').'">'.__('Anyone can register', 'site-reviews').'</a>'
        ),
        'label' => __('Show Registration Link', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.multilingual' => [
        'default' => '',
        'description' => __('Integrate with a multilingual plugin to calculate ratings for all languages of a post.', 'site-reviews'),
        'label' => __('Multilingual', 'site-reviews'),
        'options' => [
            '' => __('No Integration', 'site-reviews'),
            'polylang' => __('Integrate with Polylang', 'site-reviews'),
            'wpml' => __('Integrate with WPML', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.general.trustalyze' => [
        'default' => 'no',
        'description' => sprintf(__('Integrate with the %s and validate your reviews on the blockchain to increase online reputation, trust, and transparency.', 'site-reviews'),
            '<a href="https://trustalyze.com/plans?ref=105" target="_blank">Trustalyze Confidence System</a>'
        ),
        'label' => __('Blockchain Validation', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.trustalyze_email' => [
        'default' => '',
        'depends_on' => [
            'settings.general.trustalyze' => ['yes'],
        ],
        'description' => __('Enter your Trustalyze account email here.', 'site-reviews'),
        'label' => __('Trustalyze Email', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.trustalyze_serial' => [
        'default' => '',
        'depends_on' => [
            'settings.general.trustalyze' => ['yes'],
        ],
        'description' => __('Enter your Trustalyze account serial key here.', 'site-reviews'),
        'label' => __('Trustalyze Serial Key', 'site-reviews'),
        'type' => 'password',
    ],
    'settings.general.notifications' => [
        'default' => [],
        'label' => __('Notifications', 'site-reviews'),
        'options' => [
            'admin' => __('Send to administrator', 'site-reviews').' <code>'.(string) get_option('admin_email').'</code>',
            'author' => __('Send to author of the page that the review is assigned to', 'site-reviews'),
            'custom' => __('Send to one or more email addresses', 'site-reviews'),
            'slack' => __('Send to <a href="https://slack.com/">Slack</a>', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.general.notification_email' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['custom'],
        ],
        'label' => __('Send Notification Emails To', 'site-reviews'),
        'placeholder' => __('Separate multiple emails with a comma', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.notification_slack' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['slack'],
        ],
        'description' => sprintf(__('To send notifications to Slack, create a new %s and then paste the provided Webhook URL in the field above.', 'site-reviews'),
            '<a href="https://api.slack.com/incoming-webhooks">'.__('Incoming WebHook', 'site-reviews').'</a>'
        ),
        'label' => __('Slack Webhook URL', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.notification_message' => [
        'default' => glsr('Modules\Html\Template')->build('templates/email-notification'),
        'depends_on' => [
            'settings.general.notifications' => ['admin', 'author', 'custom', 'slack'],
        ],
        'description' => __(
            'To restore the default text, save an empty template. '.
            'If you are sending notifications to Slack then this template will only be used as a fallback in the event that <a href="https://api.slack.com/docs/attachments">Message Attachments</a> have been disabled. Available template tags:'.
            '<br><code>{review_rating}</code> The review rating number (1-5)'.
            '<br><code>{review_title}</code> The review title'.
            '<br><code>{review_content}</code> The review content'.
            '<br><code>{review_author}</code> The review author'.
            '<br><code>{review_email}</code> The email of the review author'.
            '<br><code>{review_ip}</code> The IP address of the review author'.
            '<br><code>{review_link}</code> The link to edit/view a review',
            'site-reviews'
        ),
        'label' => __('Notification Template', 'site-reviews'),
        'rows' => 10,
        'type' => 'code',
    ],
    'settings.reviews.date.format' => [
        'default' => '',
        'description' => sprintf(__('The default date format is the one set in your %s.', 'site-reviews'),
            '<a href="'.admin_url('options-general.php#date_format_custom').'">'.__('WordPress settings', 'site-reviews').'</a>'
        ),
        'label' => __('Date Format', 'site-reviews'),
        'options' => [
            '' => __('Use the default date format', 'site-reviews'),
            'relative' => __('Use a relative date format', 'site-reviews'),
            'custom' => __('Use a custom date format', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.date.custom' => [
        'default' => get_option('date_format'),
        'depends_on' => [
            'settings.reviews.date.format' => 'custom',
        ],
        'description' => __('Enter a custom date format (<a href="https://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>).', 'site-reviews'),
        'label' => __('Custom Date Format', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.reviews.name.format' => [
        'default' => '',
        'description' => __('Choose how names are shown in your reviews.', 'site-reviews'),
        'label' => __('Name Format', 'site-reviews'),
        'options' => [
            '' => __('Use the name as given', 'site-reviews'),
            'first' => __('Use the first name only', 'site-reviews'),
            'first_initial' => __('Convert first name to an initial', 'site-reviews'),
            'last_initial' => __('Convert last name to an initial', 'site-reviews'),
            'initials' => __('Convert to all initials', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.name.initial' => [
        'default' => '',
        'depends_on' => [
            'settings.reviews.name.format' => ['first_initial', 'last_initial', 'initials'],
        ],
        'description' => __('Choose how the initial is displayed.', 'site-reviews'),
        'label' => __('Initial Format', 'site-reviews'),
        'options' => [
            '' => __('Initial with a space', 'site-reviews'),
            'period' => __('Initial with a period', 'site-reviews'),
            'period_space' => __('Initial with a period and a space', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.assigned_links' => [
        'default' => 'no',
        'description' => __('Display a link to the assigned post of a review.', 'site-reviews'),
        'label' => __('Enable Assigned Links', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars' => [
        'default' => 'no',
        'description' => __('Display reviewer avatars. These are generated from the email address of the reviewer using <a href="https://gravatar.com">Gravatar</a>.', 'site-reviews'),
        'label' => __('Enable Avatars', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars_regenerate' => [
        'default' => 'no',
        'depends_on' => [
            'settings.reviews.avatars' => 'yes',
        ],
        'description' => __('Regenerate the avatar whenever a local review is shown?', 'site-reviews'),
        'label' => __('Regenerate Avatars', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars_size' => [
        'default' => 40,
        'depends_on' => [
            'settings.reviews.avatars' => 'yes',
        ],
        'description' => __('Set the avatar size in pixels.', 'site-reviews'),
        'label' => __('Avatar Size', 'site-reviews'),
        'type' => 'number',
    ],
    'settings.reviews.excerpts' => [
        'default' => 'yes',
        'description' => __('Display an excerpt instead of the full review.', 'site-reviews'),
        'label' => __('Enable Excerpts', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.excerpts_length' => [
        'default' => 55,
        'depends_on' => [
            'settings.reviews.excerpts' => 'yes',
        ],
        'description' => __('Set the excerpt word length.', 'site-reviews'),
        'label' => __('Excerpt Length', 'site-reviews'),
        'type' => 'number',
    ],
    'settings.reviews.fallback' => [
        'default' => 'yes',
        'description' => sprintf(__('Display the fallback text when there are no reviews to display. This can be changed on the %s page. You may also override this by using the "fallback" option on the shortcode. The default fallback text is: %s', 'site-reviews'),
            '<a href="'.admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-translations').'">'.__('Translations', 'site-reviews').'</a>',
            '<code>'.__('There are no reviews yet. Be the first one to write one.', 'site-reviews').'</code>'
        ),
        'label' => __('Enable Fallback Text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.schema.type.default' => [
        'default' => 'LocalBusiness',
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_type</code>',
        'label' => __('Default Schema Type', 'site-reviews'),
        'options' => [
            'LocalBusiness' => __('Local Business', 'site-reviews'),
            'Product' => __('Product', 'site-reviews'),
            'custom' => __('Custom', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.type.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'custom',
        ],
        'description' => '<a href="https://schema.org/docs/schemas.html">'.__('View more information on schema types here', 'site-reviews').'</a>',
        'label' => __('Custom Schema Type', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.name.default' => [
        'default' => 'post',
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_name</code>',
        'label' => __('Default Name', 'site-reviews'),
        'options' => [
            'post' => __('Use the assigned or current page title', 'site-reviews'),
            'custom' => __('Enter a custom title', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.name.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.name.default' => 'custom',
        ],
        'label' => __('Custom Name', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.description.default' => [
        'default' => 'post',
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_description</code>',
        'label' => __('Default Description', 'site-reviews'),
        'options' => [
            'post' => __('Use the assigned or current page excerpt', 'site-reviews'),
            'custom' => __('Enter a custom description', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.description.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.description.default' => 'custom',
        ],
        'label' => __('Custom Description', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.url.default' => [
        'default' => 'post',
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_url</code>',
        'label' => __('Default URL', 'site-reviews'),
        'options' => [
            'post' => __('Use the assigned or current page URL', 'site-reviews'),
            'custom' => __('Enter a custom URL', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.url.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.url.default' => 'custom',
        ],
        'label' => __('Custom URL', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.image.default' => [
        'default' => 'post',
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_image</code>',
        'label' => __('Default Image', 'site-reviews'),
        'options' => [
            'post' => __('Use the featured image of the assigned or current page', 'site-reviews'),
            'custom' => __('Enter a custom image URL', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.image.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.image.default' => 'custom',
        ],
        'label' => __('Custom Image URL', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.address' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_address</code>',
        'label' => __('Address', 'site-reviews'),
        'placeholder' => '60 29th Street #343, San Francisco, CA 94110, US',
        'type' => 'text',
    ],
    'settings.schema.telephone' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_telephone</code>',
        'label' => __('Telephone Number', 'site-reviews'),
        'placeholder' => '+1 (877) 273-3049',
        'type' => 'text',
    ],
    'settings.schema.pricerange' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_pricerange</code>',
        'label' => __('Price Range', 'site-reviews'),
        'placeholder' => '$$-$$$',
        'type' => 'text',
    ],
    'settings.schema.offertype' => [
        'default' => 'AggregateOffer',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_offertype</code>',
        'label' => __('Offer Type', 'site-reviews'),
        'options' => [
            'AggregateOffer' => __('AggregateOffer', 'site-reviews'),
            'Offer' => __('Offer', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.price' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'Offer',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_price</code>',
        'label' => __('Price', 'site-reviews'),
        'placeholder' => '50.00',
        'type' => 'text',
    ],
    'settings.schema.lowprice' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'AggregateOffer',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_lowprice</code>',
        'label' => __('Low Price', 'site-reviews'),
        'placeholder' => '10.00',
        'type' => 'text',
    ],
    'settings.schema.highprice' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'AggregateOffer',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_highprice</code>',
        'label' => __('High Price', 'site-reviews'),
        'placeholder' => '100.00',
        'type' => 'text',
    ],
    'settings.schema.pricecurrency' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
        ],
        'description' => __('Custom Field name', 'site-reviews').': <code>schema_pricecurrency</code>',
        'label' => __('Price Currency', 'site-reviews'),
        'placeholder' => 'USD',
        'type' => 'text',
    ],
    'settings.submissions.required' => [
        'default' => ['content', 'email', 'name', 'rating', 'terms', 'title'],
        'description' => __('Choose which fields should be required in the submission form.', 'site-reviews'),
        'label' => __('Required Fields', 'site-reviews'),
        'options' => [
            'rating' => __('Rating', 'site-reviews'),
            'title' => __('Title', 'site-reviews'),
            'content' => __('Review', 'site-reviews'),
            'name' => __('Name', 'site-reviews'),
            'email' => __('Email', 'site-reviews'),
            'terms' => __('Terms', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.submissions.limit' => [
        'default' => '',
        'description' => __('Limits the number of reviews that can be submitted to one-per-person. If you are assigning reviews, then the limit will be applied to the assigned page or category.', 'site-reviews'),
        'label' => __('Limit Reviews', 'site-reviews'),
        'options' => [
            '' => __('No Limit', 'site-reviews'),
            'email' => __('By Email Address', 'site-reviews'),
            'ip_address' => __('By IP Address', 'site-reviews'),
            'username' => __('By Username (will only work for registered users)', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.limit_whitelist.email' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['email'],
        ],
        'description' => __('One Email per line. All emails in the whitelist will be excluded from the review submission limit.', 'site-reviews'),
        'label' => __('Email Whitelist', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.limit_whitelist.ip_address' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['ip_address'],
        ],
        'description' => __('One IP Address per line. All IP Addresses in the whitelist will be excluded from the review submission limit..', 'site-reviews'),
        'label' => __('IP Address Whitelist', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.limit_whitelist.username' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['username'],
        ],
        'description' => __('One Username per line. All registered users with a Username in the whitelist will be excluded from the review submission limit.', 'site-reviews'),
        'label' => __('Username Whitelist', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.recaptcha.integration' => [
        'default' => '',
        'description' => __('Invisible reCAPTCHA is a free anti-spam service from Google. To use it, you will need to <a href="https://www.google.com/recaptcha/admin" target="_blank">sign up</a> for an API key pair for your site.', 'site-reviews'),
        'label' => __('Invisible reCAPTCHA', 'site-reviews'),
        'options' => [
            '' => 'Do not use reCAPTCHA',
            'all' => 'Use reCAPTCHA',
            'guest' => 'Use reCAPTCHA only for guest users',
        ],
        'type' => 'select',
    ],
    'settings.submissions.recaptcha.key' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'label' => __('Site Key', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.recaptcha.secret' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'label' => __('Site Secret', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.recaptcha.position' => [
        'default' => 'bottomleft',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'description' => __('This option may not work consistently if another plugin is loading reCAPTCHA on the same page as Site Reviews.', 'site-reviews'),
        'label' => __('Badge Position', 'site-reviews'),
        'options' => [
            'bottomleft' => 'Bottom Left',
            'bottomright' => 'Bottom Right',
            'inline' => 'Inline',
        ],
        'type' => 'select',
    ],
    'settings.submissions.akismet' => [
        'default' => 'no',
        'description' => __('The <a href="https://akismet.com" target="_blank">Akismet plugin</a> integration provides spam-filtering for your reviews. In order for this setting to have any affect, you will need to first install and activate the Akismet plugin and set up a WordPress.com API key.', 'site-reviews'),
        'label' => __('Enable Akismet Integration', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.submissions.blacklist.integration' => [
        'default' => '',
        'description' => sprintf(__('Choose which Blacklist you would prefer to use for reviews. The %s can be found in the WordPress Discussion Settings page.', 'site-reviews'),
            '<a href="'.admin_url('options-discussion.php#users_can_register').'">'.__('Comment Blacklist', 'site-reviews').'</a>'
        ),
        'label' => __('Blacklist', 'site-reviews'),
        'options' => [
            '' => 'Use the Site Reviews Blacklist',
            'comments' => 'Use the WordPress Comment Blacklist',
        ],
        'type' => 'select',
    ],
    'settings.submissions.blacklist.entries' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.blacklist.integration' => [''],
        ],
        'description' => __('One entry or IP address per line. When a review contains any of these entries in its title, content, name, email, or IP address, it will be rejected. It is case-insensitive and will match partial words, so "press" will match "WordPress".', 'site-reviews'),
        'label' => __('Review Blacklist', 'site-reviews'),
        'rows' => 10,
        'type' => 'code',
    ],
    'settings.submissions.blacklist.action' => [
        'default' => 'unapprove',
        'description' => __('Choose the action that should be taken when a review is blacklisted.', 'site-reviews'),
        'label' => __('Blacklist Action', 'site-reviews'),
        'options' => [
            'unapprove' => __('Require approval', 'site-reviews'),
            'reject' => __('Reject submission', 'site-reviews'),
        ],
        'type' => 'select',
    ],
];
