<?php defined( 'WPINC' ) || die; ?>

<div id="misc-pub-pinned" class="misc-pub-section misc-pub-pinned">
	<label for="pinned-status"><?= __( 'Pinned', 'site-reviews' ) ?>:</label>
	<span id="pinned-status-text" class="pinned-status-text"><?= $pinned ? $context['yes'] : $context['no']; ?></span>
	<a href="#pinned-status" class="edit-pinned-status hide-if-no-js">
		<span aria-hidden="true"><?= __( 'Edit', 'site-reviews' ); ?></span>
		<span class="screen-reader-text"><?= __( 'Edit pinned status', 'site-reviews' ); ?></span>
	</a>
	<div id="pinned-status-select" class="pinned-status-select hide-if-js">
		<input type="hidden" id="hidden-pinned-status" value="<?= intval( $pinned ); ?>">
		<select name="pinned" id="pinned-status">
			<option value="1"<?php selected( $pinned, false ); ?>><?= __( 'Pin', 'site-reviews' ); ?></option>
			<option value="0"<?php selected( $pinned, true ); ?>><?= __( 'Unpin', 'site-reviews' ); ?></option>
		</select>
		<a href="#pinned-status" class="save-pinned-status hide-if-no-js button" data-no="{{ no }}" data-yes="{{ yes }}"><?= __( 'OK', 'site-reviews' ); ?></a>
		<a href="#pinned-status" class="cancel-pinned-status hide-if-no-js button-cancel"><?= __( 'Cancel', 'site-reviews' ); ?></a>
	</div>
</div>
