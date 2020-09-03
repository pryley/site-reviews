<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-plugin-templates">
            <span class="title">How do I use the plugin templates in my theme?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-plugin-templates" class="inside">
        <p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
        <ol>
            <li>Create a folder in your theme called "site-reviews".</li>
            <li>Copy the template files that you would like to customise from <code>/wp-content/plugins/site-reviews/templates/</code> into this new folder.</li>
            <li>Open the template files that you copied over in a text editor and make your changes.</li>
        </ol>
        <pre><code> wp-content/plugins/site-reviews/templates
 ├── form                        This folder contains the template files for the form fields
 │   ├── field-errors.php        This template displays the field errors
 │   ├── field.php               This template displays the field
 │   ├── response.php            This template displays the form submission response
 │   └── submit-button.php       This template displays the submit button
 ├── rating                      This folder contains the template files for the stars
 │   ├── empty-star.php          This template displays the empty star
 │   ├── full-star.php           This template displays the full star
 │   ├── half-star.php           This template displays the half star
 │   └── stars.php               This template displays the combined stars
 ├── email-notification.php      This template contains the content of the notification email
 ├── login-register.php          This template displays the login/register message
 ├── pagination.php              This template displays the review pagination
 ├── review.php                  This template displays a single review
 ├── reviews-form.php            This template displays the submission form
 ├── reviews-summary.php         This template displays the review summary
 └── reviews.php                 This template displays the reviews</code></pre>
    </div>
</div>
