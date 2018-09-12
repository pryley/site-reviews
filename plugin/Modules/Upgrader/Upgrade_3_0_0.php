<?php

namespace GeminiLabs\SiteReviews\Modules\Upgrader;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Helper;

class Upgrade_3_0_0
{
	/**
	 * @var array
	 */
	protected $newSettings;

	/**
	 * @var array
	 */
	protected $oldSettings;

	public function __construct()
	{
		$this->newSettings = glsr( Helper::class )->flattenArray( glsr( OptionManager::class )->all() );
		$this->oldSettings = $this->getOldSettings();
		$this->migrateSettings();
		$this->setReviewCounts();
	}

	/**
	 * @return void
	 */
	public function migrateSettings()
	{
		$this->newSettings['settings.general.notification_slack'] = $this->oldSettings['settings.general.webhook_url'];
		$this->newSettings['settings.reviews.assigned_links'] = $this->oldSettings['settings.reviews.assigned_links.enabled'];
		$this->newSettings['settings.reviews.avatars'] = $this->oldSettings['settings.reviews.avatars.enabled'];
		$this->newSettings['settings.reviews.excerpts'] = $this->oldSettings['settings.reviews.excerpt.enabled'];
		$this->newSettings['settings.reviews.excerpts_length'] = $this->oldSettings['settings.reviews.excerpt.length'];
		$this->newSettings['settings.schema'] = $this->oldSettings['settings.reviews.schema'];
		$this->newSettings['settings.submissions'] = $this->oldSettings['settings.reviews-form'];
		$this->newSettings['settings.submissions'][] = 'rating';
		$this->newSettings['settings.submissions'][] = 'terms';
		$this->migrateNotificationSettings();
		$this->migrateRecaptchaSettings();
		glsr( OptionManager::class )->set( glsr( Helper::class )->convertDotNotationArray( $this->newSettings ));
	}

	/**
	 * @return void
	 */
	public function setReviewCounts()
	{
		add_action( 'admin_init', function() {
			glsr( AdminController::class )->routerCountReviews();
		});
	}

	/**
	 * @return array
	 */
	protected function getOldSettings()
	{
		$settings = glsr( Helper::class )->flattenArray( get_option( Application::ID.'-v2', [] ));
		return wp_parse_args( $settings, [
			'settings.general.notification' => '',
			'settings.general.webhook_url' => '',
			'settings.reviews.assigned_links.enabled' => 'no',
			'settings.reviews.avatars.enabled' => 'no',
			'settings.reviews.excerpt.enabled' => 'yes',
			'settings.reviews.excerpt.length' => 55,
			'settings.reviews.schema' => $this->newSettings['schema'],
			'settings.reviews-form' => $this->newSettings['submissions'],
			'settings.reviews-form.recaptcha.key' => $this->newSettings['submissions.recaptcha.key'],
			'settings.reviews-form.recaptcha.secret' => $this->newSettings['submissions.recaptcha.secret'],
			'settings.reviews-form.recaptcha.position' => $this->newSettings['submissions.recaptcha.position'],
		]);
	}

	/**
	 * @return void
	 */
	protected function migrateNotificationSettings()
	{
		$notifications = [
			'custom' => 'custom',
			'default' => 'admin',
			'webhook' => 'slack',
		];
		foreach( $notifications as $old => $new ) {
			if( $this->oldSettings['settings.general.notification'] != $old )continue;
			$this->newSettings['settings.general.notifications'][] = $new;
		}
	}

	/**
	 * @return void
	 */
	protected function migrateRecaptchaSettings()
	{
		if( in_array( $this->oldSettings['settings.reviews-form.recaptcha.integration'], ['custom', 'invisible-recaptcha'] )) {
			$this->newSettings['settings.submissions.recaptcha.integration'] == 'all';
		}
		if( $this->oldSettings['settings.reviews-form.recaptcha.integration'] == 'invisible-recaptcha' ) {
			$invisibleRecaptchaOptions = wp_parse_args( get_site_option( 'ic-settings', [] , false ). [
				'BadgePosition' => $this->oldSettings['settings.reviews-form.recaptcha.position'],
				'SecretKey' => $this->oldSettings['settings.reviews-form.recaptcha.secret'],
				'SiteKey' => $this->oldSettings['settings.reviews-form.recaptcha.key'],
			]);
			$this->newSettings['settings.submissions.recaptcha.key'] = $invisibleRecaptchaOptions['SiteKey'];
			$this->newSettings['settings.submissions.recaptcha.secret'] = $invisibleRecaptchaOptions['SecretKey'];
			$this->newSettings['settings.submissions.recaptcha.position'] = $invisibleRecaptchaOptions['BadgePosition'];
		}
	}
}
