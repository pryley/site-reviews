<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>Recalculate review counts</h3>
	<p>Site Reviews maintains an internal rating count of your reviews, this allows the plugin to calculate the average rating score of your reviews without negatively impacting performance.</p>
	<p>In the rare instances where the rating count becomes incorrect (perhaps you edited reviews directly in your database), you can recalculate it here.</p>
	<form method="post">
		<input type="hidden" name="{{ id }}[action]" value="count-reviews">
		<?php wp_nonce_field( 'count-reviews' ); ?>
		<button type="submit" class="glsr-button button" name="count-reviews" id="count-reviews">
			<span data-loading="<?= __( 'Recalculating Counts...', 'site-reviews' ); ?>"><?= __( 'Recalculate Counts', 'site-reviews' ); ?></span>
		</button>
	</form>
</div>
