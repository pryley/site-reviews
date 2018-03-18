<?php

namespace GeminiLabs\SchemaOrg;

/**
 * The average rating based on multiple ratings or reviews.
 *
 * @see http://schema.org/AggregateRating
 *
 * @method static itemReviewed( Thing $itemReviewed )
 * @method static ratingCount( int $ratingCount )
 * @method static reviewCount( int $reviewCount )
 */
class AggregateRating extends Rating
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'itemReviewed',
        'ratingCount',
        'reviewCount',
    ];
}
