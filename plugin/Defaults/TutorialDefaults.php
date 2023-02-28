<?php

namespace GeminiLabs\SiteReviews\Defaults;

class TutorialDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'videos' => 'array',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'videos' => [],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     * @return array
     */
    protected function finalize(array $values = [])
    {
        foreach ($values['videos'] as &$video) {
            $video = glsr(VideoDefaults::class)->restrict($video);
        }
        return $values;
    }
}
