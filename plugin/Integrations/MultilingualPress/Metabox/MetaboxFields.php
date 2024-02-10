<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Metabox;

use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxTab;

class MetaboxFields
{
    public const FIELD_COPY_ASSIGNED_POSTS = 'remote-assigned_posts-copy';
    public const FIELD_COPY_ASSIGNED_USERS = 'remote-assigned_users-copy';

    public function fields(): array
    {
        return [
            new MetaboxField(
                static::FIELD_COPY_ASSIGNED_POSTS,
                new AssignedPostsField()
            ),
            new MetaboxField(
                static::FIELD_COPY_ASSIGNED_USERS,
                new AssignedUsersField()
            ),
        ];
    }
}
