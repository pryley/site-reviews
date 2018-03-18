<?php defined( 'WPINC' ) || die; ?>

<label class="screen-reader-text" for="response"><?= __( 'Respond Publicly', 'site-reviews' ); ?></label>
<textarea class="glsr-response" name="response" id="response" rows="1" cols="40"><?= $response; ?></textarea>
<p>If you need to publicly respond to this review, enter your response here.</p>
