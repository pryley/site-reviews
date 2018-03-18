<?php

namespace GeminiLabs\SchemaOrg;

/**
 * This is a catch-all class for an unknown thing which assumes it can provide an aggregateRating.
 *
 * @method static aggregateRating(AggregateRating $aggregateRating)
 */
class Unknown extends Thing
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'aggregateRating',
    ];
}
