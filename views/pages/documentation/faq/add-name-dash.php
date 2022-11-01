<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-add-name-dash">
            <span class="title">How do I add a dash in front of the author's name?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-add-name-dash" class="inside">
        <p>In earlier versions of Site Reviews, a "dash" character appeared in front of the author's name if you disabled avatars in the settings, hid the avatar, or changed the order of the review fields. If you want to add the dash back, simply use the following custom CSS. If your theme does not allow you to add custom CSS, you can use a plugin such as <a href="https://wordpress.org/plugins/simple-custom-css/" target="_blank">Simple Custom CSS</a>.</p>
        <pre><code class="language-css">.glsr-review-author::before {
    content: '\2014';
}</code></pre>
    </div>
</div>
