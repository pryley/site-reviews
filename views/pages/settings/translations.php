<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-strings-form">
	<div class="glsr-search-box" id="glsr-search-translations">
		<span class="screen-reader-text"><?= __( 'Search for translatable text', 'site-reviews' ); ?></span>
		<div class="glsr-spinner">
			<span class="spinner"></span>
		</div>
		<input type="search" class="glsr-search-input" autocomplete="off" placeholder="<?= __( 'Search here for text to translate...', 'site-reviews' ); ?>">
		<?php wp_nonce_field( 'search-translations', '_search_nonce', false ); ?>
		<div class="glsr-search-results" data-prefix="{{ database_key }}"></div>
	</div>
	<table class="glsr-strings-table wp-list-table widefat {{ class }}">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-primary"><?= __( 'Original Text', 'site-reviews' ); ?></th>
				<th scope="col" class="manage-column"><?= __( 'Custom Translation', 'site-reviews' ); ?></th>
			</tr>
		</thead>
		<tbody>{{ translations }}</tbody>
	</table>
	<input type="hidden" name="{{ database_key }}[settings][strings][]">
</div>

<script type="text/html" id="tmpl-glsr-string-plural">
<?php include glsr()->path( 'views/partials/translations/plural.php' ); ?>
</script>
<script type="text/html" id="tmpl-glsr-string-single">
<?php include glsr()->path( 'views/partials/translations/single.php' ); ?>
</script>
