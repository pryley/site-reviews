<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Migrate_5_0_0
{
    /**
     * @return void
     */
    public function migrateSidebarWidgets()
    {
        $sidebars = get_option('sidebars_widgets');
        if ($this->widgetsExist($sidebars)) {
            $sidebars = $this->updateWidgetNames($sidebars);
            update_option('sidebars_widgets', $sidebars);
        }
    }

    /**
     * @return void
     */
    public function migrateThemeModWidgets()
    {
        $themes = $this->queryThemeMods();
        foreach ($themes as $theme) {
            $themeMod = get_option($theme);
            $sidebars = Arr::get($themeMod, 'sidebars_widgets.data');
            if ($this->widgetsExist($sidebars)) {
                $themeMod['sidebars_widgets']['data'] = $this->updateWidgetNames($sidebars);
                update_option($theme, $themeMod);
            }
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->migrateSidebarWidgets();
        $this->migrateThemeModWidgets();
    }

    /**
     * @return array
     */
    protected function queryThemeMods()
    {
        global $wpdb;
        return $wpdb->get_col("
            SELECT option_name 
            FROM {$wpdb->options} 
            WHERE option_name LIKE '%theme_mods_%'
        ");
    }

    /**
     * @param array $sidebars
     * @return array
     */
    protected function updateWidgetNames(array $sidebars)
    {
        array_walk($sidebars, function(&$widgets) {
            array_walk($widgets, function(&$widget) {
                if (Str::startsWith(Application::ID.'_', $widget)) {
                    $widget = Str::replaceFirst(Application::ID.'_', Application::PREFIX, $widget);
                }
            });
        });
        return $sidebars;
    }

    /**
     * @param mixed $sidebars
     * @return bool
     */
    protected function widgetsExist($sidebars)
    {
        $widgets = call_user_func_array('array_merge', array_filter(Arr::consolidate($sidebars), 'is_array'));
        foreach ($widgets as $widget) {
            if (Str::startsWith(Application::ID.'_', $widget)) {
                return true;
            }
        }
        return false;
    }
}
