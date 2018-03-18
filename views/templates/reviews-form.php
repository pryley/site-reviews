<?php defined( 'WPINC' ) || die;

$render->rating( 'rating', [
	'label' => __( 'Your overall rating', 'site-reviews' ),
]);
$render->text( 'title', [
	'label' => __( 'Title of your review', 'site-reviews' ),
	'placeholder' => __( 'Summarize your review or highlight an interesting detail', 'site-reviews' ),
]);
$render->textarea( 'content', [
	'label' => __( 'Your review', 'site-reviews' ),
	'placeholder' => __( 'Tell people your review', 'site-reviews' ),
]);
$render->text( 'name', [
	'label' => __( 'Your name', 'site-reviews' ),
	'placeholder' => __( 'Tell us your name', 'site-reviews' ),
]);
$render->email( 'email', [
	'label' => __( 'Your email', 'site-reviews' ),
	'placeholder' => __( 'Tell us your email', 'site-reviews' ),
]);
$render->checkbox( 'terms', [
	'label' => __( 'This review is based on my own experience and is my genuine opinion.', 'site-reviews' ),
]);
$render->honeypot( 'gotcha' );
$render->hidden( 'assign_to' );
$render->hidden( 'category' );
$render->hidden( 'excluded' );
$render->hidden( 'id' );
wp_nonce_field( 'post-review' );
$render->submit( __( 'Submit your review', 'site-reviews' ));
$render->result();
