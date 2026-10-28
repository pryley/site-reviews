<?php

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\RelationController;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\RelationSaveHelper;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Url\Url;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function Inpsyde\MultilingualPress\translationIds;

/*
 * The MultilingualPress integration against the real plugin on the network's two related
 * sites (tests/multisite/setup-multilingualpress.sh). Site 1 is en-US and the second site
 * de-DE. Without the plugin every test here skips so a fork and a plain run still pass.
 *
 * A review is submitted the front-end way with CreateReview and ReviewManager::create().
 * The listeners that copy and sync skip glsr_create_review() and glsr_update_review().
 * Each test deletes the reviews it made on both sites.
 *
 * Trash and restore cannot run here: EMPTY_TRASH_DAYS is 0 in this env so wp_trash_post()
 * deletes the review and TrasherController::syncTrash() deletes its translation the same way.
 */

beforeEach(function () {
    if (!function_exists('Inpsyde\MultilingualPress\translationIds')) {
        $this->markTestSkipped('MultilingualPress is not installed in the multisite env.');
    }
});

afterEach(function () {
    remove_all_filters('site-reviews/defaults/sync-review');
    wp_set_current_user(0);
    foreach ([1, mlpSecondSiteId()] as $siteId) {
        switch_to_blog($siteId);
        $postIds = get_posts(['fields' => 'ids', 'post_status' => 'any', 'post_type' => glsr()->post_type, 'posts_per_page' => -1]);
        foreach ($postIds as $postId) {
            wp_delete_post($postId, true);
        }
        restore_current_blog();
    }
});

function mlpSecondSiteId(): int
{
    return (int) get_blog_id_from_url(get_network()->domain, '/second/');
}

function mlpUser(string $login): int
{
    $user = get_user_by('login', $login);
    if ($user) {
        return $user->ID;
    }
    return (int) wp_insert_user(['role' => 'author', 'user_login' => $login, 'user_pass' => wp_generate_password()]);
}

function mlpSubmitReview(array $values = []): Review
{
    $request = new Request($values + [
        'content' => 'The source review is comfortably long enough to pass every threshold.',
        'email' => 'reviewer@example.org',
        'name' => 'Reviewer',
        'rating' => 5,
        'title' => 'The source title',
    ]);
    $review = glsr(ReviewManager::class)->create(new CreateReview($request));
    if (!$review instanceof Review) {
        throw new RuntimeException('Review not created.');
    }
    return $review;
}

function mlpCopyId(Review $review): int
{
    return (int) (translationIds($review->ID, 'post', 1)[mlpSecondSiteId()] ?? 0);
}

/**
 * @return mixed
 */
function mlpOnSecondSite(callable $callback)
{
    switch_to_blog(mlpSecondSiteId());
    try {
        return $callback();
    } finally {
        restore_current_blog();
    }
}

function mlpCopy(Review $review): Review
{
    $copyId = mlpCopyId($review);
    return mlpOnSecondSite(function () use ($copyId) {
        wp_cache_flush();
        return glsr_get_review($copyId);
    });
}

/**
 * The translation box's save request, with no Author box posted.
 */
function mlpBoxRequest(): ServerRequest
{
    return new class() implements ServerRequest {
        public function body(): string
        {
            return '';
        }

        public function bodyValue(string $name, int $source = self::INPUT_REQUEST, int $filter = \FILTER_UNSAFE_RAW, int $options = \FILTER_FLAG_NONE)
        {
            return null;
        }

        public function header(string $name): string
        {
            return '';
        }

        public function method(): string
        {
            return 'POST';
        }

        public function serverValue(string $name): string
        {
            return '';
        }

        public function url(): Url
        {
            return new class() implements Url {
                public function __toString(): string
                {
                    return '';
                }
            };
        }
    };
}

test('a submitted review is copied to the related site with its author, text and custom field values', function () {
    $authorId = mlpUser('mlp_author');
    wp_set_current_user($authorId);
    $review = mlpSubmitReview(['note' => 'their words', 'pick' => 'yes']);

    expect(mlpCopyId($review))->toBeGreaterThan(0);
    $copy = mlpCopy($review);
    expect($copy->isValid())->toBeTrue()
        ->and($copy->author_id)->toBe($authorId)
        ->and($copy->rating)->toBe(5)
        ->and($copy->title)->toBe('The source title')
        ->and($copy->custom()->toArray())->toBe(['note' => 'their words', 'pick' => 'yes']);
    $hash = get_post_meta($review->ID, '_submitted_hash', true);
    expect($hash)->not->toBe('') // a meta row MultilingualPress is told to sync (RelationController::filterSyncedMetaKeys)
        ->and(mlpOnSecondSite(fn () => get_post_meta($copy->ID, '_submitted_hash', true)))->toBe($hash);
});

test('a front-end edit moves the rating and leaves the translation\'s title, content and custom field values', function () {
    $review = mlpSubmitReview(['note' => 'their words', 'pick' => 'yes']);
    $copyId = mlpCopyId($review);
    mlpOnSecondSite(function () use ($copyId) {
        wp_update_post(['ID' => $copyId, 'post_content' => 'Der übersetzte Text.', 'post_title' => 'Der Titel']);
        update_post_meta($copyId, '_custom_note', 'ihre Worte');
    });

    glsr(ReviewManager::class)->update($review->ID, [
        'content' => 'The edited source content.',
        'note' => 'their edited words',
        'pick' => 'no',
        'rating' => 3,
        'title' => 'The edited source title',
    ]);

    $copy = mlpCopy($review);
    expect($copy->rating)->toBe(3)
        ->and($copy->title)->toBe('Der Titel')
        ->and($copy->content)->toBe('Der übersetzte Text.')
        ->and($copy->custom()->toArray())->toBe(['note' => 'ihre Worte', 'pick' => 'yes']);
});

test('a listener on the sync-review filter lets a custom field value follow the review', function () {
    $review = mlpSubmitReview(['note' => 'their words', 'pick' => 'yes']);
    add_filter('site-reviews/defaults/sync-review', function (array $values, string $method, array $args): array {
        if (array_key_exists('pick', $args)) {
            $values['pick'] = $args['pick'];
        }
        return $values;
    }, 10, 3);

    glsr(ReviewManager::class)->update($review->ID, ['note' => 'their edited words', 'pick' => 'no']);

    expect(mlpCopy($review)->custom()->toArray())->toBe(['note' => 'their words', 'pick' => 'no']);
});

test('a response reaches the translation only while it has none', function () {
    $review = mlpSubmitReview();

    glsr(ReviewManager::class)->updateResponse($review->ID, ['response' => 'Thank you.']);
    expect(mlpCopy($review)->response)->toBe('Thank you.');

    glsr(ReviewManager::class)->updateResponse($review->ID, ['response' => 'Thank you, changed.']);
    expect(mlpCopy($review)->response)->toBe('Thank you.');
});

test('deleting a review deletes its translation', function () {
    $review = mlpSubmitReview();
    $copyId = mlpCopyId($review);

    wp_trash_post($review->ID); // EMPTY_TRASH_DAYS is 0 in this env so a trash is a delete

    expect(get_post($review->ID))->toBeNull()
        ->and(mlpOnSecondSite(fn () => get_post($copyId)))->toBeNull();
});

test('a translation created from the box starts with the custom field values and keeps its author without an Author box', function () {
    $source = glsr_create_review([ // the helper: no copy is made on creation
        'author_id' => mlpUser('mlp_author'),
        'content' => 'The source review is comfortably long enough to pass every threshold.',
        'note' => 'their words',
        'pick' => 'yes',
        'rating' => 4,
    ]);
    expect(mlpCopyId($source))->toBe(0);
    $translatorId = mlpUser('mlp_translator');
    $remoteId = mlpOnSecondSite(fn () => wp_insert_post([ // what the box saves before its hook
        'post_author' => $translatorId,
        'post_status' => 'publish',
        'post_title' => 'Die Übersetzung',
        'post_type' => glsr()->post_type,
    ]));
    $context = new RelationshipContext([
        RelationshipContext::REMOTE_POST_ID => $remoteId,
        RelationshipContext::REMOTE_SITE_ID => mlpSecondSiteId(),
        RelationshipContext::SOURCE_POST_ID => $source->ID,
        RelationshipContext::SOURCE_SITE_ID => 1,
    ]);
    (new RelationSaveHelper($context))->relateReviews();

    // MultilingualPress fires the hook on the remote site.
    mlpOnSecondSite(fn () => glsr(RelationController::class)->onSyncReview($context, mlpBoxRequest()));

    $copy = mlpCopy($source);
    expect($copy->ID)->toBe($remoteId)
        ->and($copy->isValid())->toBeTrue()
        ->and($copy->rating)->toBe(4)
        ->and($copy->custom()->toArray())->toBe(['note' => 'their words', 'pick' => 'yes'])
        ->and((int) mlpOnSecondSite(fn () => get_post_field('post_author', $remoteId)))->toBe($translatorId);
});
