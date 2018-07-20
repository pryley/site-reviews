<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-form-wrap nf-form-wrap ninja-forms-form-wrap">
	<div class="nf-form-layout">
		<form class="{{ class }}" id="{{ id }}" method="post" enctype="multipart/form-data">
			<div class="nf-form-content">
				{{ fields }}
				{{ submit_button }}
			</div>
			<div class="nf-after-form-content">
				{{ response }}
			</div>
		</form>
	</div>
</div>
