<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

class MetaboxBuilder extends Builder
{
    protected function normalize(array $args, string $type): array
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args, 'metabox');
        }
        return $args;
    }
}
