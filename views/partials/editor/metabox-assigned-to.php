<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-search-box" id="glsr-search-posts">
	<span class="glsr-spinner"><span class="spinner"></span></span>
	<input type="hidden" id="assigned_to" name="assigned_to" value="<?= $id; ?>">
	<input type="search" class="glsr-search-input" autocomplete="off" placeholder="<?= __( 'Type to search...', 'site-reviews' ); ?>">
	<?php wp_nonce_field( 'search-posts', '_search_nonce', false ); ?>
	<span class="glsr-search-results"></span>
	<p><?= __( 'Search here for a page or post that you would like to assign this review to. You may search by title or ID.', 'site-reviews' ); ?></p>
	<span class="description"><?= $template; ?></span>
</div>

<script type="text/html" id="tmpl-glsr-assigned-post">
<?php include glsr()->path( 'views/partials/editor/assigned-post.php' ); ?>
</script>
