<?php

use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\RelationSaveHelper;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ReviewCopier;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

/*
 * What travels to a review's translations: everything once, when the copy is made (ReviewCopier::copy
 * on a submission, RelationSaveHelper::syncCustomFields from the translation box), and on a front-end
 * edit (ReviewCopier::sync) the review's facts but no custom field value.
 *
 * The copier flattens the custom field values to the top level of the data, so the guard's
 * "custom" entry alone never met them: every value reached glsr_update_review() on the copy,
 * and ReviewManager::updateCustom() wrote them over the translation's own. The data is
 * guarded on the source site, before the copier switches, so that a listener on
 * site-reviews/defaults/sync-review can read the review's form there and release a value.
 *
 * copy() and sync() themselves are not run here: translationIds() is a stub with no body.
 */

function syncDataOf(array $values = []): array
{
    $review = createReview($values);
    return (new ReviewCopier($review->ID, get_current_blog_id()))->syncData($review);
}

/*
 * The helper for a relation between two posts of this one site: the context answers from its
 * own ids, since the stub's constructor stores nothing, and the helper skips the container and
 * the site switch, since the stub's resolve() returns nothing and the switch has no other site.
 */
function relationHelper(int $sourcePostId, int $remotePostId): RelationSaveHelper
{
    $context = new class() extends RelationshipContext {
        public int $remote = 0;
        public int $source = 0;

        public function hasRemotePost(): bool
        {
            return $this->remote > 0;
        }

        public function remotePost(): ?\WP_Post
        {
            return get_post($this->remote);
        }

        public function remotePostId(): int
        {
            return $this->remote;
        }

        public function remoteSiteId(): int
        {
            return 2;
        }

        public function sourcePost(): \WP_Post
        {
            return get_post($this->source);
        }

        public function sourcePostId(): int
        {
            return $this->source;
        }

        public function sourceSiteId(): int
        {
            return 1;
        }
    };
    $context->remote = $remotePostId;
    $context->source = $sourcePostId;
    return new class($context) extends RelationSaveHelper {
        public function __construct(RelationshipContext $context)
        {
            $this->context = $context;
        }

        protected function maybeSwitchSite(int $targetSiteId): int
        {
            return get_current_blog_id();
        }

        protected function maybeRestoreSite(int $originalSiteId): bool
        {
            return true;
        }
    };
}

test('a submission\'s copy is created with the custom field values', function () {
    $review = createReview(['note' => 'their words', 'pick' => 'yes']);
    $data = (new ReviewCopier($review->ID, get_current_blog_id()))->copyData($review);
    expect($data['note'])->toBe('their words')
        ->and($data['pick'])->toBe('yes');
    $copy = glsr_create_review($data); // what copy() runs on the remote site
    expect($copy->ID)->not->toBe($review->ID)
        ->and($copy->custom()->toArray())->toBe(['note' => 'their words', 'pick' => 'yes']);
});

test('a translation created from the box is new until it has a rating row, and starts with the custom field values', function () {
    $source = createReview(['note' => 'their words', 'pick' => 'yes']);
    $remoteId = wp_insert_post([
        'post_status' => 'publish',
        'post_title' => 'The translation',
        'post_type' => glsr()->post_type,
    ]);
    $helper = relationHelper($source->ID, $remoteId);
    expect($helper->isNewRemoteReview())->toBeTrue();
    $helper->syncCustomFields();
    expect(get_post_meta($remoteId, '_custom_note', true))->toBe('their words')
        ->and(get_post_meta($remoteId, '_custom_pick', true))->toBe('yes');
    $existing = createReview();
    expect(relationHelper($source->ID, $existing->ID)->isNewRemoteReview())->toBeFalse();
});

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
