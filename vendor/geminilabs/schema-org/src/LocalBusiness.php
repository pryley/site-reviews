<?php

namespace GeminiLabs\SchemaOrg;

/**
 * A particular physical business or branch of an organization. Examples of LocalBusiness include a
 * restaurant, a particular branch of a restaurant chain, a branch of a bank, a medical practice, a
 * club, a bowling alley, etc.
 *
 * @see http://schema.org/LocalBusiness
 *
 * @method static branchOf( Organization $branchOf )
 * @method static currenciesAccepted( string $currenciesAccepted )
 * @method static openingHours( string $openingHours )
 * @method static paymentAccepted( string $paymentAccepted )
 * @method static priceRange( string $priceRange )
 */
class LocalBusiness extends Organization
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'branchOf',
        'currenciesAccepted',
        'openingHours',
        'paymentAccepted',
        'priceRange',
    ];
}
