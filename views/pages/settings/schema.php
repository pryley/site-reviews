<?php defined( 'WPINC' ) || die; ?>

<p><?= __( 'The JSON-LD schema appears in Google\'s search results and shows the star rating and other information about your reviews. If the schema has been enabled in your shortcodes, you can use Google\'s <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test your pages for valid schema data.', 'site-reviews' ); ?></p>
<p><?= __( 'You may override any of these options on a per-post/page basis by using its Custom Field name and adding a custom value using the <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a> metabox.', 'site-reviews' ); ?></p>
<table class="form-table">
	<tbody>
		{{ rows }}
	</tbody>
</table>


