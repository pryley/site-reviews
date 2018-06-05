<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-form-wrap">
	<form class="{{ class }}" id="{{ id }}" method="post" enctype="multipart/form-data">
		<?php foreach( $fields as $field ) : ?>
			<?= $field; ?>
		<?php endforeach; ?>
		{{ submit_button }}
		{{ results }}
	</form>
</div>
