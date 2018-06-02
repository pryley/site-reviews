<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-addon-wrap">
	<div class="glsr-addon">
		<a href="{{ link }}" class="glsr-addon-screenshot" data-name="{{ name }}">
			<span class="screen-reader-text">{{ name }}</span>
		</a>
		<div class="glsr-addon-description">
			<h3 class="glsr-addon-name">{{ title }}</h3>
			<p>{{ description }}</p>
		</div>
		<div class="glsr-addon-footer">
			<a href="{{ link }}" class="glsr-addon-link button button-secondary">
				<?= __( 'More Info', 'site-reviews' ); ?>
			</a>
		</div>
	</div>
</div>
