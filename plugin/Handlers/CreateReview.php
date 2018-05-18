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
	 * @return void|string
	 */
	public function handle( Command $command )
	{
		$post_id = glsr( Database::class )->createReview( $this->normalize( $command ), $command );
		glsr( Database::class )->setReviewMeta( intval( $post_id ), $command->category );
		$this->sendNotification( $post_id, $command );
		$successMessage = apply_filters( 'site-reviews/local/review/submitted/message',
			__( 'Your review has been submitted!', 'site-reviews' ),
			$command
		);
		do_action( 'site-reviews/local/review/submitted', $successMessage, $command );
		if( $command->ajaxRequest ) {
			glsr( Session::class )->clear();
			return $successMessage;
		}
		glsr( Session::class )->set( $command->formId.'-message', $successMessage );
		wp_safe_redirect( $command->referrer );
		exit;
	}

	/**
	 * @return Email
	 */
	protected function createEmailNotification( Command $command, array $args = [] )
	{
		$email = [
			'to' => $args['recipient'],
			'subject' => $args['notification_title'],
			'template' => 'review-notification',
			'template-tags' => [
				'review_author' => $command->author,
				'review_content' => $command->content,
				'review_email' => $command->email,
				'review_ip' => $command->ipAddress,
				'review_link' => sprintf( '<a href="%1$s">%1$s</a>', $args['notification_link'] ),
				'review_rating' => $command->rating,
				'review_title' => $command->title,
			],
		];
		return glsr( Email::class )->compose( $email );
	}

	/**
	 * @return string
	 */
	protected function createWebhookNotification( Command $command, array $args )
	{
		$fields = [];
		$fields[] = ['title' => str_repeat( ':star:', (int) $command->rating )];
		if( $command->title ) {
			$fields[] = ['title' => $command->title];
		}
		if( $command->content ) {
			$fields[] = ['value' => $command->content];
		}
		if( $command->email ) {
			$command->email = ' <'.$command->email.'>';
		}
		if( $command->author ) {
			$fields[] = ['value' => trim( $command->author.$command->email.' - '.$command->ipAddress )];
		}
		$fields[] = ['value' => sprintf( '<%s|%s>', $args['notification_link'], __( 'View Review', 'site-reviews' ))];
		return json_encode([
			'icon_url' => glsr()->url.'assets/img/icon.png',
			'username' => glsr()->name,
			'attachments' => [[
				'pretext' => $args['notification_title'],
				'color' => '#665068',
				'fallback' => $this->createEmailNotification( $command, $args )->read( 'plaintext' ),
				'fields' => $fields,
			]],
		]);
	}

	/**
	 * @return array
	 */
	protected function normalize( Command $command )
	{
		$review = [
			'author' => $command->author,
			'assigned_to' => $command->assignedTo,
			'avatar' => get_avatar_url( $command->email ),
			'content' => $command->content,
			'email' => $command->email,
			'ip_address' => $command->ipAddress,
			'rating' => $command->rating,
			'review_type' => 'local',
			'title' => $command->title,
		];
		return apply_filters( 'site-reviews/local/review', $review, $command );
	}

	/**
	 * @param int $post_id
	 * @return void
	 */
	protected function sendNotification( $post_id, Command $command )
	{
		$notificationType = glsr( OptionManager::class )->get( 'settings.general.notification' );
		if( !in_array( $notificationType, ['default','custom','webhook'] ))return;
		$assignedToTitle = get_the_title( (int) $command->assignedTo );
		$notificationSubject = _nx(
			'New %s-star review',
			'New %s-star review of: %s',
			(int) empty( $assignedToTitle ),
			'The text is different depending on whether or not the review has been assigned to a post.',
			'site-reviews'
		);
		$notificationTitle = sprintf( '[%s] %s',
			wp_specialchars_decode( (string)get_option( 'blogname' ), ENT_QUOTES ),
			sprintf( $notificationSubject, $command->rating, $assignedToTitle )
		);
		$args = [
			'notification_link' => esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', $post_id ))),
			'notification_title' => $notificationTitle,
			'notification_type' => $notificationType,
		];
		return $args['notification_type'] == 'webhook'
			? $this->sendWebhookNotification( $command, $args )
			: $this->sendEmailNotification( $command, $args );
	}

	/**
	 * @return void
	 */
	protected function sendEmailNotification( Command $command, array $args )
	{
		$args['recipient'] = $args['notification_type'] === 'default'
			? get_option( 'admin_email' )
			: glsr( OptionManager::class )->get( 'settings.general.notification_email' );
		$result = !empty( $args['recipient'] )
			? $this->createEmailNotification( $command, $args )->send()
			: false;
		if( $result === null ) {
			glsr_log()->error( __( 'Email notification was not sent: missing email, subject, or message.', 'site-reviews' ));
		}
		if( $result === false ) {
			glsr_log()->error( __( 'Email notification was not sent: wp_mail() failed.', 'site-reviews' ));
		}
	}

	/**
	 * @return void
	 */
	protected function sendWebhookNotification( Command $command, array $args )
	{
		if( !( $endpoint = glsr( OptionManager::class )->get( 'settings.general.webhook_url' )))return;
		$notification = $this->createWebhookNotification( $command, $args );
		$result = wp_remote_post( $endpoint, [
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => false,
			'sslverify' => false,
			'headers' => ['Content-Type' => 'application/json'],
			'body' => apply_filters( 'site-reviews/webhook/notification', $notification, $command ),
		]);
		if( is_wp_error( $result )) {
			glsr_log()->error( $result->get_error_message() );
		}
	}
}
