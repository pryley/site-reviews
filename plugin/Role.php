<?php

namespace GeminiLabs\SiteReviews;

class Role
{
    /**
     * @param string $role
     * @return void
     */
    public function addCapabilities($role)
    {
        $roleCapabilities = $this->roleCapabilities();
        $wpRole = get_role($role);
        if (empty($wpRole) || !array_key_exists($role, $roleCapabilities)) {
            return;
        }
        foreach ($roleCapabilities[$role] as $capability) {
            $wpRole->add_cap($this->normalizeCapability($capability));
        }
    }

    /**
     * @param string $capability
     * @return bool
     */
    public function can($capability)
    {
        return in_array($capability, $this->capabilities())
            ? current_user_can($this->normalizeCapability($capability))
            : current_user_can($capability);
    }

    /**
     * @return void
     */
    public function hardResetAll()
    {
        $roles = array_keys($this->roleCapabilities());
        array_walk($roles, [$this, 'removeCapabilities']);
        array_walk($roles, [$this, 'addCapabilities']);
    }

    /**
     * @param string $role
     * @return void
     */
    public function removeCapabilities($role)
    {
        $wpRole = get_role($role);
        if (empty($wpRole) || 'administrator' === $role) { // do not remove from administrator role
            return;
        }
        foreach ($this->capabilities() as $capability) {
            $wpRole->remove_cap($this->normalizeCapability($capability));
        }
    }

    /**
     * @return void
     */
    public function resetAll()
    {
        $roles = array_keys($this->roleCapabilities());
        array_walk($roles, [$this, 'addCapabilities']);
    }

    /**
     * @return array
     */
    protected function capabilities()
    {
        return [
            'delete_others_posts',
            'delete_post',
            'delete_posts',
            'delete_private_posts',
            'delete_published_posts',
            'edit_others_posts',
            'edit_post',
            'edit_posts',
            'edit_private_posts',
            'edit_published_posts',
            'publish_posts',
            'read_post',
            'read_private_posts',
        ];
    }

    /**
     * @param string $capability
     * @return string
     */
    protected function normalizeCapability($capability)
    {
        return str_replace('post', glsr()->post_type, $capability);
    }

    /**
     * @return array
     */
    protected function roleCapabilities()
    {
        return [
            'administrator' => [
                'delete_others_posts',
                'delete_posts',
                'delete_private_posts',
                'delete_published_posts',
                'edit_others_posts',
                'edit_posts',
                'edit_private_posts',
                'edit_published_posts',
                'publish_posts',
                'read_private_posts',
            ],
            'editor' => [
                'delete_others_posts',
                'delete_posts',
                'delete_private_posts',
                'delete_published_posts',
                'edit_others_posts',
                'edit_posts',
                'edit_private_posts',
                'edit_published_posts',
                'publish_posts',
                'read_private_posts',
            ],
            'author' => [
                'delete_posts',
                'delete_published_posts',
                'edit_posts',
                'edit_published_posts',
                'publish_posts',
            ],
            'contributor' => [
                'delete_posts',
                'edit_posts',
            ],
        ];
    }
}
