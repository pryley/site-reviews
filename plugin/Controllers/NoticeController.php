<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class NoticeController extends Controller
{
    const USER_META_KEY = '_glsr_notices';

    /**
     * @var array
     */
    protected $dismissValuesMap;

    public function __construct()
    {
        $this->dismissValuesMap = [
            'rebusify' => glsr()->version('major'),
            'welcome' => glsr()->version('minor'),
        ];
    }

    /**
     * @return void
     * @action admin_notices
     */
    public function filterAdminNotices()
    {
        $screen = glsr_current_screen();
        $this->renderWelcomeNotice($screen->post_type);
        $this->renderRebusifyNotice($screen->post_type);
        $this->renderAddonsNotice($screen->id);
    }

    /**
     * @return void
     */
    public function routerDismissNotice(array $request)
    {
        if ($key = Arr::get($request, 'notice')) {
            $this->dismissNotice($key);
        }
    }

    /**
     * @param string $key
     * @return void
     */
    protected function dismissNotice($key)
    {
        $this->setUserMeta($key, $this->getVersionFor($key));
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
     * @param string $screenId
     * @return void
     */
    protected function renderAddonsNotice($screenId)
    {
        if (Application::POST_TYPE.'_page_addons' == $screenId) {
            echo glsr()->render('partials/notices/addons');
        }
    }

    /**
     * @param string $screenPostType
     * @return void
     */
    protected function renderRebusifyNotice($screenPostType)
    {
        if (Application::POST_TYPE == $screenPostType
            && version_compare($this->getVersionFor('rebusify'), $this->getUserMeta('rebusify', 0), '>')
            && !glsr(OptionManager::class)->getBool('settings.general.rebusify')) {
            echo glsr()->render('partials/notices/rebusify');
        }
    }

    /**
     * @param string $screenPostType
     * @return void
     */
    protected function renderWelcomeNotice($screenPostType)
    {
        if (Application::POST_TYPE == $screenPostType
            && version_compare($this->getVersionFor('welcome'), $this->getUserMeta('welcome', 0), '>')) {
            $welcomeText = '0.0.0' == glsr(OptionManager::class)->get('version_upgraded_from')
                ? __('Thanks for installing Site Reviews %s, we hope you love it!', 'site-reviews')
                : __('Thanks for updating to Site Reviews %s, we hope you love the changes!', 'site-reviews');
            echo glsr()->render('partials/notices/welcome', [
                'text' => sprintf($welcomeText, glsr()->version),
            ]);
        }
    }

    /**
     * @param string $key
     * @param mixed $fallback
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
