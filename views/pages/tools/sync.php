<?php defined( 'WPINC' ) || die; ?>

<form method="post" class="glsr-status">
	<?php $selected = key( $services ); ?>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td class="check-column glsr-radio-column"><span class="dashicons-before dashicons-update"></span></td>
				<th scope="col" class="column-primary"><?= __( 'Service', 'site-reviews' ); ?></th>
				<th scope="col" class="column-total_fetched"><?= __( 'Reviews', 'site-reviews' ); ?></th>
				<th scope="col" class="column-last_sync"><?= __( 'Last Sync', 'site-reviews' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $services as $slug => $details ) : ?>
			<tr class="service-<?= $slug; ?>">
				<th scope="row" class="check-column">
					<input type="radio" name="{{ id }}[service]" value="<?= $slug; ?>" <?php checked( $slug, $selected ); ?>>
				</th>
				<td class="column-primary has-row-actions">
					<strong><?= $details['name']; ?></strong>
					<div class="row-actions">
						<span><a href="{{ base_url }}&page=settings#!addons"><?= __( 'Settings', 'site-reviews' ); ?></a> | </span>
						<span><a href="{{ base_url }}&page=settings#!licenses"><?= __( 'License', 'site-reviews' ); ?></a> | </span>
						<span><a href="{{ base_url }}&page=documentation#!addons"><?= __( 'Documentation', 'site-reviews' ); ?></a></span>
					</div>
					<button type="button" class="toggle-row">
						<span class="screen-reader-text"><?= __( 'Show more details', 'site-reviews' ); ?></span>
					</button>
				</td>
				<td class="column-total_fetched" data-colname="<?= __( 'Reviews', 'site-reviews' ); ?>">
					<a href="<?= $details['reviews_url']; ?>"><?= $details['reviews_count']; ?></a>
				</td>
				<td class="column-last_sync" data-colname="<?= __( 'Last Sync', 'site-reviews' ); ?>">
					<?= $details['last_sync']; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" class="no-items" style="display:table-cell!important;">
					<div class="glsr-progress" data-active-text="<?= __( 'Please wait...', 'site-reviews' ); ?>">
						<div class="glsr-progress-bar" style="width: 0%;">
							<span class="glsr-progress-status"><?= __( 'Inactive', 'site-reviews' ); ?></span>
						</div>
						<div class="glsr-progress-background">
							<span class="glsr-progress-status"><?= __( 'Inactive', 'site-reviews' ); ?></span>
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
