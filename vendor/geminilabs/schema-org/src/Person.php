<?php

namespace GeminiLabs\SchemaOrg;

/**
 * A person (alive, dead, undead, or fictional).
 *
 * @see http://schema.org/Person
 *
 * @method static hasOfferCatalog( OfferCatalog $hasOfferCatalog )
 * @method static additionalName( string $additionalName )
 * @method static address( PostalAddress|string $address )
 * @method static affiliation( Organization $affiliation )
 * @method static alumniOf( EducationalOrganization $alumniOf )
 * @method static award( string $award )
 * @method static awards( string $awards )
 * @method static birthDate( \DateTimeInterface $birthDate )
 * @method static brand( Brand|Organization $brand )
 * @method static children( Person $children )
 * @method static colleague( Person|string $colleague )
 * @method static colleagues( Person $colleagues )
 * @method static contactPoint( ContactPoint $contactPoint )
 * @method static contactPoints( ContactPoint $contactPoints )
 * @method static deathDate( \DateTimeInterface $deathDate )
 * @method static duns( string $duns )
 * @method static email( string $email )
 * @method static familyName( string $familyName )
 * @method static faxNumber( string $faxNumber )
 * @method static follows( Person $follows )
 * @method static gender( string|GenderType $gender )
 * @method static givenName( string $givenName )
 * @method static globalLocationNumber( string $globalLocationNumber )
 * @method static hasPOS( Place $hasPOS )
 * @method static height( Distance|QuantitativeValue $height )
 * @method static homeLocation( ContactPoint|Place $homeLocation )
 * @method static honorificPrefix( string $honorificPrefix )
 * @method static honorificSuffix( string $honorificSuffix )
 * @method static isicV4( string $isicV4 )
 * @method static jobTitle( string $jobTitle )
 * @method static knows( Person $knows )
 * @method static makesOffer( Offer $makesOffer )
 * @method static memberOf( Organization|ProgramMembership $memberOf )
 * @method static naics( string $naics )
 * @method static nationality( Country $nationality )
 * @method static netWorth( PriceSpecification|MonetaryAmount $netWorth )
 * @method static owns( OwnershipInfo|Product $owns )
 * @method static parent( Person $parent )
 * @method static parents( Person $parents )
 * @method static performerIn( Event $performerIn )
 * @method static relatedTo( Person $relatedTo )
 * @method static seeks( Demand $seeks )
 * @method static sibling( Person $sibling )
 * @method static siblings( Person $siblings )
 * @method static sponsor( Organization|Person $sponsor )
 * @method static funder( Organization|Person $funder )
 * @method static spouse( Person $spouse )
 * @method static taxID( string $taxID )
 * @method static telephone( string $telephone )
 * @method static vatID( string $vatID )
 * @method static weight( QuantitativeValue $weight )
 * @method static workLocation( ContactPoint|Place $workLocation )
 * @method static worksFor( Organization $worksFor )
 * @method static birthPlace( Place $birthPlace )
 * @method static deathPlace( Place $deathPlace )
 */
class Person extends Thing
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'hasOfferCatalog',
        'additionalName',
        'address',
        'affiliation',
        'alumniOf',
        'award',
        'awards',
        'birthDate',
        'brand',
        'children',
        'colleague',
        'colleagues',
        'contactPoint',
        'contactPoints',
        'deathDate',
        'duns',
        'email',
        'familyName',
        'faxNumber',
        'follows',
        'gender',
        'givenName',
        'globalLocationNumber',
        'hasPOS',
        'height',
        'homeLocation',
        'honorificPrefix',
        'honorificSuffix',
        'isicV4',
        'jobTitle',
        'knows',
        'makesOffer',
        'memberOf',
        'naics',
        'nationality',
        'netWorth',
        'owns',
        'parent',
        'parents',
        'performerIn',
        'relatedTo',
        'seeks',
        'sibling',
        'siblings',
        'sponsor',
        'funder',
        'spouse',
        'taxID',
        'telephone',
        'vatID',
        'weight',
        'workLocation',
        'worksFor',
        'birthPlace',
        'deathPlace',
    ];
}
