<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\TemplateTagsDefaults;

class TemplateTags
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->formatTags(glsr(TemplateTagsDefaults::class)->defaults());
    }

    /**
     * @return string
     */
    protected function formatTags(array $tags)
    {
        return array_reduce(array_keys($tags), function ($carry, $tag) use ($tags) {
            return $carry.sprintf('<br><code>{%s}</code> %s', $tag, $tags[$tag]);
        });
    }
}
