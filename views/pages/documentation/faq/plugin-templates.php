<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-plugin-templates">
            <span class="title">How do I use the plugin templates in my theme?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-plugin-templates" class="inside">
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                Make sure to use these template files in a <a href="https://wpengine.com/resources/create-child-theme-wordpress/#The_Plugin_Method" target="_blank">child theme</a> to prevent changes from being overwritten when your parent theme is updated.
            </p>
        </div>
        <p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
        <ol>
            <li>Create a folder in your child theme called "site-reviews".</li>
            <li>Copy the template files that you would like to customise from <code>/wp-content/plugins/site-reviews/templates/</code> into this new folder, keeping the subdirectories the same.</li>
            <li>Open the template files that you copied over in a text editor and make your changes.</li>
        </ol>
        <p>For example:</p>
        <p><code>/wp-content/plugins/site-reviews/templates/form/field.php</code><br><br>
            Would be copied here:<br><br>
            <code>/wp-content/themes/&lt;your-child-theme&gt;/site-reviews/form/field.php</code>
        </p>
        <pre><code class="language-text">wp-content/plugins/site-reviews/templates
├── emails                      This folder contains the email template files.
│   └── default.php             This is the default template for HTML emails.
├── form                        This folder contains the template files for the form fields.
│   ├── field-description.php   This template displays the form field description.
│   ├── field-errors.php        This template displays the form field validation errors.
│   ├── field-label.php         This template displays the form field label.
│   ├── field.php               This template displays the form field element.
│   ├── field_{type}.php        To target a specific field type, duplicate the field.php file and append the type with an underscore (i.e. field_email.php, field_textarea.php).
│   ├── response.php            This template displays the form submission response.
│   ├── submit-button.php       This template displays the submit button.
│   ├── type-checkbox.php       This template is used by the field_checkbox.php template to display each checkbox.
│   ├── type-radio.php          This template is used by the field_radio.php template to display each radio.
│   └── type-toggle.php         This template is used by the field_toggle.php template to display each toggle switch.
├── woocommerce                 This folder contains the template files for the WooCommerce integration.
│   ├── loop
│   │   └── rating.php          This template displays the form field description.
│   ├── widgets
│   │   ├── rating-filter.php   This template is used by the WooCommerce Rating Filter widget.
│   │   └── recent-reviews.php  This template is used by the WooCommerce Recent Reviews widget.
│   ├── rating.php              This template displays the rating underneath the page title on product pages.
│   └── reviews.php             This template displays the reviews in the reviews tab on product pages.
├── load-more-button.php        This template displays the Load More button.
├── login-register.php          This template displays the login/register message.
├── notification.php            This template contains the default message contents of the notification email.
├── pagination.php              This template displays the review pagination.
├── review.php                  This template displays a single review.
├── reviews-form.php            This template displays the review form.
├── reviews-summary.php         This template displays the review summary.
└── reviews.php                 This template displays the reviews.
└── reviews.php                 This template displays the reviews.
└── verify-review.php           This template contains the default message contents of the verify review email.</code></pre>
    </div>
</div>
