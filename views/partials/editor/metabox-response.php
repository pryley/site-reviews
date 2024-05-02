<?php defined('ABSPATH') || exit; ?>

<label class="screen-reader-text" for="response">
    <?php echo esc_html_x('Respond Publicly', 'admin-text', 'site-reviews'); ?>
</label>
<textarea class="glsr-response" name="response" id="response" rows="1" cols="40"><?php echo esc_html($response); ?></textarea>
<div class="glsr-metabox-footer">
    <p>
        <?php echo esc_html_x('If you need to publicly respond to this review, enter your response here.', 'admin-text', 'site-reviews'); ?>
    </p>
</div>
