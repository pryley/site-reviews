<?php

use Faker\Factory;
use GeminiLabs\SiteReviews\Modules\Email;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\sentMail;

uses()->group('email');

beforeEach(fn () => resetPluginState());

/*
 * Nothing leaves the test container: `pre_wp_mail` short-circuits wp_mail() and records the
 * message instead (see Support/helpers.php), so these assert on what WOULD have been sent, not
 * just wp_mail()'s return value.
 */

test('composes and sends an email from a template', function () {
    $review = reviewValues();
    $sent = glsr(Email::class)->compose([
        'to' => 'test@wordpress.dev',
        'subject' => 'Test Email',
        'template' => 'default',
        // the body is the caller's to provide: Email holds no template of its own to fall back on
        'message' => 'A review by {review_author}.',
        'template-tags' => [
            'review_author' => $review['name'],
            'review_content' => $review['content'],
            'review_email' => $review['email'],
            'review_ip' => '127.0.0.1',
            'review_link' => 'http://...',
            'review_rating' => $review['rating'],
            'review_title' => $review['title'],
        ],
    ])->send();

    expect($sent)->toBeTrue();

    $mail = sentMail();
    expect($mail)->toHaveCount(1);
    expect((array) $mail[0]['to'])->toContain('test@wordpress.dev');
    expect($mail[0]['subject'])->toBe('Test Email');
    expect($mail[0]['message'])->toBeString()->not->toBeEmpty();
    expect($mail[0]['message'])->toContain('A review by ')->not->toContain('{review_author}');
});

test('does not send an email it cannot validate', function () {
    // No recipient: send() bails before wp_mail() is ever reached.
    $sent = glsr(Email::class)->compose([
        'subject' => 'Test Email',
        'template' => 'default',
    ])->send();

    expect($sent)->toBeFalse();
    expect(sentMail())->toBeEmpty();
});

test('an email with nothing in the body is not sent', function () {
    // The rendered message is the email template wrapped around the body, so it is never empty
    // and cannot answer this on its own. Without the body check a site whose message template
    // has been blanked by a filter mails out the wrapper and calls it a success.
    $sent = glsr(Email::class)->compose([
        'to' => 'test@wordpress.dev',
        'subject' => 'Test Email',
        'template' => 'default',
        'message' => '   ',
    ])->send();

    expect($sent)->toBeFalse();
    expect(sentMail())->toBeEmpty();
});

/**
 * A stand-in review for the email templates.
 */
function reviewValues(): array
{
    $faker = Factory::create();
    return [
        'content' => $faker->text(),
        'email' => $faker->email(),
        'name' => $faker->name(),
        'rating' => '5',
        'title' => $faker->sentence(),
    ];
}
