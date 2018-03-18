<?php defined( 'WPINC' ) || die; ?>

<div id="misc-pub-pinned" class="misc-pub-section misc-pub-pinned">

<?php
	global $wp_version;

	// WP < 4.4 support
	if( version_compare( $wp_version, '4.4', '<' ) ) {
		printf( '<span class="pinned-icon">%s</span>', file_get_contents( glsr_app()->path . 'assets/img/pinned.svg' ) );
	}

	$pinnedNo  = __( 'No', 'site-reviews' );
	$pinnedYes = __( 'Yes', 'site-reviews' );
?>

	<label for="pinned-status"><?= __( 'Pinned', 'site-reviews' ) ?>:</label>
	<span id="pinned-status-text" class="pinned-status-text"><?= $pinned ? $pinnedYes : $pinnedNo; ?></span>

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

		<a href="#pinned-status" class="save-pinned-status hide-if-no-js button" data-no="<?= $pinnedNo; ?>" data-yes="<?= $pinnedYes; ?>"><?= __( 'OK', 'site-reviews' ); ?></a>
		<a href="#pinned-status" class="cancel-pinned-status hide-if-no-js button-cancel"><?= __( 'Cancel', 'site-reviews' ); ?></a>

	</div>

</div>
