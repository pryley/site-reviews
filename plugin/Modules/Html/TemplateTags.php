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
        $result = [];
        foreach ($tags as $tag => $description) {
            $result[] = sprintf('<code>{%s}</code> %s', $tag, $description);
        }
        return implode('<br>', $result);
    }
}
