<?php

use GeminiLabs\SiteReviews\Modules\Avatars\InitialsAvatar;
use GeminiLabs\SiteReviews\Modules\Avatars\PixelAvatar;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The avatars the plugin draws itself.
 *
 * Gravatar works by sending a hash of every visitor's email address to a server owned by
 * Automattic, on every page load, and getting a picture back. A site that would rather not do
 * that — which in the EU is most of them — needs a face to put next to a review anyway. So the
 * plugin draws one: either the reviewer's initials, or a pixel avatar generated from a hash of
 * their email.
 *
 * The word "privacy" is doing real work there, and it is what most of this file checks. The
 * email is hashed and the hash is what the drawing is derived from; the address itself must
 * not survive into the SVG, into the filename, or into the URL that ends up in the page — all
 * three of which are public.
 *
 * The other property that matters is boring and important: the SAME person must get the SAME
 * face, every time, forever. An avatar that changed on each page load would be worse than no
 * avatar at all.
 */

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    // create() writes real files into the uploads directory, and the transaction cannot roll
    // back a filesystem.
    $dir = trailingslashit(wp_upload_dir()['basedir']).'site-reviews/avatars/';
    array_map('unlink', (array) glob($dir.'*.svg'));
});

function pixelAvatar(): PixelAvatar
{
    return glsr(PixelAvatar::class);
}

/*
 * The pixel avatar.
 */

test('the same person always gets the same face', function () {
    // Deterministic from the hash. If this ever stops being true, every reviewer on every site
    // gets a new face on every page load.
    $first = pixelAvatar()->generate('jane@example.org');
    $second = pixelAvatar()->generate('jane@example.org');

    expect($first)->toBe($second)
        ->and($first)->not->toBe(pixelAvatar()->generate('somebody.else@example.org'));
});

test('the same person gets the same face however they typed their email', function () {
    // md5(strtolower(trim($from))). "Jane@Example.org " and "jane@example.org" are one person.
    expect(pixelAvatar()->generate(' Jane@Example.ORG '))
        ->toBe(pixelAvatar()->generate('jane@example.org'));
});

test('the email address is nowhere in the picture', function () {
    // THE POINT OF THE FEATURE. The SVG is inlined or served from a public URL; the filename is
    // in that URL. Neither may carry the address the face was derived from.
    $email = 'jane@example.org';
    $svg = pixelAvatar()->generate($email);
    $filename = protectedMethod(PixelAvatar::class, 'filename')->invoke(pixelAvatar(), $email);

    expect($svg)->not->toContain($email)
        ->and($svg)->not->toContain('jane')
        ->and($svg)->not->toContain('example.org');
    expect($filename)->not->toContain('jane')
        ->and($filename)->toHaveLength(15)              // the first 15 of an md5
        ->and($filename)->toMatch('/^[0-9a-f]{15}$/');  // and nothing but the hash
});

test('the picture is an svg of eleven by eleven pixels, and nothing else', function () {
    // It is written into a public directory and echoed into the page. Anything in here that is
    // not a path is something an attacker put there.
    $svg = pixelAvatar()->generate('jane@example.org');

    expect($svg)->toStartWith('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 11">')
        ->and($svg)->toEndWith('</svg>')
        ->and($svg)->toContain('<path fill="#')
        ->and($svg)->not->toContain('<script')
        ->and($svg)->not->toContain('href');

    // every fill is a hex colour, and every path is a run of pixel commands
    preg_match_all('/<path fill="([^"]+)" d="([^"]+)"\/>/', $svg, $matches);
    expect($matches[1])->not->toBeEmpty();
    foreach ($matches[1] as $fill) {
        expect($fill)->toMatch('/^#[0-9a-fA-F]{3,8}$/');
    }
    foreach ($matches[2] as $d) {
        expect($d)->toMatch('/^[MHhVvzZ0-9 ]+$/');
    }
});

test('every face has a background, a body, a head and a mouth', function () {
    // Four passes over the grid, each adding its own colour. A face drawn with one path is a
    // face where three of the passes did nothing.
    $svg = pixelAvatar()->generate('jane@example.org');

    preg_match_all('/<path fill="([^"]+)"/', $svg, $matches);

    expect(array_unique($matches[1]))->toHaveCount(count($matches[1])) // one path per colour
        ->and(count($matches[1]))->toBeGreaterThanOrEqual(4);
});

/*
 * The initials avatar.
 */

test('a full name becomes its initials', function () {
    $initials = fn (string $name) => protectedMethod(InitialsAvatar::class, 'filename')
        ->invoke(glsr(InitialsAvatar::class), $name);

    expect($initials('Jane Doe'))->toBe('JD');
});

test('one name becomes its first two letters, not one lonely one', function () {
    // "J" in a circle looks like a mistake. "JA" looks like a person.
    $initials = fn (string $name) => protectedMethod(InitialsAvatar::class, 'filename')
        ->invoke(glsr(InitialsAvatar::class), $name);

    expect($initials('Jane'))->toBe('JA')
        ->and($initials('jane'))->toBe('JA'); // and it is upper-cased
});

test('a name in an alphabet with no capitals still gets two characters', function () {
    // mb_substr, not substr. Cutting a multibyte name at two BYTES produces a broken character
    // and a mojibake avatar.
    $initials = protectedMethod(InitialsAvatar::class, 'filename')
        ->invoke(glsr(InitialsAvatar::class), '田中太郎');

    expect(mb_strlen($initials))->toBeLessThanOrEqual(2)
        ->and($initials)->not->toContain('?');
});

test('the initials avatar draws the initials and nothing about the person', function () {
    $svg = glsr(InitialsAvatar::class)->generate('Jane Doe');

    expect($svg)->toContain('JD')
        ->and($svg)->toContain('<svg')
        ->and($svg)->not->toContain('Jane Doe');
});

/*
 * Saving it. The avatar is written once and served as a file from then on — a site does not
 * redraw a thousand SVGs on every page of its reviews.
 */

test('the avatar is written into the uploads directory, and the url points at it', function () {
    $url = pixelAvatar()->create('jane@example.org');
    $filename = protectedMethod(PixelAvatar::class, 'filename')->invoke(pixelAvatar(), 'jane@example.org');
    $path = trailingslashit(wp_upload_dir()['basedir'])."site-reviews/avatars/{$filename}.svg";

    expect($url)->toEndWith("site-reviews/avatars/{$filename}.svg")
        ->and(file_exists($path))->toBeTrue()
        ->and(file_get_contents($path))->toBe(pixelAvatar()->generate('jane@example.org'));
});

test('an avatar that has already been drawn is not drawn again', function () {
    // create() is called for every review on the page. Rewriting the file each time would be a
    // filesystem write per review per page load.
    $url = pixelAvatar()->create('jane@example.org');
    $filename = protectedMethod(PixelAvatar::class, 'filename')->invoke(pixelAvatar(), 'jane@example.org');
    $path = trailingslashit(wp_upload_dir()['basedir'])."site-reviews/avatars/{$filename}.svg";

    file_put_contents($path, '<svg>touched</svg>'); // if it were redrawn, this would be replaced

    expect(pixelAvatar()->create('jane@example.org'))->toBe($url)
        ->and(file_get_contents($path))->toBe('<svg>touched</svg>');
});
