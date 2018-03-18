<?php

namespace GeminiLabs\SchemaOrg;

/**
 * The most generic kind of creative work, including books, movies, photographs, software programs,
 * etc.
 *
 * @see http://schema.org/CreativeWork
 *
 * @method static schemaVersion( string $schemaVersion )
 * @method static about( Thing $about )
 * @method static accessibilityAPI( string $accessibilityAPI )
 * @method static accessibilityControl( string $accessibilityControl )
 * @method static accessibilityFeature( string $accessibilityFeature )
 * @method static accessibilityHazard( string $accessibilityHazard )
 * @method static accountablePerson( Person $accountablePerson )
 * @method static aggregateRating( AggregateRating $aggregateRating )
 * @method static alternativeHeadline( string $alternativeHeadline )
 * @method static associatedMedia( MediaObject $associatedMedia )
 * @method static audience( Audience $audience )
 * @method static audio( AudioObject $audio )
 * @method static author( Organization|Person $author )
 * @method static award( string $award )
 * @method static awards( string $awards )
 * @method static citation( CreativeWork|string $citation )
 * @method static comment( Comment $comment )
 * @method static contentLocation( Place $contentLocation )
 * @method static locationCreated( Place $locationCreated )
 * @method static contentRating( string $contentRating )
 * @method static contributor( Organization|Person $contributor )
 * @method static copyrightHolder( Organization|Person $copyrightHolder )
 * @method static copyrightYear( float|int $copyrightYear )
 * @method static creator( Organization|Person $creator )
 * @method static dateCreated( \DateTimeInterface $dateCreated )
 * @method static dateModified( \DateTimeInterface $dateModified )
 * @method static datePublished( \DateTimeInterface $datePublished )
 * @method static discussionUrl( string $discussionUrl )
 * @method static editor( Person $editor )
 * @method static educationalAlignment( AlignmentObject $educationalAlignment )
 * @method static educationalUse( string $educationalUse )
 * @method static encoding( MediaObject $encoding )
 * @method static encodings( MediaObject $encodings )
 * @method static fileFormat( string $fileFormat )
 * @method static isAccessibleForFree( bool $isAccessibleForFree )
 * @method static genre( string $genre )
 * @method static headline( string $headline )
 * @method static inLanguage( string|Language $inLanguage )
 * @method static interactivityType( string $interactivityType )
 * @method static isBasedOnUrl( string|CreativeWork|Product $isBasedOnUrl )
 * @method static isBasedOn( string|CreativeWork|Product $isBasedOn )
 * @method static isFamilyFriendly( bool $isFamilyFriendly )
 * @method static isPartOf( CreativeWork $isPartOf )
 * @method static keywords( string $keywords )
 * @method static license( CreativeWork|string $license )
 * @method static learningResourceType( string $learningResourceType )
 * @method static mainEntity( Thing $mainEntity )
 * @method static mentions( Thing $mentions )
 * @method static offers( Offer $offers )
 * @method static position( string|int $position )
 * @method static producer( Person|Organization $producer )
 * @method static publication( PublicationEvent $publication )
 * @method static publisher( Organization|Person $publisher )
 * @method static publishingPrinciples( string $publishingPrinciples )
 * @method static recordedAt( Event $recordedAt )
 * @method static review( Review $review )
 * @method static reviews( Review $reviews )
 * @method static sourceOrganization( Organization $sourceOrganization )
 * @method static spatialCoverage( Place $spatialCoverage )
 * @method static sponsor( Organization|Person $sponsor )
 * @method static funder( Organization|Person $funder )
 * @method static temporalCoverage( \DateTimeInterface|string $temporalCoverage )
 * @method static text( string $text )
 * @method static thumbnailUrl( string $thumbnailUrl )
 * @method static timeRequired( Duration $timeRequired )
 * @method static typicalAgeRange( string $typicalAgeRange )
 * @method static version( float|int|string $version )
 * @method static video( VideoObject $video )
 * @method static provider( Person|Organization $provider )
 * @method static commentCount( int $commentCount )
 * @method static hasPart( CreativeWork $hasPart )
 * @method static workExample( CreativeWork $workExample )
 * @method static exampleOfWork( CreativeWork $exampleOfWork )
 * @method static character( Person $character )
 * @method static translator( Person|Organization $translator )
 * @method static releasedEvent( PublicationEvent $releasedEvent )
 * @method static material( string|Product $material )
 * @method static interactionStatistic( InteractionCounter $interactionStatistic )
 * @method static accessMode( string $accessMode )
 * @method static accessModeSufficient( string $accessModeSufficient )
 * @method static accessibilitySummary( string $accessibilitySummary )
 */
class CreativeWork extends Thing
{
    /**
     * @see http://schema.org/{PROPERTY_NAME}
     */
    const PROPERTIES = [
        'schemaVersion',
        'about',
        'accessibilityAPI',
        'accessibilityControl',
        'accessibilityFeature',
        'accessibilityHazard',
        'accountablePerson',
        'aggregateRating',
        'alternativeHeadline',
        'associatedMedia',
        'audience',
        'audio',
        'author',
        'award',
        'awards',
        'citation',
        'comment',
        'contentLocation',
        'locationCreated',
        'contentRating',
        'contributor',
        'copyrightHolder',
        'copyrightYear',
        'creator',
        'dateCreated',
        'dateModified',
        'datePublished',
        'discussionUrl',
        'editor',
        'educationalAlignment',
        'educationalUse',
        'encoding',
        'encodings',
        'fileFormat',
        'isAccessibleForFree',
        'genre',
        'headline',
        'inLanguage',
        'interactivityType',
        'isBasedOnUrl',
        'isBasedOn',
        'isFamilyFriendly',
        'isPartOf',
        'keywords',
        'license',
        'learningResourceType',
        'mainEntity',
        'mentions',
        'offers',
        'position',
        'producer',
        'publication',
        'publisher',
        'publishingPrinciples',
        'recordedAt',
        'review',
        'reviews',
        'sourceOrganization',
        'spatialCoverage',
        'sponsor',
        'funder',
        'temporalCoverage',
        'text',
        'thumbnailUrl',
        'timeRequired',
        'typicalAgeRange',
        'version',
        'video',
        'provider',
        'commentCount',
        'hasPart',
        'workExample',
        'exampleOfWork',
        'character',
        'translator',
        'releasedEvent',
        'material',
        'interactionStatistic',
        'accessMode',
        'accessModeSufficient',
        'accessibilitySummary',
    ];
}
