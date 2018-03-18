<?php

namespace GeminiLabs\SchemaOrg;

/**
 * The most generic type of item.
 *
 * @see http://schema.org/Thing
 *
 * @method static additionalType( string $additionalType )
 * @method static alternateName( string $alternateName )
 * @method static description( string $description )
 * @method static disambiguatingDescription( string $disambiguatingDescription )
 * @method static image( string|ImageObject $image )
 * @method static mainEntityOfPage( CreativeWork|string $mainEntityOfPage )
 * @method static name( string $name )
 * @method static sameAs( string $sameAs )
 * @method static url( string $url )
 * @method static potentialAction( Action $potentialAction )
 * @method static identifier( string|PropertyValue $identifier )
 */
class Thing extends BaseType
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'additionalType',
        'alternateName',
        'description',
        'disambiguatingDescription',
        'image',
        'mainEntityOfPage',
        'name',
        'sameAs',
        'url',
        'potentialAction',
        'identifier',
    ];
}
