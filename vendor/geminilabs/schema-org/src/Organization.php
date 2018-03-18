<?php

namespace GeminiLabs\SchemaOrg;

/**
 * An organization such as a school, NGO, corporation, club, etc.
 *
 * @see http://schema.org/Organization
 *
 * @method static hasOfferCatalog( OfferCatalog $hasOfferCatalog )
 * @method static address( PostalAddress|string $address )
 * @method static aggregateRating( AggregateRating $aggregateRating )
 * @method static areaServed( Place|AdministrativeArea|GeoShape|string $areaServed )
 * @method static award( string $award )
 * @method static awards( string $awards )
 * @method static parentOrganization( Organization $parentOrganization )
 * @method static brand( Brand|Organization $brand )
 * @method static contactPoint( ContactPoint $contactPoint )
 * @method static contactPoints( ContactPoint $contactPoints )
 * @method static department( Organization $department )
 * @method static duns( string $duns )
 * @method static email( string $email )
 * @method static employee( Person $employee )
 * @method static employees( Person $employees )
 * @method static event( Event $event )
 * @method static events( Event $events )
 * @method static faxNumber( string $faxNumber )
 * @method static founder( Person $founder )
 * @method static founders( Person $founders )
 * @method static dissolutionDate( \DateTimeInterface $dissolutionDate )
 * @method static foundingDate( \DateTimeInterface $foundingDate )
 * @method static globalLocationNumber( string $globalLocationNumber )
 * @method static hasPOS( Place $hasPOS )
 * @method static isicV4( string $isicV4 )
 * @method static legalName( string $legalName )
 * @method static location( Place|PostalAddress|string $location )
 * @method static logo( ImageObject|string $logo )
 * @method static makesOffer( Offer $makesOffer )
 * @method static offeredBy( Person|Offer $offeredBy )
 * @method static member( Organization|Person $member )
 * @method static memberOf( Organization|ProgramMembership $memberOf )
 * @method static members( Organization|Person $members )
 * @method static naics( string $naics )
 * @method static numberOfEmployees( QuantitativeValue $numberOfEmployees )
 * @method static owns( OwnershipInfo|Product $owns )
 * @method static review( Review $review )
 * @method static reviews( Review $reviews )
 * @method static seeks( Demand $seeks )
 * @method static serviceArea( Place|AdministrativeArea|GeoShape $serviceArea )
 * @method static sponsor( Organization|Person $sponsor )
 * @method static funder( Organization|Person $funder )
 * @method static subOrganization( Organization $subOrganization )
 * @method static taxID( string $taxID )
 * @method static telephone( string $telephone )
 * @method static vatID( string $vatID )
 * @method static foundingLocation( Place $foundingLocation )
 * @method static leiCode( string $leiCode )
 */
class Organization extends Thing
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'hasOfferCatalog',
        'address',
        'aggregateRating',
        'areaServed',
        'award',
        'awards',
        'parentOrganization',
        'brand',
        'contactPoint',
        'contactPoints',
        'department',
        'duns',
        'email',
        'employee',
        'employees',
        'event',
        'events',
        'faxNumber',
        'founder',
        'founders',
        'dissolutionDate',
        'foundingDate',
        'globalLocationNumber',
        'hasPOS',
        'isicV4',
        'legalName',
        'location',
        'logo',
        'makesOffer',
        'offeredBy',
        'member',
        'memberOf',
        'members',
        'naics',
        'numberOfEmployees',
        'owns',
        'review',
        'reviews',
        'seeks',
        'serviceArea',
        'sponsor',
        'funder',
        'subOrganization',
        'taxID',
        'telephone',
        'vatID',
        'foundingLocation',
        'leiCode',
    ];
}
