<?php

defined( 'WPINC' ) || die;

add_action( 'site-reviews/review/submitted', function() {
	if( has_action( 'site-reviews/local/review/submitted' )) {
		glsr_log()->notice( 'The "site-reviews/local/review/submitted" hook has been deprecated. Please use the "site-reviews/review/submitted" hook instead.' );
	}
	if( has_filter( 'site-reviews/local/review/submitted/message' )) {
		glsr_log()->notice( 'The "site-reviews/local/review/submitted/message" hook has been deprecated.' );
	}
});

add_filter( 'site-reviews/create/review-values', function( $values ) {
	if( has_filter( 'site-reviews/local/review' )) {
		glsr_log()->notice( 'The "site-reviews/local/review" hook has been deprecated. Please use the "site-reviews/create/review-values" hook instead.' );
	}
	return $values;
});

add_filter( 'site-reviews/get/defaults', function( $defaults ) {
	if( has_filter( 'site-reviews/addon/defaults' )) {
		glsr_log()->notice( 'The "site-reviews/addon/defaults" hook has been deprecated. Please use the "site-reviews/get/defaults" hook instead.' );
	}
	return $defaults;
});

add_filter( 'site-reviews/rating/average', function( $average ) {
	if( has_filter( 'site-reviews/average/rating' )) {
		glsr_log()->notice( 'The "site-reviews/average/rating" hook has been deprecated. Please use the "site-reviews/rating/average" hook instead.' );
	}
	return $average;
});

add_filter( 'site-reviews/rating/ranking', function( $ranking ) {
	if( has_filter( 'site-reviews/bayesian/ranking' )) {
		glsr_log()->notice( 'The "site-reviews/bayesian/ranking" hook has been deprecated. Please use the "site-reviews/rating/ranking" hook instead.' );
	}
	return $ranking;
});

add_filter( 'site-reviews/review/build/before', function( $review ) {
	if( has_filter( 'site-reviews/rendered/review/meta/order' )) {
		glsr_log()->notice( 'The "site-reviews/rendered/review/meta/order" hook has been deprecated. Please use a custom template instead (refer to the documentation).' );
	}
	if( has_filter( 'site-reviews/rendered/review/order' )) {
		glsr_log()->notice( 'The "site-reviews/rendered/review/order" hook has been deprecated. Please use a custom template instead (refer to the documentation).' );
	}
	return $review;
});

add_filter( 'site-reviews/review/build/after', function( $review ) {
	if( has_filter( 'site-reviews/reviews/review/text' )) {
		glsr_log()->notice( 'The "site-reviews/reviews/review/text" hook has been deprecated. Please use the "site-reviews/review/build/after" hook instead.' );
	}
	if( has_filter( 'site-reviews/reviews/review/title' )) {
		glsr_log()->notice( 'The "site-reviews/reviews/review/title" hook has been deprecated. Please use the "site-reviews/review/build/after" hook instead.' );
	}
	return $review;
});

add_filter( 'site-reviews/enqueue/public/localize', function( $variables ) {
	if( has_filter( 'site-reviews/enqueue/localize' )) {
		glsr_log()->notice( 'The "site-reviews/enqueue/localize" hook has been deprecated. Please use the "site-reviews/enqueue/public/localize" hook instead.' );
	}
	return $variables;
});
