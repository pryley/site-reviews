<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-hide-empty-review-titles">
            <span class="title">How do I hide empty review titles?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-empty-review-titles" class="inside">
        <ol>
            <li>Go to the <?php echo glsr_admin_link('settings.strings'); ?> page and search for "No Title".</li>
            <li>Add a single space character as the Custom Text.</li>
            <li>Click the "Save Changes" button.</li>
        </ol>
    </div>
</div>
