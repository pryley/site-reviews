<?php

namespace GeminiLabs\SchemaOrg;

/**
 * A review of an item - for example, of a restaurant, movie, or store.
 *
 * @see http://schema.org/Review
 *
 * @method static itemReviewed( Thing $itemReviewed )
 * @method static reviewBody( string $reviewBody )
 * @method static reviewRating( Rating $reviewRating )
 */
class Review extends CreativeWork
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'itemReviewed',
        'reviewBody',
        'reviewRating',
    ];
}
