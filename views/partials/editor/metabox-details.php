<?php defined( 'WPINC' ) || die; ?>

<table class="glsr-metabox-table">
	<tbody>
	<?php foreach( $metabox as $key => $value ) : ?>
		<tr>
			<td><?= $key; ?></td>
			<td><?= $value; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div class="revert-action">
	<span class="spinner"></span>
	<?= $button; ?>
</div>
