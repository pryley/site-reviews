<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class MetaboxField extends Field
{
    public Review $review;

    public function __construct(array $args = [])
    {
        $this->review = glsr_get_review(get_the_ID()); // review is cached
        parent::__construct($args);
    }

    public function builder(): BuilderContract
    {
        return glsr(MetaboxBuilder::class);
    }

    public function buildField(): string
    {
        return glsr(Template::class)->build('partials/editor/metabox-field', [
            'context' => [
                'class' => $this->classAttrField(),
                'field' => $this->buildFieldElement(),
                'label' => $this->buildFieldLabel(),
            ],
            'field' => $this,
            'review' => $this->review,
        ]);
    }

    public function location(): string
    {
        return 'metabox';
    }

    protected function classAttrField(): string
    {
        $type = $this->isChoiceField() ? 'choice' : $this->original_type;
        return glsr(Sanitizer::class)->sanitizeAttrClass(
            "glsr-metabox-field glsr-field-{$type}"
        );
    }
}
