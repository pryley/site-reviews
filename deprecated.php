<?php

defined( 'WPINC' ) || die;

// Database/ReviewManager.php
add_action( 'site-reviews/review/created', function( $review ) {
	if( has_action( 'site-reviews/local/review/create' )) {
		glsr()->deprecated[] = 'The "site-reviews/local/review/create" hook has been deprecated. Please use the "site-reviews/review/created" hook instead.';
		do_action( 'site-reviews/local/review/create', (array)get_post( $review->ID ), (array)$review, $review->ID );
	}
}, 9 );

// Handlers/CreateReview.php
add_action( 'site-reviews/review/submitted', function( $review ) {
	if( has_action( 'site-reviews/local/review/submitted' )) {
		glsr()->deprecated[] = 'The "site-reviews/local/review/submitted" hook has been deprecated. Please use the "site-reviews/review/submitted" hook instead.';
		do_action( 'site-reviews/local/review/submitted', null, $review );
	}
	if( has_filter( 'site-reviews/local/review/submitted/message' )) {
		glsr()->deprecated[] = 'The "site-reviews/local/review/submitted/message" hook has been deprecated.';
	}
}, 9 );

// Database/ReviewManager.php
add_filter( 'site-reviews/create/review-values', function( $values, $command ) {
	if( has_filter( 'site-reviews/local/review' )) {
		glsr()->deprecated[] = 'The "site-reviews/local/review" hook has been deprecated. Please use the "site-reviews/create/review-values" hook instead.';
		return apply_filters( 'site-reviews/local/review', $values, $command );
	}
	return $values;
}, 9, 2 );

// Handlers/EnqueuePublicAssets.php
add_filter( 'site-reviews/enqueue/public/localize', function( $variables ) {
	if( has_filter( 'site-reviews/enqueue/localize' )) {
		glsr()->deprecated[] = 'The "site-reviews/enqueue/localize" hook has been deprecated. Please use the "site-reviews/enqueue/public/localize" hook instead.';
		return apply_filters( 'site-reviews/enqueue/localize', $variables );
	}
	return $variables;
}, 9 );

// Modules/Rating.php
add_filter( 'site-reviews/rating/average', function( $average ) {
	if( has_filter( 'site-reviews/average/rating' )) {
		glsr()->deprecated[] = 'The "site-reviews/average/rating" hook has been deprecated. Please use the "site-reviews/rating/average" hook instead.';
	}
	return $average;
}, 9 );

// Modules/Rating.php
add_filter( 'site-reviews/rating/ranking', function( $ranking ) {
	if( has_filter( 'site-reviews/bayesian/ranking' )) {
		glsr()->deprecated[] = 'The "site-reviews/bayesian/ranking" hook has been deprecated. Please use the "site-reviews/rating/ranking" hook instead.';
	}
	return $ranking;
}, 9 );

// Modules/Html/Partials/SiteReviews.php
add_filter( 'site-reviews/review/build/after', function( $renderedFields ) {
	if( has_filter( 'site-reviews/reviews/review/text' )) {
		glsr()->deprecated[] = 'The "site-reviews/reviews/review/text" hook has been deprecated. Please use the "site-reviews/review/build/after" hook instead.';
	}
	if( has_filter( 'site-reviews/reviews/review/title' )) {
		glsr()->deprecated[] = 'The "site-reviews/reviews/review/title" hook has been deprecated. Please use the "site-reviews/review/build/after" hook instead.';
	}
	return $renderedFields;
}, 9 );

// Modules/Html/Partials/SiteReviews.php
add_filter( 'site-reviews/review/build/before', function( $review ) {
	if( has_filter( 'site-reviews/rendered/review' )) {
		glsr()->deprecated[] = 'The "site-reviews/rendered/review" hook has been deprecated. Please either use a custom "review.php" template (refer to the documentation), or use the "site-reviews/review/build/after" hook instead.';
	}
	if( has_filter( 'site-reviews/rendered/review/meta/order' )) {
		glsr()->deprecated[] = 'The "site-reviews/rendered/review/meta/order" hook has been deprecated. Please use a custom "review.php" template instead (refer to the documentation).';
	}
	if( has_filter( 'site-reviews/rendered/review/order' )) {
		glsr()->deprecated[] = 'The "site-reviews/rendered/review/order" hook has been deprecated. Please use a custom "review.php" template instead (refer to the documentation).';
	}
	if( has_filter( 'site-reviews/rendered/review-form/login-register' )) {
		glsr()->deprecated[] = 'The "site-reviews/rendered/review-form/login-register" hook has been deprecated. Please use a custom "login-register.php" template instead (refer to the documentation).';
	}
	if( has_filter( 'site-reviews/reviews/navigation_links' )) {
		glsr()->deprecated[] = 'The "site-reviews/reviews/navigation_links" hook has been deprecated. Please use a custom "pagination.php" template instead (refer to the documentation).';
	}
	return $review;
}, 9 );

add_filter( 'site-reviews/validate/custom', function( $result, $request ) {
	if( has_filter( 'site-reviews/validate/review/submission' )) {
		glsr_log()->notice( 'The "site-reviews/validate/review/submission" hook has been deprecated. Please use the "site-reviews/validate/custom" hook instead.' );
		return apply_filters( 'site-reviews/validate/review/submission', $result, $request );
	}
	return $result;
}, 9, 2 );

add_filter( 'site-reviews/views/file', function( $file, $view, $data ) {
	if( has_filter( 'site-reviews/addon/views/file' )) {
		glsr()->deprecated[] = 'The "site-reviews/addon/views/file" hook has been deprecated. Please use the "site-reviews/views/file" hook instead.';
		$file = apply_filters( 'site-reviews/addon/views/file', $file, $view, $data );
	}
	return $file;
}, 9, 3 );

add_action( 'wp_footer', function() {
	$notices = array_keys( array_flip( glsr()->deprecated ));
	natsort( $notices );
	foreach( $notices as $notice ) {
		glsr_log()->notice( $notice );
		apply_filters( 'console', $notice ); // Show in Blackbar plugin if installed
	}
});

