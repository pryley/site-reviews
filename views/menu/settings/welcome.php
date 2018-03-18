<?php defined( 'WPINC' ) || die; ?>

<div id="welcome-panel" class="welcome-panel">
	<a class="welcome-panel-close" href="<?= admin_url( 'edit.php?post_type=site-review&page=site-reviews&welcome=0' ); ?>" aria-label="Dismiss the welcome panel">Dismiss</a>
	<div class="welcome-panel-content">

		<h2>Thank you for installing Reviews!</h2>
		<p class="about-description">Here is how to get started:</p>

		<div class="welcome-panel-column-container">

			<div class="welcome-panel-column" style="width:68%;">
				<h3>Get Started</h3>
				<p></p>
			</div>

			<div class="welcome-panel-column" style="width:32%;">
				<h3>More Actions</h3>
				<ul>
					<li><div class="welcome-icon welcome-widgets-menus">Manage <a href="<?= admin_url( 'widgets.php' ); ?>">widgets</a></div></li>
				</ul>
			</div>

		</div>
	</div>
</div>
