<?php defined( 'WPINC' ) || die; ?>

<table class="wp-list-table widefat fixed striped glsr-status">
	<thead>
		<tr>
			<th class="site column-primary"><?= __( 'Site', 'site-reviews' ); ?></th>
			<th class="total-fetched"><?= __( 'Reviews', 'site-reviews' ); ?></th>
			<th class="last-fetch"><?= __( 'Last Sync', 'site-reviews' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach( $sites as $slug => $details ) : ?>
		<tr data-site="<?= $slug; ?>">
			<td class="site column-primary">
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
			<th class="site column-primary"><?= __( 'Site', 'site-reviews' ); ?></th>
			<th class="total-fetched"><?= __( 'Reviews', 'site-reviews' ); ?></th>
			<th class="last-fetch"><?= __( 'Last Sync', 'site-reviews' ); ?></th>
		</tr>
	</tfoot>
</table>

<form method="post">
	<div class="tablenav bottom">
		<div class="actions">
			<input type="hidden" name="{{ id }}[_action]" value="sync-reviews">
			<?php wp_nonce_field( 'sync-reviews' ); ?>
			<?php if( count( $sites ) > 1) : ?>
				<label for="bulk-action-selector-bottom" class="screen-reader-text">Select a site to sync</label>
				<select name="{{ id }}[site]">
					<option value>- <?= __( 'Select a Site', 'site-reviews' ); ?> -</option>
					<?php foreach( $sites as $slug => $details ) : ?>
					<option value="<?= $slug; ?>" class="hide-if-no-js"><?= $details['name']; ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<?php foreach( $sites as $slug => $details ) : ?>
				<input type="hidden" name="{{ id }}[site]" value="<?= $slug; ?>">
				<?php endforeach; ?>
			<?php endif; ?>
			<button type="submit" class="glsr-button button" id="sync-reviews">
				<span data-loading="<?= __( 'Syncing...', 'site-reviews' ); ?>"><?= __( 'Sync Reviews', 'site-reviews' ); ?></span>
			</button>
		</div>
	</div>
</form>


