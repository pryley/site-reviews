<?php

return [
    'settings.general.delete_data_on_uninstall' => [
        'class' => 'regular-text',
        'data-glsr-track' => '',
        'default' => '',
        'description' => _x('Site Reviews will not delete anything when uninstalled unless you change this setting.', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Do not delete anything', 'admin-text', 'site-reviews'),
            'minimal' => _x('Delete all plugin settings, widgets settings, and caches', 'admin-text', 'site-reviews'),
            'all' => _x('Delete everything (including all reviews and categories)', 'admin-text', 'site-reviews'),
        ],
        'label' => _x('Delete data on uninstall', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.general.style' => [
        'class' => 'regular-text',
        'default' => 'default',
        'description' => _x('Site Reviews relies on the CSS of your theme to style the review form. If your theme does not provide proper CSS rules for form elements and you are using a WordPress plugin/theme or CSS Framework listed here, please try selecting it, otherwise choose "Site Reviews (default)".', 'admin-text', 'site-reviews'),
        'label' => _x('Plugin Style', 'admin-text', 'site-reviews'),
        'options' => [
            'bootstrap_4' => _x('CSS Framework: Bootstrap 4', 'admin-text', 'site-reviews'),
            'bootstrap_4_custom' => _x('CSS Framework: Bootstrap 4 (Custom Forms)', 'admin-text', 'site-reviews'),
            'contact_form_7' => _x('Plugin: Contact Form 7 (v5)', 'admin-text', 'site-reviews'),
            'ninja_forms' => _x('Plugin: Ninja Forms (v3)', 'admin-text', 'site-reviews'),
            'wpforms' => _x('Plugin: WPForms Lite (v1)', 'admin-text', 'site-reviews'),
            'default' => _x('Site Reviews (default)', 'admin-text', 'site-reviews'),
            'minimal' => _x('Site Reviews (minimal)', 'admin-text', 'site-reviews'),
            'divi' => _x('Theme: Divi (v3)', 'admin-text', 'site-reviews'),
            'twentyfifteen' => _x('Theme: Twenty Fifteen', 'admin-text', 'site-reviews'),
            'twentysixteen' => _x('Theme: Twenty Sixteen', 'admin-text', 'site-reviews'),
            'twentyseventeen' => _x('Theme: Twenty Seventeen', 'admin-text', 'site-reviews'),
            'twentynineteen' => _x('Theme: Twenty Nineteen', 'admin-text', 'site-reviews'),
            'twentytwenty' => _x('Theme: Twenty Twenty', 'admin-text', 'site-reviews'),
            'twentytwentyone' => _x('Theme: Twenty Twenty-One', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.general.require.approval' => [
        'default' => 'no',
        'description' => _x('Set the status of new review submissions to "unapproved".', 'admin-text', 'site-reviews'),
        'label' => _x('Require Approval', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.require.login' => [
        'default' => 'no',
        'description' => _x('Only allow review submissions from registered users.', 'admin-text', 'site-reviews'),
        'label' => _x('Require Login', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.require.login_register' => [
        'default' => 'no',
        'depends_on' => [
            'settings.general.require.login' => 'yes',
        ],
        'description' => sprintf(_x('Show a link for a new user to register. The %s Membership option must be enabled in General Settings for this to work.', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('options-general.php#users_can_register').'">'._x('Anyone can register', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => _x('Show Registration Link', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.general.multilingual' => [
        'class' => 'regular-text',
        'default' => '',
        'description' => _x('Integrate with a multilingual plugin to calculate ratings for all languages of a post.', 'admin-text', 'site-reviews'),
        'label' => _x('Multilingual', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('No Integration', 'admin-text', 'site-reviews'),
            'polylang' => _x('Integrate with Polylang', 'admin-text', 'site-reviews'),
            'wpml' => _x('Integrate with WPML', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.general.notifications' => [
        'default' => [],
        'label' => _x('Notifications', 'admin-text', 'site-reviews'),
        'options' => [
            'admin' => _x('Send to administrator', 'admin-text', 'site-reviews').' <code>'.(string) get_option('admin_email').'</code>',
            'author' => _x('Send to author of the page that the review is assigned to', 'admin-text', 'site-reviews'),
            'custom' => _x('Send to one or more email addresses', 'admin-text', 'site-reviews'),
            'slack' => _x('Send to <a href="https://slack.com/">Slack</a>', 'admin-text', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.general.notification_from' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['admin', 'author', 'custom', 'slack'],
        ],
        'description' => _x('If emails are not sending, make sure this email uses the same domain as your website.', 'admin-text', 'site-reviews'),
        'label' => _x('Send Emails From', 'admin-text', 'site-reviews'),
        'placeholder' => get_option('admin_email'),
        'type' => 'text',
    ],
    'settings.general.notification_email' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['custom'],
        ],
        'label' => _x('Send Emails To', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('Separate multiple emails with a comma', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.notification_slack' => [
        'default' => '',
        'depends_on' => [
            'settings.general.notifications' => ['slack'],
        ],
        'description' => sprintf(_x('To send notifications to Slack, create a new %s and then paste the provided Webhook URL in the field above.', 'admin-text', 'site-reviews'),
            '<a href="https://api.slack.com/incoming-webhooks">'._x('Incoming WebHook', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => _x('Slack Webhook URL', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.general.notification_message' => [
        'default' => glsr('Modules\Html\Template')->build('templates/email-notification'),
        'depends_on' => [
            'settings.general.notifications' => ['admin', 'author', 'custom', 'slack'],
        ],
        'description' => _x(
            'To restore the default text, save an empty template. If you are sending notifications to Slack then this template will only be used as a fallback in the event that <a href="https://api.slack.com/docs/attachments">Message Attachments</a> have been disabled. Available template tags:',
            'admin-text',
            'site-reviews'
        ).glsr('Modules\Html\TemplateTags')->getDescription(),
        'label' => _x('Notification Template', 'admin-text', 'site-reviews'),
        'rows' => 10,
        'type' => 'code',
    ],
    'settings.reviews.date.format' => [
        'class' => 'regular-text',
        'default' => '',
        'description' => sprintf(_x('The default date format is the one set in your %s.', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('options-general.php#date_format_custom').'">'._x('WordPress settings', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => _x('Date Format', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Use the default date format', 'admin-text', 'site-reviews'),
            'relative' => _x('Use a relative date format', 'admin-text', 'site-reviews'),
            'custom' => _x('Use a custom date format', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.date.custom' => [
        'default' => get_option('date_format'),
        'depends_on' => [
            'settings.reviews.date.format' => 'custom',
        ],
        'description' => _x('Enter a custom date format (<a href="https://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>).', 'admin-text', 'site-reviews'),
        'label' => _x('Custom Date Format', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.reviews.name.format' => [
        'class' => 'regular-text',
        'default' => '',
        'description' => _x('Choose how names are shown in your reviews.', 'admin-text', 'site-reviews'),
        'label' => _x('Name Format', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Use the name as given', 'admin-text', 'site-reviews'),
            'first' => _x('Use the first name only', 'admin-text', 'site-reviews'),
            'first_initial' => _x('Convert first name to an initial', 'admin-text', 'site-reviews'),
            'last_initial' => _x('Convert last name to an initial', 'admin-text', 'site-reviews'),
            'initials' => _x('Convert to all initials', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.name.initial' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.reviews.name.format' => ['first_initial', 'last_initial', 'initials'],
        ],
        'description' => _x('Choose how the initial is displayed.', 'admin-text', 'site-reviews'),
        'label' => _x('Initial Format', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Initial with a space', 'admin-text', 'site-reviews'),
            'period' => _x('Initial with a period', 'admin-text', 'site-reviews'),
            'period_space' => _x('Initial with a period and a space', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.assignment' => [
        'class' => 'regular-text',
        'default' => 'strict',
        'description' => sprintf('%s<br>%s<br>%s',
            _x('This setting determines how the assigned options work in the reviews and summary shortcodes and blocks.', 'admin-text', 'site-reviews'),
            _x('"Loose Assignment" means <code>display reviews that are assigned to this OR this</code>.', 'admin-text', 'site-reviews'),
            _x('"Strict Assignment" means <code>display reviews that are assigned to this AND this</code>.', 'admin-text', 'site-reviews')
        ),
        'label' => _x('Review Assignment', 'admin-text', 'site-reviews'),
        'options' => [
            'loose' => _x('Loose Assignment', 'admin-text', 'site-reviews'),
            'strict' => _x('Strict Assignment', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.assigned_links' => [
        'default' => 'no',
        'description' => _x('Display a link to the assigned posts of a review.', 'admin-text', 'site-reviews'),
        'label' => _x('Enable Assigned Links', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars' => [
        'default' => 'no',
        'description' => _x('Display reviewer avatars. These are generated from the email address of the reviewer using <a href="https://gravatar.com">Gravatar</a>.', 'admin-text', 'site-reviews'),
        'label' => _x('Enable Avatars', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars_regenerate' => [
        'default' => 'no',
        'depends_on' => [
            'settings.reviews.avatars' => 'yes',
        ],
        'description' => _x('Regenerate the avatar whenever a local review is shown?', 'admin-text', 'site-reviews'),
        'label' => _x('Regenerate Avatars', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.avatars_size' => [
        'after' => _x('pixels', 'avatar size (admin-text)', 'site-reviews'),
        'default' => 40,
        'depends_on' => [
            'settings.reviews.avatars' => 'yes',
        ],
        'description' => _x('Set the height/width of the avatar in pixels.', 'admin-text', 'site-reviews'),
        'label' => _x('Avatar Size', 'admin-text', 'site-reviews'),
        'type' => 'number',
    ],
    'settings.reviews.excerpts' => [
        'default' => 'yes',
        'description' => _x('Display an excerpt instead of the full review.', 'admin-text', 'site-reviews'),
        'label' => _x('Enable Excerpts', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.excerpts_action' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.reviews.excerpts' => 'yes',
        ],
        'description' => 'The action that is performed when you click the "Read more" link',
        'label' => _x('Excerpt Action', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Collapse/Expand the review', 'admin-text', 'site-reviews'),
            'modal' => _x('Display the review in a modal', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.reviews.excerpts_length' => [
        'after' => _x('words', 'exceprt length (admin-text)', 'site-reviews'),
        'default' => 55,
        'depends_on' => [
            'settings.reviews.excerpts' => 'yes',
        ],
        'description' => _x('Set the excerpt word length.', 'admin-text', 'site-reviews'),
        'label' => _x('Excerpt Length', 'admin-text', 'site-reviews'),
        'type' => 'number',
    ],
    'settings.reviews.fallback' => [
        'default' => 'yes',
        'description' => sprintf(_x('Display the fallback text when there are no reviews to display. This can be changed on the %s page. You may also override this by using the "fallback" option on the shortcode. The default fallback text is: %s', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-translations').'">'._x('Translations', 'admin-text', 'site-reviews').'</a>',
            '<code>'.__('There are no reviews yet. Be the first one to write one.', 'site-reviews').'</code>'
        ),
        'label' => _x('Enable Fallback Text', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.reviews.pagination.url_parameter' => [
        'default' => 'yes',
        'description' => sprintf(
            _x('Paginated URLs include the %s URL parameter. If you would like to keep the pagination links but prevent search engines from indexing them, add the following lines to your %s file instead: %s', 'admin-text', 'site-reviews'),
            '<code>?'.glsr()->constant('PAGED_QUERY_VAR').'={page_number}</code>',
            '<a href="https://www.robotstxt.org/">robots.txt</a>',
            '<br><code>user-agent: *</code>'.
            '<br><code>Disallow: /*?'.glsr()->constant('PAGED_QUERY_VAR').'=*</code>'.
            '<br><code>Disallow: /*?*'.glsr()->constant('PAGED_QUERY_VAR').'=*</code>'
        ),
        'label' => esc_html_x('Enable Paginated URLs', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.schema.type.default' => [
        'class' => 'regular-text',
        'default' => 'LocalBusiness',
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_type</code>',
        'label' => _x('Default Schema Type', 'admin-text', 'site-reviews'),
        'options' => [
            'LocalBusiness' => _x('Local Business', 'admin-text', 'site-reviews'),
            'Product' => _x('Product', 'admin-text', 'site-reviews'),
            'custom' => _x('Custom', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.type.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'custom',
        ],
        'description' => '<a href="https://schema.org/docs/schemas.html">'._x('View more information on schema types here', 'admin-text', 'site-reviews').'</a>',
        'label' => _x('Custom Schema Type', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.name.default' => [
        'class' => 'regular-text',
        'default' => 'post',
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_name</code>',
        'label' => _x('Default Name', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => _x('Use the assigned or current page title', 'admin-text', 'site-reviews'),
            'custom' => _x('Enter a custom title', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.name.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.name.default' => 'custom',
        ],
        'label' => _x('Custom Name', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.description.default' => [
        'class' => 'regular-text',
        'default' => 'post',
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_description</code>',
        'label' => _x('Default Description', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => _x('Use the assigned or current page excerpt', 'admin-text', 'site-reviews'),
            'custom' => _x('Enter a custom description', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.description.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.description.default' => 'custom',
        ],
        'label' => _x('Custom Description', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.url.default' => [
        'class' => 'regular-text',
        'default' => 'post',
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_url</code>',
        'label' => _x('Default URL', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => _x('Use the assigned or current page URL', 'admin-text', 'site-reviews'),
            'custom' => _x('Enter a custom URL', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.url.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.url.default' => 'custom',
        ],
        'label' => _x('Custom URL', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.image.default' => [
        'class' => 'regular-text',
        'default' => 'post',
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_image</code>',
        'label' => _x('Default Image', 'admin-text', 'site-reviews'),
        'options' => [
            'post' => _x('Use the featured image of the assigned or current page', 'admin-text', 'site-reviews'),
            'custom' => _x('Enter a custom image URL', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.image.custom' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.image.default' => 'custom',
        ],
        'label' => _x('Custom Image URL', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.address' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_address</code>',
        'label' => _x('Address', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('60 29th Street #343, San Francisco, CA 94110, US', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.telephone' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_telephone</code>',
        'label' => _x('Telephone Number', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('+1 (877) 273-3049', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.pricerange' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'LocalBusiness',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_pricerange</code>',
        'label' => _x('Price Range', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('$$-$$$', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.schema.offertype' => [
        'class' => 'regular-text',
        'default' => 'AggregateOffer',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_offertype</code>',
        'label' => _x('Offer Type', 'admin-text', 'site-reviews'),
        'options' => [
            'AggregateOffer' => _x('AggregateOffer', 'admin-text', 'site-reviews'),
            'Offer' => _x('Offer', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.schema.price' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'Offer',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_price</code>',
        'label' => _x('Price', 'admin-text', 'site-reviews'),
        'placeholder' => '50.00',
        'type' => 'text',
    ],
    'settings.schema.lowprice' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'AggregateOffer',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_lowprice</code>',
        'label' => _x('Low Price', 'admin-text', 'site-reviews'),
        'placeholder' => '10.00',
        'type' => 'text',
    ],
    'settings.schema.highprice' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
            'settings.schema.offertype' => 'AggregateOffer',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_highprice</code>',
        'label' => _x('High Price', 'admin-text', 'site-reviews'),
        'placeholder' => '100.00',
        'type' => 'text',
    ],
    'settings.schema.pricecurrency' => [
        'default' => '',
        'depends_on' => [
            'settings.schema.type.default' => 'Product',
        ],
        'description' => _x('Custom Field name', 'admin-text', 'site-reviews').': <code>schema_pricecurrency</code>',
        'label' => _x('Price Currency', 'admin-text', 'site-reviews'),
        'placeholder' => esc_attr_x('USD', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.required' => [
        'default' => ['content', 'email', 'name', 'rating', 'terms', 'title'],
        'description' => _x('Choose which fields should be required in the review form.', 'admin-text', 'site-reviews'),
        'label' => _x('Required Fields', 'admin-text', 'site-reviews'),
        'options' => [
            'rating' => _x('Rating', 'admin-text', 'site-reviews'),
            'title' => _x('Title', 'admin-text', 'site-reviews'),
            'content' => _x('Review', 'admin-text', 'site-reviews'),
            'name' => _x('Name', 'admin-text', 'site-reviews'),
            'email' => _x('Email', 'admin-text', 'site-reviews'),
            'terms' => _x('Terms', 'admin-text', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.submissions.limit' => [
        'class' => 'regular-text',
        'default' => '',
        'description' => _x('Limits the number of reviews that can be submitted to one-per-person.', 'admin-text', 'site-reviews'),
        'label' => _x('Limit Reviews', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('No Limit', 'admin-text', 'site-reviews'),
            'email' => _x('By Email Address', 'admin-text', 'site-reviews'),
            'ip_address' => _x('By IP Address', 'admin-text', 'site-reviews'),
            'username' => _x('By Username (will only work for registered users)', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.limit_assignments' => [
        'default' => ['assigned_posts'],
        'depends_on' => [
            'settings.submissions.limit' => ['email', 'ip_address', 'username'],
        ],
        'description' => sprintf('%s %s',
            _x('Select which assignments the review limits should be restricted to.', 'admin-text', 'site-reviews'),
            _x('The review limits use strict assignment, i.e. <code>restrict limits to reviews assigned to this AND this</code>.', 'admin-text', 'site-reviews')
        ),
        'label' => _x('Restrict Limits To', 'admin-text', 'site-reviews'),
        'options' => [
            'assigned_posts' => _x('Assigned Posts', 'admin-text', 'site-reviews'),
            'assigned_terms' => _x('Assigned Terms', 'admin-text', 'site-reviews'),
            'assigned_users' => _x('Assigned Users', 'admin-text', 'site-reviews'),
        ],
        'type' => 'checkbox',
    ],
    'settings.submissions.limit_whitelist.email' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['email'],
        ],
        'description' => _x('One Email per line. All emails in the whitelist will be excluded from the review submission limit.', 'admin-text', 'site-reviews'),
        'label' => _x('Email Whitelist', 'admin-text', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.limit_whitelist.ip_address' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['ip_address'],
        ],
        'description' => _x('One IP Address per line. All IP Addresses in the whitelist will be excluded from the review submission limit..', 'admin-text', 'site-reviews'),
        'label' => _x('IP Address Whitelist', 'admin-text', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.limit_whitelist.username' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.limit' => ['username'],
        ],
        'description' => _x('One Username per line. All registered users with a Username in the whitelist will be excluded from the review submission limit.', 'admin-text', 'site-reviews'),
        'label' => _x('Username Whitelist', 'admin-text', 'site-reviews'),
        'rows' => 5,
        'type' => 'code',
    ],
    'settings.submissions.recaptcha.integration' => [
        'class' => 'regular-text',
        'default' => '',
        'description' => _x('The Invisible reCAPTCHA badge (reCAPTCHA v2) is a free anti-spam service from Google. To use it, you will need to <a href="https://www.google.com/recaptcha/admin" target="_blank">sign up</a> for an API key pair for your site.', 'admin-text', 'site-reviews'),
        'label' => _x('Invisible reCAPTCHA', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Do not use reCAPTCHA', 'admin-text', 'site-reviews'),
            'all' => _x('Use reCAPTCHA', 'admin-text', 'site-reviews'),
            'guest' => _x('Use reCAPTCHA only for guest users', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.recaptcha.key' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'label' => _x('Site Key', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.recaptcha.secret' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'label' => _x('Site Secret', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.submissions.recaptcha.position' => [
        'class' => 'regular-text',
        'default' => 'bottomleft',
        'depends_on' => [
            'settings.submissions.recaptcha.integration' => ['all', 'guest'],
        ],
        'description' => _x('This option may not work consistently if another plugin is loading reCAPTCHA on the same page as Site Reviews.', 'admin-text', 'site-reviews'),
        'label' => _x('Badge Position', 'admin-text', 'site-reviews'),
        'options' => [
            'bottomleft' => _x('Bottom Left', 'admin-text', 'site-reviews'),
            'bottomright' => _x('Bottom Right', 'admin-text', 'site-reviews'),
            'inline' => _x('Inline', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.akismet' => [
        'default' => 'no',
        'description' => _x('The <a href="https://akismet.com" target="_blank">Akismet plugin</a> integration provides spam-filtering for your reviews. In order for this setting to have any affect, you will need to first install and activate the Akismet plugin and set up a WordPress.com API key.', 'admin-text', 'site-reviews'),
        'label' => _x('Enable Akismet Integration', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.submissions.blacklist.integration' => [
        'class' => 'regular-text',
        'default' => 'comments',
        'description' => sprintf(_x('Choose which Blacklist you would prefer to use for reviews. The %s option can be found in the WordPress Discussion Settings page.', 'admin-text', 'site-reviews'),
            '<a href="'.admin_url('options-discussion.php').'">'._x('Disallowed Comment Keys', 'admin-text', 'site-reviews').'</a>'
        ),
        'label' => _x('Blacklist', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Use the Site Reviews Blacklist', 'admin-text', 'site-reviews'),
            'comments' => _x('Use the WordPress Disallowed Comment Keys', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'settings.submissions.blacklist.entries' => [
        'default' => '',
        'depends_on' => [
            'settings.submissions.blacklist.integration' => [''],
        ],
        'description' => _x('One entry or IP address per line. When a review contains any of these entries in its title, content, name, email, or IP address, it will be rejected. It is case-insensitive and will match partial words, so "press" will match "WordPress".', 'admin-text', 'site-reviews'),
        'label' => _x('Review Blacklist', 'admin-text', 'site-reviews'),
        'rows' => 10,
        'type' => 'code',
    ],
    'settings.submissions.blacklist.action' => [
        'class' => 'regular-text',
        'default' => 'unapprove',
        'description' => _x('Choose the action that should be taken when a review is blacklisted.', 'admin-text', 'site-reviews'),
        'label' => _x('Blacklist Action', 'admin-text', 'site-reviews'),
        'options' => [
            'unapprove' => _x('Require approval', 'admin-text', 'site-reviews'),
            'reject' => _x('Reject submission', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
];
