<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;

class NoticeController extends Controller
{
    public const USER_META_KEY = '_glsr_notices';

    /**
     * @var array
     */
    protected $activeNotices;

    /**
     * @var array
     */
    protected $dismissValuesMap;

    public function __construct()
    {
        $this->activeNotices = [];
        $this->dismissValuesMap = [
            'footer' => glsr()->version('major'),
            'premium' => glsr()->version('minor'),
            'welcome' => glsr()->version('minor'),
            'write-review' => glsr()->version('major'),
        ];
    }

    /**
     * @return void
     * @filter admin_notices
     */
    public function adminNotices()
    {
        // order is intentional!
        $this->renderPremiumNotice();
        $this->renderWelcomeNotice();
        $this->renderMigrationNotice();
        $this->renderWriteReviewNotice(); // this goes last!
    }

    /**
     * @return void
     * @action site-reviews/route/admin/dismiss-notice
     */
    public function dismissNotice(Request $request)
    {
        $notice = glsr(Sanitizer::class)->sanitizeText($request->notice);
        if ($notice && array_key_exists($notice, $this->dismissValuesMap)) {
            $this->setUserMeta($notice, $this->getVersionFor($notice));
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/dismiss-notice
     */
    public function dismissNoticeAjax(Request $request)
    {
        $this->dismissNotice($request);
        wp_send_json_success();
    }

    /**
     * @return void
     * @action in_admin_footer
     */
    public function renderFooterNotice()
    {
        if (!Str::startsWith(glsr_current_screen()->post_type, glsr()->post_type)) {
            return;
        }
        if (Helper::isGreaterThan($this->getVersionFor('footer'), $this->getUserMeta('footer', 0))) {
            $link = glsr(Builder::class)->a([
                'class' => 'button button-link',
                'href' => 'https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post',
                'target' => '_blank',
                'text' => '★★★★★',
            ]);
            $text = sprintf('%s %s 💖',
                sprintf(_x('Are you happy with %s?', 'Site Reviews (admin-text)', 'site-reviews'), sprintf('<strong>%s</strong>', glsr()->name)),
                sprintf(_x('Please rate %s on WordPress and let other people know about it.', '★★★★★ (admin-text)', 'site-reviews'), $link)
            );
            glsr()->render('partials/notices/footer', compact('text'));
        }
    }

    protected function licensing()
    {
        $isFree = true; // priority 1
        $isValid = true; // priority 2
        $isSaved = true; // priority 3
        foreach (glsr()->updated as $addonId => $addon) {
            if (!$addon['licensed']) {
                continue; // this is a free add-on
            }
            $isFree = false; // there are premium add-ons installed
            if (empty(glsr_get_option('licenses.'.$addonId))) {
                $isSaved = false;
                continue; // the license has not been saved in the settings
            }
            $licenseStatus = get_option(glsr()->prefix.$addonId);
            if (empty($licenseStatus)) { // the license status has not been stored
                $license = glsr_get_option('licenses.'.$addonId);
                $updater = new Updater($addon['updateUrl'], $addon['file'], $addonId, compact('license'));
                $licenseStatus = $updater->isLicenseValid() ? 'valid' : 'invalid';
            }
            if ('valid' !== $licenseStatus) {
                $isValid = false;
                break;
            }
        }
        return compact('isFree', 'isSaved', 'isValid');
    }

    protected function futureTime()
    {
        $time = !glsr(Migrate::class)->isMigrationNeeded()
            ? glsr(OptionManager::class)->get('last_migration_run', time())
            : time();
        return $time + WEEK_IN_SECONDS;
    }

    /**
     * @param string $key
     * @param mixed $fallback
     * @return mixed
     */
    protected function getUserMeta($key, $fallback)
    {
        $meta = get_user_meta(get_current_user_id(), static::USER_META_KEY, true);
        return Arr::get($meta, $key, $fallback);
    }

    /**
     * @param string $noticeKey
     * @return string
     */
    protected function getVersionFor($noticeKey)
    {
        return Arr::get($this->dismissValuesMap, $noticeKey, glsr()->version('major'));
    }

    /**
     * @return void
     */
    protected function renderPremiumNotice()
    {
        if (!Str::startsWith(glsr_current_screen()->post_type, glsr()->post_type)) {
            return;
        }
        $licensing = $this->licensing();
        $isNewVersion = Helper::isGreaterThan($this->getVersionFor('premium'), $this->getUserMeta('premium', 0));
        $isUnsavedLicense = glsr()->can('edit_others_posts') && !$licensing['isSaved'];
        if ($licensing['isFree']) {
            if ($isNewVersion) {
                glsr()->render('partials/notices/premium', $licensing);
            }
        } elseif (($isUnsavedLicense && $isNewVersion) || !$licensing['isValid']) {
            // always show this notice for invalid licenses!
            glsr()->render('partials/notices/premium', $licensing);
        }
    }

    /**
     * @return void
     */
    protected function renderMigrationNotice()
    {
        if ($this->isReviewAdminScreen() && glsr()->hasPermission('tools', 'general')) {
            $args = [];
            if (glsr(Database::class)->isMigrationNeeded()) {
                $args['database'] = true;
            }
            if (glsr(Migrate::class)->isMigrationNeeded()) {
                $args['migrations'] = glsr(Migrate::class)->pendingVersions();
            }
            if (empty($args)) {
                return;
            }
            if (!glsr(Queue::class)->isPending('queue/migration')) {
                // The $args are informational only
                glsr(Queue::class)->once(time() + MINUTE_IN_SECONDS, 'queue/migration', $args);
            }
            glsr()->render('partials/notices/migrate', [
                'action' => glsr(Builder::class)->a([
                    'data-expand' => '#support-common-problems-and-solutions',
                    'href' => glsr_admin_url('documentation', 'support'),
                    'text' => _x('Common Problems and Solutions', 'admin-text', 'site-reviews'),
                ]),
            ]);
            $this->activeNotices[] = 'migration';
        }
    }

    /**
     * @return void
     */
    protected function renderWelcomeNotice()
    {
        if ($this->isReviewAdminScreen()
            && Helper::isGreaterThan($this->getVersionFor('welcome'), $this->getUserMeta('welcome', 0))
            && glsr()->can('edit_others_posts')) {
            if ('0.0.0' == glsr(OptionManager::class)->get('version_upgraded_from')) {
                $fresh = true;
                $text = sprintf(_x('Thank you for installing %s! I hope you love it.', 'plugin name (admin-text)', 'site-reviews'),
                    sprintf('<strong>%s</strong> v%s', glsr()->name, glsr()->version)
                ).' ✨';
            } else {
                $fresh = false;
                $text = sprintf(_x('Thank you for updating %s to %s! I hope you love the improvements.', 'plugin name|version (admin-text)', 'site-reviews'),
                    sprintf('<strong>%s</strong>', glsr()->name),
                    sprintf('v%s', glsr()->version)
                ).' 🎉';
            }
            glsr()->render('partials/notices/welcome', compact('fresh', 'text'));
            $this->activeNotices[] = 'welcome';
        }
    }

    /**
     * @return void
     */
    protected function renderWriteReviewNotice()
    {
        if (!Str::startsWith(glsr_current_screen()->post_type, glsr()->post_type)) {
            return;
        }
        if (empty($this->activeNotices)
            && Helper::isGreaterThan($this->getVersionFor('write-review'), $this->getUserMeta('write-review', 0))
            && $this->futureTime() < time()) {
            $text = sprintf('%s %s %s',
                sprintf(_x('Are you happy with %s?', 'Site Reviews (admin-text)', 'site-reviews'), sprintf('<strong>%s</strong>', glsr()->name)),
                sprintf(_x('Please rate %s on WordPress and let other people know about it.', '★★★★★ (admin-text)', 'site-reviews'), '★★★★★'),
                _x('Thank you so much!', 'admin-text', 'site-reviews')
            );
            glsr()->render('partials/notices/write-review', compact('text'));
            $this->activeNotices[] = 'write-review';
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function setUserMeta($key, $value)
    {
        $userId = get_current_user_id();
        $meta = (array) get_user_meta($userId, static::USER_META_KEY, true);
        $meta = array_filter(wp_parse_args($meta, []));
        $meta[$key] = $value;
        update_user_meta($userId, static::USER_META_KEY, $meta);
    }
}
