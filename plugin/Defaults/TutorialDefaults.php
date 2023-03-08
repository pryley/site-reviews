<?php

namespace GeminiLabs\SiteReviews\Defaults;

class TutorialDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
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
