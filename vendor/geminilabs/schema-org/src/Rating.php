<?php

namespace GeminiLabs\SchemaOrg;

/**
 * A rating is an evaluation on a numeric scale, such as 1 to 5 stars.
 *
 * @see http://schema.org/Rating
 *
 * @method static author( Organization|Person $author )
 * @method static bestRating( float|int|string $bestRating )
 * @method static ratingValue( string|float|int $ratingValue )
 * @method static worstRating( float|int|string $worstRating )
 */
class Rating extends Intangible
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'author',
        'bestRating',
        'ratingValue',
        'worstRating',
    ];
}
