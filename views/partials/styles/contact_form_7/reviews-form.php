<?php defined( 'WPINC' ) || die; ?>

<div class="wpcf7">
	<div class="glsr-form-wrap">
		<form class="wpcf7-form {{ class }}" id="{{ id }}" method="post" enctype="multipart/form-data">
			{{ fields }}
			{{ submit_button }}
			{{ response }}
		</form>
	</div>
</div>
