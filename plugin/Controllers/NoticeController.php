<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;

class NoticeController extends Controller
{
    const USER_META_KEY = '_glsr_notices';

    /**
     * @return void
     * @action admin_notices
     */
    public function filterAdminNotices()
    {
        $screen = glsr_current_screen();
        $this->renderRebusifyNotice($screen->post_type);
        $this->renderAddonsNotice($screen->id);
    }

    /**
     * @return void
     */
    public function routerDismissNotice(array $request)
    {
        $key = glsr_get($request, 'notice');
        $method = glsr(Helper::class)->buildMethodName($key, 'dismiss');
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $key);
        }
    }

    /**
     * @param string $key
     * @return void
     */
    protected function dismissRebusify($key)
    {
        $this->setUserMeta($key, glsr()->version('major'));
    }

    /**
     * @param string $key
     * @param mixed $fallback
     * @return mixed
     */
    protected function getUserMeta($key, $fallback)
    {
        $meta = get_user_meta(get_current_user_id(), static::USER_META_KEY, true);
        return glsr_get($meta, $key, $fallback);
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
            && version_compare(glsr()->version('major'), $this->getUserMeta('rebusify', 0), '>')
            && !glsr(OptionManager::class)->getBool('settings.general.rebusify')) {
            echo glsr()->render('partials/notices/rebusify');
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
