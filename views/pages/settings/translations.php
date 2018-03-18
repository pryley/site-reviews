<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-search-box" id="glsr-search-translations">
	<span class="screen-reader-text"><?= __( 'Search for translatable text', 'site-reviews' ); ?></span>
	<div class="glsr-spinner"><span class="spinner"></span></div>
	<input type="search" class="glsr-search-input" autocomplete="off" placeholder="<?= __( 'Search for translatable text...', 'site-reviews' ); ?>">
	<?php wp_nonce_field( 'search-translations' ); ?>
	<div class="glsr-search-results" data-prefix="<?= $databaseKey; ?>"></div>
</div>

<?php $html->renderForm( $currentTab.'.'.$currentSection ); ?>

<script type="text/html" id="tmpl-glsr-string-plural">
<?php include glsr()->path( 'views/translations/plural.php' ); ?>
</script>
<script type="text/html" id="tmpl-glsr-string-single">
<?php include glsr()->path( 'views/translations/single.php' ); ?>
</script>
