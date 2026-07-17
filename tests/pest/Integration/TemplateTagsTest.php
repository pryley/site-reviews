<?php

use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The {tag} vocabulary of the notification emails and verification messages.
 * NotificationTest proves the tags land in a real email; this is the tags that
 * no shipped notification template uses by default.
 */

beforeEach(fn () => resetPluginState());

test('the tag list renders as documentation for the settings screen', function () {
    $list = glsr(TemplateTags::class)->listTags(['include' => ['review_author', 'review_rating']]);

    expect($list)->toBe('<ul><li><code>{review_author}</code></li><li><code>{review_rating}</code></li></ul>');
});

test('a tag can be inserted into the vocabulary as well as filtered out', function () {
    $tags = glsr(TemplateTags::class)->filteredTags(['insert' => ['my_custom_tag' => 'What it does']]);

    expect($tags)->toHaveKey('my_custom_tag')
        ->and($tags['my_custom_tag'])->toBe('What it does');
});

test('the informational tags answer from the site and the review', function () {
    $review = createReview(['rating' => 3]);
    $tags = glsr(TemplateTags::class);

    expect($tags->tagAdminEmail())->toBe(get_bloginfo('admin_email'))
        ->and($tags->tagReviewResponse($review))->toBe('')
        ->and($tags->tagReviewStars($review))->toBe('★★★☆☆')
        ->and($tags->tagVerifyUrl($review))->toContain('glsr_='); // encrypted, never the plain id

    // @compat v6: review_link is an anchor to the edit screen
    expect($tags->tagReviewLink($review))->toStartWith('<a href=')
        ->and($tags->tagReviewLink($review))->toContain('post.php');
});

test('the verified date is only a date for a review that was verified', function () {
    $review = createReview();
    $tags = glsr(TemplateTags::class);

    expect($tags->tagVerifiedDate($review))->toBe(''); // never verified

    glsr(PostMeta::class)->set($review->ID, 'verified_on', time());
    // a FRESH instance: the cached review above already memoized its (empty) meta
    $verified = new \GeminiLabs\SiteReviews\Review(get_post($review->ID));
    expect($tags->tagVerifiedDate($verified))->not->toBe('');
});
