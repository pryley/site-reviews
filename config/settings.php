<?php

return [
    'settings.general.style' => [
        'default' => 'default',
        'description' => esc_html_x('Site Reviews relies on the CSS of your theme to style the submission form. If your theme does not provide proper CSS rules for form elements and you are using a WordPress plugin/theme or CSS Framework listed here, please try selecting it, otherwise choose "Site Reviews (default)".', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Plugin Style', 'admin-text', 'site-reviews'),
        'options' => [
            'bootstrap_4' => esc_attr_x('CSS Framework: Bootstrap 4', 'admin-text', 'site-reviews'),
            'bootstrap_4_custom' => esc_attr_x('CSS Framework: Bootstrap 4 (Custom Forms)', 'admin-text', 'site-reviews'),
            'contact_form_7' => esc_attr_x('Plugin: Contact Form 7 (v5)', 'admin-text', 'site-reviews'),
            'ninja_forms' => esc_attr_x('Plugin: Ninja Forms (v3)', 'admin-text', 'site-reviews'),
            'wpforms' => esc_attr_x('Plugin: WPForms Lite (v1)', 'admin-text', 'site-reviews'),
            'default' => esc_attr_x('Site Reviews (default)', 'admin-text', 'site-reviews'),
            'minimal' => esc_attr_x('Site Reviews (minimal)', 'admin-text', 'site-reviews'),
            'divi' => esc_attr_x('Theme: Divi (v3)', 'admin-text', 'site-reviews'),
            'materialize' => esc_attr_x('Theme: Materialize', 'admin-text', 'site-reviews'),
            'twentyfifteen' => esc_attr_x('Theme: Twenty Fifteen', 'admin-text', 'site-reviews'),
            'twentyseventeen' => esc_attr_x('Theme: Twenty Seventeen', 'admin-text', 'site-reviews'),
            'twentynineteen' => esc_attr_x('Theme: Twenty Nineteen', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.general.require.approval' => [
        'default' => 'no',
        'description' => esc_html_x('Set the status of new review submissions to "unapproved".', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Require Approval', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.require.login' => [
        'default' => 'no',
        'description' => esc_html_x('Only allow review submissions from registered users.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Require Login', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.require.login_register' => [
        'default' => 'no',
        'depends_on' => [
            'settings.general.require.login' => 'yes',
        ],
        'description' => sprintf(esc_html_x('Show a link for a new user to register. The %s Membership option must be enabled in General Settings for this to work.', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('options-general.php#users_can_register').'">'.esc_html_x('Anyone can register', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => esc_html_x('Show Registration Link', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.multilingual' => [
        'default' => '',
        'description' => esc_html_x('Integrate with a multilingual plugin to calculate ratings for all languages of a post.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Multilingual', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('No Integration', 'admin-text', 'site-reviews'),
            'polylang' => esc_attr_x('Integrate with Polylang', 'admin-text', 'site-reviews'),
            'wpml' => esc_attr_x('Integrate with WPML', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.general.trustalyze' => [
        'default' => 'no',
        'description' => sprintf(esc_html_x('Integrate with the %s and validate your reviews on the blockchain to increase online reputation, trust, and transparency.', 'admin-text', 'site-reviews'),
            '<a href="https://trustalyze.com/plans?ref=105" target="_blank">Trustalyze Confidence System</a>'
        ),
        'label' => esc_html_x('Blockchain Validation', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.trustalyze_email' => [
        'default' => '',
        'depends_on' => [
            'settings.general.trustalyze' => ['yes'],
        ],
        'description' => esc_html_x('Enter your Trustalyze account email here.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Trustalyze Email', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.trustalyze_serial' => [
        'default' => '',
        'depends_on' => [
            'settings.general.trustalyze' => ['yes'],
        ],
        'description' => esc_html_x('Enter your Trustalyze account serial key here.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Trustalyze Serial Key', 'admin-text', 'site-reviews'),
        'type' => 'password',
    ],
    'settings.general.notifications' => [
        'default' => [],
        'label' => esc_html_x('Notifications', 'admin-text', 'site-reviews'),
        'options' => [
            'admin' => esc_attr_x('Send to administrator', 'admin-text', 'site-reviews').' <code>'.(string) get_option('admin_email').'</code>',
            'author' => esc_attr_x('Send to author of the page that the review is assigned to', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Send to one or more email addresses', 'admin-text', 'site-reviews'),
            'slack' => esc_attr_x('Send to <a href="https://slack.com/">Slack</a>', 'admin-text', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.general.notification_email' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['custom'],
        ],
        'label' => esc_html_x('Send Notification Emails To', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('Separate multiple emails with a comma', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.notification_slack' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['slack'],
        ],
        'description' => sprintf(esc_html_x('To send notifications to Slack, create a new %s and then paste the provided Webhook URL in the field above.', 'admin-text', 'site-reviews'),
            '<a href="https://api.slack.com/incoming-webhooks">'.esc_attr_x('Incoming WebHook', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => esc_html_x('Slack Webhook URL', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.notification_message' => [
        'default' => glsr('Modules\Html\Template')->build('templates/email-notification'),
        'depends_on' => [
            'settings.general.notifications' => ['admin', 'author', 'custom', 'slack'],
        ],
        'description' => esc_html_x(
            'To restore the default text, save an empty template. '.
            'If you are sending notifications to Slack then this template will only be used as a fallback in the event that <a href="https://api.slack.com/docs/attachments">Message Attachments</a> have been disabled. Available template tags:'.
            '<br><code>{review_rating}</code> The review rating number (1-5)'.
            '<br><code>{review_title}</code> The review title'.
            '<br><code>{review_content}</code> The review content'.
            '<br><code>{review_author}</code> The review author'.
            '<br><code>{review_email}</code> The email of the review author'.
            '<br><code>{review_ip}</code> The IP address of the review author'.
            '<br><code>{review_link}</code> The link to edit/view a review',
            'admin-text',
            'site-reviews'
        ),
        'label' => esc_html_x('Notification Template', 'admin-text', 'site-reviews'),
        'rows' => 10,
        'type' => 'code',
    ],
    'settings.reviews.date.format' => [
        'default' => '',
        'description' => sprintf(esc_html_x('The default date format is the one set in your %s.', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('options-general.php#date_format_custom').'">'.esc_attr_x('WordPress settings', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => esc_html_x('Date Format', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('Use the default date format', 'admin-text', 'site-reviews'),
            'relative' => esc_attr_x('Use a relative date format', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Use a custom date format', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.date.custom' => [
        'default' => get_option('date_format'),
        'depends_on' => [
            'settings.reviews.date.format' => 'custom',
        ],
        'description' => esc_html_x('Enter a custom date format (<a href="https://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>).', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Custom Date Format', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.reviews.name.format' => [
        'default' => '',
        'description' => esc_html_x('Choose how names are shown in your reviews.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Name Format', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('Use the name as given', 'admin-text', 'site-reviews'),
            'first' => esc_attr_x('Use the first name only', 'admin-text', 'site-reviews'),
            'first_initial' => esc_attr_x('Convert first name to an initial', 'admin-text', 'site-reviews'),
            'last_initial' => esc_attr_x('Convert last name to an initial', 'admin-text', 'site-reviews'),
            'initials' => esc_attr_x('Convert to all initials', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.name.initial' => [
        'default' => '',
        'depends_on' => [
            'settings.reviews.name.format' => ['first_initial', 'last_initial', 'initials'],
        ],
        'description' => esc_html_x('Choose how the initial is displayed.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Initial Format', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('Initial with a space', 'admin-text', 'site-reviews'),
            'period' => esc_attr_x('Initial with a period', 'admin-text', 'site-reviews'),
            'period_space' => esc_attr_x('Initial with a period and a space', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.assigned_links' => [
        'default' => 'no',
        'description' => esc_html_x('Display a link to the assigned post of a review.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Enable Assigned Links', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars' => [
        'default' => 'no',
        'description' => esc_html_x('Display reviewer avatars. These are generated from the email address of the reviewer using <a href="https://gravatar.com">Gravatar</a>.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Enable Avatars', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars_regenerate' => [
        'default' => 'no',
        'depends_on' => [
            'settings.reviews.avatars' => 'yes',
        ],
        'description' => esc_html_x('Regenerate the avatar whenever a local review is shown?', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Regenerate Avatars', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars_size' => [
        'default' => 40,
        'depends_on' => [
            'settings.reviews.avatars' => 'yes',
        ],
        'description' => esc_html_x('Set the avatar size in pixels.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Avatar Size', 'admin-text', 'site-reviews'),
        'type' => 'number',
    ],
    'settings.reviews.excerpts' => [
        'default' => 'yes',
        'description' => esc_html_x('Display an excerpt instead of the full review.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Enable Excerpts', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.excerpts_length' => [
        'default' => 55,
        'depends_on' => [
            'settings.reviews.excerpts' => 'yes',
        ],
        'description' => esc_html_x('Set the excerpt word length.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Excerpt Length', 'admin-text', 'site-reviews'),
        'type' => 'number',
    ],
    'settings.reviews.fallback' => [
        'default' => 'yes',
        'description' => sprintf(esc_html_x('Display the fallback text when there are no reviews to display. This can be changed on the %s page. You may also override this by using the "fallback" option on the shortcode. The default fallback text is: %s', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-translations').'">'.esc_attr_x('Translations', 'admin-text', 'site-reviews').'</a>',
            '<code>'.__('There are no reviews yet. Be the first one to write one.', 'site-reviews').'</code>'
        ),
        'label' => esc_html_x('Enable Fallback Text', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.pagination.url_parameter' => [
        'default' => 'yes',
        'description' => sprintf(_x('Use the <code>?%s={page_number}</code> URL parameter with AJAX pagination.', 'admin-text', 'site-reviews'), glsr()->constant('PAGED_QUERY_VAR')),
        'label' => esc_html_x('Pagination URL Parameter', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.schema.type.default' => [
        'default' => 'LocalBusiness',
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_type</code>',
        'label' => esc_html_x('Default Schema Type', 'admin-text', 'site-reviews'),
        'options' => [
            'LocalBusiness' => esc_attr_x('Local Business', 'admin-text', 'site-reviews'),
            'Product' => esc_attr_x('Product', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Custom', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.type.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'custom',
        ],
        'description' => '<a href="https://schema.org/docs/schemas.html">'.esc_attr_x('View more information on schema types here', 'admin-text', 'site-reviews').'</a>',
        'label' => esc_html_x('Custom Schema Type', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.name.default' => [
        'default' => 'post',
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_name</code>',
        'label' => esc_html_x('Default Name', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => esc_attr_x('Use the assigned or current page title', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Enter a custom title', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.name.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.name.default' => 'custom',
        ],
        'label' => esc_html_x('Custom Name', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.description.default' => [
        'default' => 'post',
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_description</code>',
        'label' => esc_html_x('Default Description', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => esc_attr_x('Use the assigned or current page excerpt', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Enter a custom description', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.description.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.description.default' => 'custom',
        ],
        'label' => esc_html_x('Custom Description', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.url.default' => [
        'default' => 'post',
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_url</code>',
        'label' => esc_html_x('Default URL', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => esc_attr_x('Use the assigned or current page URL', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Enter a custom URL', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.url.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.url.default' => 'custom',
        ],
        'label' => esc_html_x('Custom URL', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.image.default' => [
        'default' => 'post',
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_image</code>',
        'label' => esc_html_x('Default Image', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => esc_attr_x('Use the featured image of the assigned or current page', 'admin-text', 'site-reviews'),
            'custom' => esc_attr_x('Enter a custom image URL', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.image.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.image.default' => 'custom',
        ],
        'label' => esc_html_x('Custom Image URL', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.address' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_address</code>',
        'label' => esc_html_x('Address', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('60 29th Street #343, San Francisco, CA 94110, US', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.telephone' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_telephone</code>',
        'label' => esc_html_x('Telephone Number', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('+1 (877) 273-3049', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.pricerange' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_pricerange</code>',
        'label' => esc_html_x('Price Range', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('$$-$$$', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.offertype' => [
        'default' => 'AggregateOffer',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_offertype</code>',
        'label' => esc_html_x('Offer Type', 'admin-text', 'site-reviews'),
        'options' => [
            'AggregateOffer' => esc_attr_x('AggregateOffer', 'admin-text', 'site-reviews'),
            'Offer' => esc_attr_x('Offer', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.price' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'Offer',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_price</code>',
        'label' => esc_html_x('Price', 'admin-text', 'site-reviews'),
        'placeholder' => '50.00',
        'type' => 'text',
    ],
    'settings.schema.lowprice' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'AggregateOffer',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_lowprice</code>',
        'label' => esc_html_x('Low Price', 'admin-text', 'site-reviews'),
        'placeholder' => '10.00',
        'type' => 'text',
    ],
    'settings.schema.highprice' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'AggregateOffer',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_highprice</code>',
        'label' => esc_html_x('High Price', 'admin-text', 'site-reviews'),
        'placeholder' => '100.00',
        'type' => 'text',
    ],
    'settings.schema.pricecurrency' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
        ],
        'description' => esc_html_x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_pricecurrency</code>',
        'label' => esc_html_x('Price Currency', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('USD', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.required' => [
        'default' => ['content', 'email', 'name', 'rating', 'terms', 'title'],
        'description' => esc_html_x('Choose which fields should be required in the submission form.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Required Fields', 'admin-text', 'site-reviews'),
        'options' => [
            'rating' => esc_attr_x('Rating', 'admin-text', 'site-reviews'),
            'title' => esc_attr_x('Title', 'admin-text', 'site-reviews'),
            'content' => esc_attr_x('Review', 'admin-text', 'site-reviews'),
            'name' => esc_attr_x('Name', 'admin-text', 'site-reviews'),
            'email' => esc_attr_x('Email', 'admin-text', 'site-reviews'),
            'terms' => esc_attr_x('Terms', 'admin-text', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.submissions.limit' => [
        'default' => '',
        'description' => esc_html_x('Limits the number of reviews that can be submitted to one-per-person. If you are assigning reviews, then the limit will be applied to the assigned page or category.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Limit Reviews', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('No Limit', 'admin-text', 'site-reviews'),
            'email' => esc_attr_x('By Email Address', 'admin-text', 'site-reviews'),
            'ip_address' => esc_attr_x('By IP Address', 'admin-text', 'site-reviews'),
            'username' => esc_attr_x('By Username (will only work for registered users)', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.limit_whitelist.email' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['email'],
        ],
        'description' => esc_html_x('One Email per line. All emails in the whitelist will be excluded from the review submission limit.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Email Whitelist', 'admin-text', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.limit_whitelist.ip_address' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['ip_address'],
        ],
        'description' => esc_html_x('One IP Address per line. All IP Addresses in the whitelist will be excluded from the review submission limit..', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('IP Address Whitelist', 'admin-text', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.limit_whitelist.username' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['username'],
        ],
        'description' => esc_html_x('One Username per line. All registered users with a Username in the whitelist will be excluded from the review submission limit.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Username Whitelist', 'admin-text', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.recaptcha.integration' => [
        'default' => '',
        'description' => esc_html_x('Invisible reCAPTCHA is a free anti-spam service from Google. To use it, you will need to <a href="https://www.google.com/recaptcha/admin" target="_blank">sign up</a> for an API key pair for your site.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Invisible reCAPTCHA', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('Do not use reCAPTCHA', 'admin-text', 'site-reviews'),
            'all' => esc_attr_x('Use reCAPTCHA', 'admin-text', 'site-reviews'),
            'guest' => esc_attr_x('Use reCAPTCHA only for guest users', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.recaptcha.key' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'label' => esc_html_x('Site Key', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.recaptcha.secret' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'label' => esc_html_x('Site Secret', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.recaptcha.position' => [
        'default' => 'bottomleft',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'description' => esc_html_x('This option may not work consistently if another plugin is loading reCAPTCHA on the same page as Site Reviews.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Badge Position', 'admin-text', 'site-reviews'),
        'options' => [
            'bottomleft' => esc_attr_x('Bottom Left', 'admin-text', 'site-reviews'),
            'bottomright' => esc_attr_x('Bottom Right', 'admin-text', 'site-reviews'),
            'inline' => esc_attr_x('Inline', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.akismet' => [
        'default' => 'no',
        'description' => esc_html_x('The <a href="https://akismet.com" target="_blank">Akismet plugin</a> integration provides spam-filtering for your reviews. In order for this setting to have any affect, you will need to first install and activate the Akismet plugin and set up a WordPress.com API key.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Enable Akismet Integration', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.submissions.blacklist.integration' => [
        'default' => '',
        'description' => sprintf(esc_html_x('Choose which Blacklist you would prefer to use for reviews. The %s can be found in the WordPress Discussion Settings page.', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('options-discussion.php#users_can_register').'">'.esc_attr_x('Comment Blacklist', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => esc_html_x('Blacklist', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_attr_x('Use the Site Reviews Blacklist', 'admin-text', 'site-reviews'),
            'comments' => esc_attr_x('Use the WordPress Comment Blacklist', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.blacklist.entries' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.blacklist.integration' => [''],
        ],
        'description' => esc_html_x('One entry or IP address per line. When a review contains any of these entries in its title, content, name, email, or IP address, it will be rejected. It is case-insensitive and will match partial words, so "press" will match "WordPress".', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Review Blacklist', 'admin-text', 'site-reviews'),
        'rows' => 10,
        'type' => 'code',
    ],
    'settings.submissions.blacklist.action' => [
        'default' => 'unapprove',
        'description' => esc_html_x('Choose the action that should be taken when a review is blacklisted.', 'admin-text', 'site-reviews'),
        'label' => esc_html_x('Blacklist Action', 'admin-text', 'site-reviews'),
        'options' => [
            'unapprove' => esc_attr_x('Require approval', 'admin-text', 'site-reviews'),
            'reject' => esc_attr_x('Reject submission', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
];
