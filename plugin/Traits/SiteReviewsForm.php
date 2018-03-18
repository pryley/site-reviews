<?php

/**
 * Shared shortcode/widget methods
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Traits;

/**
 * @property bool|string $id
 */
trait SiteReviewsForm
{
	/**
	 * Generate a unique ID string
	 *
	 * @param mixed $from
	 * @return string
	 */
	public function generateId( $from = [] )
	{
		return !empty( $this->id )
			? sanitize_title( (string)$this->id )
			: substr( md5( serialize( $from )), 0, 8 );
	}

	/**
	 * @param array|string $args
	 * @return array
	 */
	public function normalize( $args, array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'assign_to' => '',
			'category' => '',
			'class' => '',
			'description' => '',
			'hide' => [],
			'id' => '',
			'title' => '',
		]);
		$atts = shortcode_atts( $defaults, wp_parse_args( $args ));
		$atts = $this->makeCompatible( $atts );
		$atts['hide'] = $this->normalizeHiddenFields( $atts['hide'] );
		return $atts;
	}

	/**
	 * @return string
	 */
	public function renderForm( array $atts )
	{
		$formId  = $this->generateId( $atts );
		$session = glsr_resolve( 'Session' );
		$errors  = $session->get( "{$formId}-errors", [], 'and then remove errors' );
		$message = $session->get( "{$formId}-message", [], 'and then remove message' );

		$values  = !empty( $errors )
			? $session->get( "{$formId}-values", [], 'and then remove values' )
			: [];

		ob_start();

		if( !empty( $atts['description'] )) {
			printf( '<p class="glsr-form-description">%s</p>', $atts['description'] );
		}

		glsr_resolve( 'Controllers\ReviewController' )->render( 'submit/index', [
			'assign_to' => $atts['assign_to'],
			'category'  => $atts['category'],
			'class'     => trim( 'glsr-submit-review-form ' . $atts['class'] ),
			'errors'    => $errors,
			'exclude'   => $atts['hide'],
			'form_id'   => $formId,
			'message'   => $message,
			'values'    => shortcode_atts([
				'content' => '',
				'email'   => '',
				'name'    => '',
				'rating'  => '',
				'terms'   => '',
				'title'   => '',
			], $values ),
		]);

		return ob_get_clean();
	}

	/**
	 * @return bool|string
	 */
	public function renderRequireLogin()
	{
		$requireUser = glsr_get_option( 'general.require.login' );
		if( $requireUser != 'yes' || is_user_logged_in() ) {
			return false;
		}
		$login = sprintf( __( 'You must be %s to submit a review.', 'site-reviews' ),
			sprintf( '<a href="%s">%s</a>', wp_login_url( (string) get_permalink() ), __( 'logged in', 'site-reviews' ))
		);
		if( get_option( 'users_can_register' ) && glsr_get_option( 'general.require.login_register' ) == 'yes' ) {
			$login .= ' '.sprintf( __( 'You may also %s for an account.', 'site-reviews' ),
				sprintf( '<a href="%s">%s</a>', wp_registration_url(), __( 'register', 'site-reviews' ))
			);
		}
		echo apply_filters( 'site-reviews/rendered/review-form/login-register', wpautop( trim( $login )));
		return true;
	}

	/**
	 * Maintain backwards compatibility with version <= v1.2.1
	 *
	 * @return array
	 */
	protected function makeCompatible( array $args )
	{
		if( is_string( $args['hide'] )) {
			$args['hide'] = str_replace( 'reviewer', 'name', $args['hide'] );
			$hide = explode( ',', $args['hide'] );
			$args['hide'] = array_unique( array_map( 'trim', $hide ));
		}
		return $args;
	}

	/**
	 * @param string|array $hiddenFields
	 * @return array
	 */
	protected function normalizeHiddenFields( $hiddenFields )
	{
		if( is_string( $hiddenFields )) {
			$hiddenFields = explode( ',', $hiddenFields );
		}
		return array_filter( $hiddenFields, function( $value ) {
			return in_array( $value, [
				'email',
				'name',
				'terms',
				'title',
			]);
		});
	}
}
