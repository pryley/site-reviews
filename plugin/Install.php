<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\SqlSchema;

class Install
{
    /**
     * @return void
     */
    public function run()
    {
        if (is_plugin_active_for_network(glsr()->file)) {
            $sites = get_sites([
                'fields' => 'ids',
                'network_id' => get_current_network_id(),
            ]);
            foreach ($sites as $siteId) {
                $this->runOnSite($siteId);
            }
            return;
        }
        $this->install();
    }

    /**
     * @param int $siteId
     * @return void
     */
    public function runOnSite($siteId)
    {
        switch_to_blog($siteId);
        $this->install();
        restore_current_blog();
    }

    /**
     * @return void
     */
    protected function install()
    {
        $this->createRoleCapabilities();
        $this->createTables();
    }

    /**
     * @return void
     */
    protected function createRoleCapabilities()
    {
        glsr(Role::class)->resetAll();
    }

    /**
     * @return void
     */
    protected function createTables()
    {
        glsr(SqlSchema::class)->createTables();
        glsr(SqlSchema::class)->addTableConstraints();
    }
}
