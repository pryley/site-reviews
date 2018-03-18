<?php defined( 'WPINC' ) || die; ?>

<form method="post" action="" name="glsr-<?= $form_id; ?>" class="<?= $class; ?>">
<?php

	echo $html->renderField(['type' => 'honeypot']);

	echo $html->renderField([
		'type'       => 'select',
		'name'       => 'rating',
		'class'      => 'glsr-star-rating',
		'errors'     => $errors,
		'label'      => __( 'Your overall rating', 'site-reviews' ),
		'prefix'     => false,
		'render'     => !in_array( 'rating', $exclude ),
		'suffix'     => $form_id,
		'value'      => $values['rating'],
		'options'    => [
			''  => __( 'Select a Rating', 'site-reviews' ),
			'5' => __( 'Excellent', 'site-reviews' ),
			'4' => __( 'Very good', 'site-reviews' ),
			'3' => __( 'Average', 'site-reviews' ),
			'2' => __( 'Poor', 'site-reviews' ),
			'1' => __( 'Terrible', 'site-reviews' ),
		],
	]);

	echo $html->renderField([
		'type'        => 'text',
		'name'        => 'title',
		'errors'      => $errors,
		'label'       => __( 'Title of your review', 'site-reviews' ),
		'placeholder' => __( 'Summarize your review or highlight an interesting detail', 'site-reviews' ),
		'prefix'      => false,
		'render'      => !in_array( 'title', $exclude ),
		'required'    => in_array( 'title', glsr_get_option( 'reviews-form.required', [] )),
		'suffix'      => $form_id,
		'value'       => $values['title'],
	]);

	echo $html->renderField([
		'type'        => 'textarea',
		'name'        => 'content',
		'errors'      => $errors,
		'label'       => __( 'Your review', 'site-reviews' ),
		'placeholder' => __( 'Tell people your review', 'site-reviews' ),
		'prefix'      => false,
		'rows'        => 5,
		'render'      => !in_array( 'content', $exclude ),
		'required'    => in_array( 'content', glsr_get_option( 'reviews-form.required', [] )),
		'suffix'      => $form_id,
		'value'       => $values['content'],
	]);

	echo $html->renderField([
		'type'        => 'text',
		'name'        => 'name',
		'errors'      => $errors,
		'label'       => __( 'Your name', 'site-reviews' ),
		'placeholder' => __( 'Tell us your name', 'site-reviews' ),
		'prefix'      => false,
		'render'      => !in_array( 'name', $exclude ),
		'required'    => in_array( 'name', glsr_get_option( 'reviews-form.required', [] )),
		'suffix'      => $form_id,
		'value'       => $values['name'],
	]);

	echo $html->renderField([
		'type'        => 'email',
		'name'        => 'email',
		'errors'      => $errors,
		'label'       => __( 'Your email', 'site-reviews' ),
		'placeholder' => __( 'Tell us your email', 'site-reviews' ),
		'prefix'      => false,
		'render'      => !in_array( 'email', $exclude ),
		'required'    => in_array( 'email', glsr_get_option( 'reviews-form.required', [] )),
		'suffix'      => $form_id,
		'value'       => $values['email'],
	]);

	echo $html->renderField([
		'type'       => 'checkbox',
		'name'       => 'terms',
		'errors'     => $errors,
		'options'    => __( 'This review is based on my own experience and is my genuine opinion.', 'site-reviews' ),
		'prefix'     => false,
		'render'     => !in_array( 'terms', $exclude ),
		'required'   => true,
		'suffix'     => $form_id,
		'value'      => $values['terms'],
	]);

	echo $html->renderField([
		'type'   => 'hidden',
		'name'   => 'action',
		'prefix' => false,
		'value'  => 'post-review',
	]);

	echo $html->renderField([
		'type'   => 'hidden',
		'name'   => 'form_id',
		'prefix' => false,
		'value'  => $form_id,
	]);

	echo $html->renderField([
		'type'   => 'hidden',
		'name'   => 'assign_to',
		'prefix' => false,
		'value'  => $assign_to,
	]);

	echo $html->renderField([
		'type'   => 'hidden',
		'name'   => 'category',
		'prefix' => false,
		'value'  => $category,
	]);

	echo $html->renderField([
		'type'   => 'hidden',
		'name'   => 'excluded',
		'prefix' => false,
		'value'  => esc_attr( json_encode( $exclude )),
	]);

	wp_nonce_field( 'post-review' );

	if( $message ) {
		printf( '<div class="glsr-form-messages%s">%s</div>', ( $errors ? ' gslr-has-errors' : '' ), wpautop( $message ));
	}

	echo $html->renderField([
		'type'   => 'submit',
		'prefix' => false,
		'value'  => __( 'Submit your review', 'site-reviews' ),
	]);

?>
</form>
