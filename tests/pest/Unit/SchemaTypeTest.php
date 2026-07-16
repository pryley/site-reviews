<?php

use GeminiLabs\SiteReviews\Modules\Schema\BaseType;
use GeminiLabs\SiteReviews\Modules\Schema\Exceptions\InvalidProperty;
use GeminiLabs\SiteReviews\Modules\Schema\LocalBusiness;
use GeminiLabs\SiteReviews\Modules\Schema\Person;
use GeminiLabs\SiteReviews\Modules\Schema\Thing;
use GeminiLabs\SiteReviews\Modules\Schema\UnknownType;

uses()->group('plugin');

/*
 * The schema.org value objects behind the JSON-LD output (Modules/Schema assembles them;
 * SchemaTest proves the assembly). Each type declares the properties schema.org allows on it,
 * inherits the rest from its parents, and serializes to the <script> tag Google reads.
 */

test('a property can be set fluently, by array access, or in bulk', function () {
    $thing = new Thing();
    $thing->name('A place');                                     // __call
    $thing['description'] = 'Somewhere nice';                    // offsetSet
    $thing->addProperties(['url' => 'https://example.org/x']);   // addProperties

    expect($thing->getProperty('name'))->toBe('A place');
    expect($thing['description'])->toBe('Somewhere nice');       // offsetGet
    expect(isset($thing['url']))->toBeTrue();                    // offsetExists
    expect($thing->getProperty('nope', 'fallback'))->toBe('fallback');

    unset($thing['url']);                                        // offsetUnset
    expect(isset($thing['url']))->toBeFalse();

    expect($thing->getType())->toBe('Thing');
    expect($thing->getContext())->toBe('https://schema.org');
});

test('a property schema.org does not allow on the type is refused, not rendered', function () {
    // Google rejects documents with unexpected properties; the type logs a warning and drops
    // the value instead of emitting invalid JSON-LD.
    $thing = new Thing();
    $thing->setProperty('telephone', '555-1234'); // a LocalBusiness property, not a Thing one

    expect($thing->getProperties())->not->toHaveKey('telephone');
});

test('an empty value is not rendered at all', function () {
    $thing = new Thing();
    $thing->name('');

    expect($thing->getProperties())->not->toHaveKey('name');
});

test('a type inherits the properties of its parents', function () {
    // LocalBusiness -> Place/Organization -> Thing: `name` is declared on Thing and
    // `telephone` on the business types, and both are usable on the leaf type.
    $business = new LocalBusiness();
    $business->name('The Shop')->telephone('555-1234');

    expect($business->getProperty('name'))->toBe('The Shop');
    expect($business->getProperty('telephone'))->toBe('555-1234');
});

test('a parent that does not exist as a class is skipped, not fatal', function () {
    $type = new class() extends BaseType {
        public $allowed = ['name'];
        public $parents = ['NotARealSchemaType'];
    };
    $type->name('Still works');

    expect($type->getProperty('name'))->toBe('Still works');
});

test('an unknown type accepts any property, under the name it was given', function () {
    // getSchemaType() falls back to UnknownType when the site owner configures a schema type
    // the plugin does not ship a class for — their custom type must not lose its properties.
    $custom = new UnknownType('VideoGame');
    $custom->setProperty('gamePlatform', 'PC');

    expect($custom->getType())->toBe('VideoGame');
    expect($custom->getProperty('gamePlatform'))->toBe('PC');
});

test('serializes to the JSON-LD script tag Google reads', function () {
    $person = (new Person())->name('Jane Doe');

    expect($person->toArray())->toBe([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Jane Doe',
    ]);
    expect((string) $person)->toBe(
        '<script type="application/ld+json">{"@context":"https://schema.org","@type":"Person","name":"Jane Doe"}</script>'
    );
    expect(json_encode($person))->toContain('"@type":"Person"'); // jsonSerialize
});

test('an identifier becomes the @id of the document, on any type', function () {
    // "@id" is a JSON-LD keyword, not a schema.org property, so it bypasses the per-type
    // allow-list — a typed class used to drop its identifier entirely because the rename
    // was refused by its own allowed[] check.
    $custom = (new UnknownType('Store'))->identifier('https://example.org/#store');
    $array = $custom->toArray();
    expect($array['@id'])->toBe('https://example.org/#store');
    expect($array)->not->toHaveKey('identifier');

    $typed = (new Thing())->identifier('https://example.org/#thing');
    $array = $typed->toArray();
    expect($array['@id'])->toBe('https://example.org/#thing');
    expect($array)->not->toHaveKey('identifier');
});

test('nested values serialize by what they are', function () {
    $review = new Thing();
    $review->addProperties([
        'name' => 'A review',
        'mainEntityOfPage' => (new Person())->name('Jane'),   // a nested type: no duplicate @context
        'additionalType' => new \DateTime('2024-06-01T12:00:00+00:00'), // a date: ATOM format
        'alternateName' => new class() {                      // an object that knows its string form
            public function __toString(): string
            {
                return 'stringable';
            }
        },
    ]);

    $array = $review->toArray();
    expect($array['mainEntityOfPage'])->toBe(['@type' => 'Person', 'name' => 'Jane'])
        ->and($array['additionalType'])->toBe('2024-06-01T12:00:00+00:00')
        ->and($array['alternateName'])->toBe('stringable');
});

test('an object that cannot be serialized is an exception, not silent garbage', function () {
    $thing = (new Thing())->name(new \stdClass());

    expect(fn () => $thing->toArray())->toThrow(InvalidProperty::class);
});
