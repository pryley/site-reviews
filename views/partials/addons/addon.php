<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-addon-wrap">
	<div class="glsr-addon">
		<a href="{{ link }}" class="glsr-addon-screenshot" data-name="{{ name }}">{{ name }}</a>
		<div class="glsr-addon-description">
			<p>{{ description }}</p>
		</div>
		<h3 class="glsr-addon-name">{{ title }}</h3>
		<a href="{{ link }}" class="glsr-addon-link button button-secondary">
			<?= __( 'More Info', 'site-reviews' ); ?>
		</a>
	</div>
</div>
