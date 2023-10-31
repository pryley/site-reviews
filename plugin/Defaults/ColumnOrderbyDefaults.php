<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ColumnOrderbyDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'author_email' => 'email',
            'author_name' => 'name',
            'ip_address' => 'ip_address',
            'is_pinned' => 'is_pinned',
            'is_verified' => 'is_verified',
            'rating' => 'rating',
            'type' => 'type',
        ];
    }
}
