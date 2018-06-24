<?php

namespace GeminiLabs\SiteReviews\Handlers;

use Exception;
use GeminiLabs\SiteReviews\Commands\CreateReview as Command;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Email;
use GeminiLabs\SiteReviews\Modules\Session;
use ReflectionException;
use WP_Error;

class CreateReview
{
	/**
	 * @var Command
	 */
	protected $command;

	/**
	 * @return void|string
	 */
	public function handle( Command $command )
	{
		$this->command = $command;
		$postId = glsr( Database::class )->createReview( $command );
		if( !$postId ) {
			glsr( Session::class )->set( $command->form_id.'errors', [] );
			return __( 'Your review could not be submitted, please notify the site admin.', 'site-reviews' );
		}
		$this->sendNotification( $postId );
		do_action( 'site-reviews/local/review/submitted', $postId, $command );
		glsr( Session::class )->set( $command->form_id.'message', __( 'Your review has been submitted!', 'site-reviews' ));
		if( $command->ajax_request ) {
			glsr( Session::class )->clear();
			return;
		}
		wp_safe_redirect( $command->referrer );
		exit;
	}

	/**
	 * @return Email
	 */
	protected function createEmailNotification( array $args = [] )
	{
		$email = [
			'to' => $args['recipient'],
			'subject' => $args['notification_title'],
			'template' => 'review-notification',
			'template-tags' => [
				'review_author' => $this->command->author,
				'review_content' => $this->command->content,
				'review_email' => $this->command->email,
				'review_ip' => $this->command->ip_address,
				'review_link' => sprintf( '<a href="%1$s">%1$s</a>', $args['notification_link'] ),
				'review_rating' => $this->command->rating,
				'review_title' => $this->command->title,
			],
		];
		return glsr( Email::class )->compose( $email );
	}

	/**
	 * @return string
	 */
	protected function createWebhookNotification( array $args )
	{
		$fields = [];
		$fields[] = ['title' => str_repeat( ':star:', $this->command->rating )];
		if( $this->command->title ) {
			$fields[] = ['title' => $this->command->title];
		}
		if( $this->command->content ) {
			$fields[] = ['value' => $this->command->content];
		}
		if( $this->command->email ) {
			$this->command->email = ' <'.$this->command->email.'>';
		}
		if( $this->command->author ) {
			$fields[] = ['value' => trim( $this->command->author.$this->command->email.' - '.$this->command->ip_address )];
		}
		$fields[] = ['value' => sprintf( '<%s|%s>', $args['notification_link'], __( 'View Review', 'site-reviews' ))];
		return json_encode([
			'icon_url' => glsr()->url( 'assets/img/icon.png' ),
			'username' => glsr()->name,
			'attachments' => [[
				'pretext' => $args['notification_title'],
				'color' => '#665068',
				'fallback' => $this->createEmailNotification( $args )->read( 'plaintext' ),
				'fields' => $fields,
			]],
		]);
	}

	/**
	 * @param int $post_id
	 * @return void
	 */
	protected function sendNotification( $postId )
	{
		$notificationType = glsr( OptionManager::class )->get( 'settings.general.notification' );
		if( !in_array( $notificationType, ['default','custom','webhook'] ))return;
		$assignedToTitle = get_the_title( (int)$this->command->assigned_to );
		$notificationSubject = _nx(
			'New %s-star review',
			'New %s-star review of: %s',
			intval( empty( $assignedToTitle )),
			'The text is different depending on whether or not the review has been assigned to a post.',
			'site-reviews'
		);
		$notificationTitle = sprintf( '[%s] %s',
			wp_specialchars_decode( (string)get_option( 'blogname' ), ENT_QUOTES ),
			sprintf( $notificationSubject, $this->command->rating, $assignedToTitle )
		);
		$args = [
			'notification_link' => esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', $postId ))),
			'notification_title' => $notificationTitle,
			'notification_type' => $notificationType,
		];
		$notificationMethod = $args['notification_type'] == 'webhook'
			? 'sendWebhookNotification'
			: 'sendEmailNotification';
		$this->$notificationMethod( $args );
	}

	/**
	 * @return void
	 */
	protected function sendEmailNotification( array $args )
	{
		$args['recipient'] = $args['notification_type'] !== 'default'
			? glsr( OptionManager::class )->get( 'settings.general.notification_email' )
			: get_option( 'admin_email' );
		if( empty( $args['recipient'] )) {
			glsr_log()->error( 'Email notification was not sent: missing email, subject, or message.' );
		}
		else {
			$email = $this->createEmailNotification( $args );
			if( $email->send() === false ) {
				glsr_log()->error( 'Email notification was not sent: wp_mail() failed.' )->debug( $email );
			}
		}
	}

	/**
	 * @return void
	 */
	protected function sendWebhookNotification( array $args )
	{
		if( !( $endpoint = glsr( OptionManager::class )->get( 'settings.general.webhook_url' )))return;
		$notification = $this->createWebhookNotification( $args );
		$result = wp_remote_post( $endpoint, [
			'blocking' => false,
			'body' => apply_filters( 'site-reviews/webhook/notification', $notification, $this->command ),
			'headers' => ['Content-Type' => 'application/json'],
			'httpversion' => '1.0',
			'method' => 'POST',
			'redirection' => 5,
			'sslverify' => false,
			'timeout' => 45,
		]);
		if( is_wp_error( $result )) {
			glsr_log()->error( $result->get_error_message() );
		}
	}
}
