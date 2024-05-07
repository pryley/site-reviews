<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Str;

class Role
{
    /**
     * @param array[] $roles
     */
    public function addCapabilities(string $role, array $roles = []): void
    {
        if (empty($roles)) {
            $roles = $this->roles();
        }
        $wpRole = get_role($role);
        if (empty($wpRole) || !array_key_exists($role, $roles)) {
            return;
        }
        foreach ($roles[$role] as $capability) {
            $wpRole->add_cap($this->capability($capability));
        }
    }

    /**
     * @param mixed ...$args
     */
    public function can(string $capability, ...$args): bool
    {
        return in_array($capability, $this->capabilities())
            ? current_user_can($this->capability($capability), ...$args)
            : current_user_can($capability, ...$args);
    }

    /**
     * @return string[]
     */
    public function capabilities(): array
    {
        $capabilities = [
            'create_posts',
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
            'respond_to_others_post',
            'respond_to_others_posts',
            'respond_to_post',
            'respond_to_posts',
            'assign_terms',
            'delete_terms',
            'edit_terms',
            'manage_terms',
        ];
        return glsr()->filterArray('capabilities', $capabilities);
    }

    public function capability(string $capability): string
    {
        if (str_contains($capability, 'post')) {
            return str_replace('post', glsr()->post_type, $capability);
        }
        if (str_contains($capability, 'terms')) {
            return str_replace('terms', glsr()->post_type.'_terms', $capability);
        }
        return $capability;
    }

    public function hardResetAll(): void
    {
        $roles = $this->roles();
        array_walk($roles, fn ($caps, $role) => $this->removeCapabilities($role));
        array_walk($roles, fn ($caps, $role) => $this->addCapabilities($role, $roles));
    }

    public function removeCapabilities(string $role): void
    {
        $wpRole = get_role($role);
        if (empty($wpRole) || 'administrator' === $role) { // do not remove from administrator role
            return;
        }
        foreach ($this->capabilities() as $capability) {
            $wpRole->remove_cap($this->capability($capability));
        }
    }

    /**
     * @param array[] $roles
     */
    public function reset(array $roles): void
    {
        if (empty($roles)) {
            return;
        }
        array_walk($roles, fn ($caps, $role) => $this->addCapabilities($role, $roles));
    }

    public function resetAll(): void
    {
        $roles = $this->roles();
        array_walk($roles, fn ($caps, $role) => $this->addCapabilities($role, $roles));
    }

    /**
     * @return array[]
     */
    public function roles(): array
    {
        $roles = [
            'administrator' => [
                'create_posts',
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
                'respond_to_others_posts',
                'respond_to_posts',
                'assign_terms',
                'delete_terms',
                'edit_terms',
                'manage_terms',
            ],
            'editor' => [
                'create_posts',
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
                'respond_to_others_posts',
                'respond_to_posts',
                'assign_terms',
                'delete_terms',
                'edit_terms',
                'manage_terms',
            ],
            'author' => [
                'create_posts',
                'delete_posts',
                'delete_published_posts',
                'edit_posts',
                'edit_published_posts',
                'publish_posts',
                'respond_to_posts',
                'assign_terms',
                'delete_terms',
                'edit_terms',
                'manage_terms',
            ],
            'contributor' => [
                'delete_posts',
                'edit_posts',
                'respond_to_posts',
                'assign_terms',
            ],
        ];
        return glsr()->filterArray('roles', $roles);
    }
}
