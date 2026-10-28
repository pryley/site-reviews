<?php

use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ReviewCopier;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

/*
 * What a front-end edit sends to a review's translations (ReviewCopier::sync).
 *
 * The copier flattens the custom field values to the top level of the data, so the guard's
 * "custom" entry alone never met them: every value reached glsr_update_review() on the copy,
 * and ReviewManager::updateCustom() wrote them over the translation's own. The data is
 * guarded on the source site, before the copier switches, so that a listener on
 * site-reviews/defaults/sync-review can read the review's form there and release a value.
 *
 * sync() itself is not run here: translationIds() is a stub with no body.
 */

function syncDataOf(array $values = []): array
{
    $review = createReview($values);
    return (new ReviewCopier($review->ID, get_current_blog_id()))->syncData($review);
}

test('a front-end edit sends no custom field value to a translation', function () {
    $data = syncDataOf(['note' => 'their words', 'pick' => 'yes']);
    expect($data)->not->toHaveKey('note')
        ->and($data)->not->toHaveKey('pick')
        ->and($data)->not->toHaveKey('custom')
        ->and($data['rating'])->toBe(5)
        ->and(glsr(CustomFieldsDefaults::class)->merge($data))->toBe([]); // nothing for updateCustom() to write on the copy
});

test('the sync data keeps the source assigned ids for the remote mapping', function () {
    $postId = createPost();
    $data = syncDataOf(['assigned_posts' => [$postId]]);
    expect($data['assigned_posts'])->toBe([$postId])
        ->and($data['assigned_users'])->toBe([]);
});

test('a listener on the sync-review filter can release a custom field value from the unguarded input', function () {
    $listener = function (array $values, string $method, array $args): array {
        $values['pick'] = $args['pick'];
        return $values;
    };
    add_filter('site-reviews/defaults/sync-review', $listener, 10, 3);
    $data = syncDataOf(['note' => 'their words', 'pick' => 'yes']);
    remove_filter('site-reviews/defaults/sync-review', $listener, 10);
    expect($data['pick'])->toBe('yes')
        ->and($data)->not->toHaveKey('note');
});
