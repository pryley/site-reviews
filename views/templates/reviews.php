<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-reviews-wrap">
	<div class="glsr-reviews {{ class }}" id="{{ id }}">
		<?php foreach( $reviews as $review ) : ?>
		<div class="glsr-review">
			<?= $review->title; ?>
			<?= $review->rating; ?>
			<?= $review->date; ?>
			<?= $review->assigned_to; ?>
			<?= $review->content; ?>
			<?= $review->avatar; ?>
			<?= $review->author; ?>
			<?= $review->response; ?>
		</div>
		<?php endforeach; ?>
		{{ navigation }}
	</div>
</div>
