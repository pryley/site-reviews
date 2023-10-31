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

    protected function defaults(): array
    {
        return [
            'videos' => [],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        foreach ($values['videos'] as &$video) {
            $video = glsr(VideoDefaults::class)->restrict($video);
        }
        return $values;
    }
}
