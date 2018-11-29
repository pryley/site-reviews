<?php defined( 'WPINC' ) || die; ?>

<form method="post" class="glsr-status">

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td class="check-column glsr-radio-column"><span class="dashicons-before dashicons-update"></span></td>
				<th class="column-primary"><?= __( 'Service', 'site-reviews' ); ?></th>
				<th class="total-fetched"><?= __( 'Reviews', 'site-reviews' ); ?></th>
				<th class="last-fetch"><?= __( 'Last Sync', 'site-reviews' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php $selected = key( $services ); ?>
		<?php foreach( $services as $slug => $details ) : ?>
			<tr data-service="<?= $slug; ?>">
				<th scope="row" class="check-column">
					<input type="radio" name="{{ id }}[service]" value="<?= $slug; ?>"<?php if( $slug == $selected ) echo ' checked'; ?>>
				</th>
				<td class="column-primary">
					<?= $details['name']; ?> <span>(<a href="<?= $details['settings_url']; ?>"><?= __( 'settings', 'site-reviews' ); ?></a>)</span>
					<button type="button" class="toggle-row">
						<span class="screen-reader-text"><?= __( 'Show more details', 'site-reviews' ); ?></span>
					</button>
				</td>
				<td class="total-fetched" data-colname="<?= __( 'Reviews', 'site-reviews' ); ?>">
					<a href="<?= $details['reviews_url']; ?>"><?= $details['reviews_count']; ?></a>
				</td>
				<td class="last-fetch" data-colname="<?= __( 'Last Sync', 'site-reviews' ); ?>">
					<?= $details['last_sync']; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan=4" class="no-items" style="display:table-cell!important;">
					<?php
						$inactive = __( 'Inactive', 'site-reviews' );
						$active = __( 'Please wait...', 'site-reviews' );
					?>
					<div class="glsr-progress" data-inactive-text="<?= $inactive ?>" data-active-text="<?= $active ?>">
						<div class="glsr-progress-bar glsr-progress-bar-1" style="width: 0%;">
							<span class="glsr-progress-status"><?= $inactive; ?></span>
						</div>
						<div class="glsr-progress-background">
							<span class="glsr-progress-status"><?= $inactive; ?></span>
						</div>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>

	<div class="tablenav bottom">
		<button type="submit" class="glsr-button button" id="sync-reviews">
			<span data-loading="<?= __( 'Syncing...', 'site-reviews' ); ?>"><?= __( 'Sync Reviews', 'site-reviews' ); ?></span>
		</button>
	</div>
</form>


