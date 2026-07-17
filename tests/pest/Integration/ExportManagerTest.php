<?php

use GeminiLabs\SiteReviews\Database\ExportManager;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUserAndGet;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The CSV export query builder. The suite already exports by id; the gap was
 * "slug" mode, where every assignment column joins through to a human-readable
 * value so the CSV can be re-imported on a site whose ids differ.
 */

beforeEach(fn () => resetPluginState());

test('slug mode exports every assignment as a name, not an id', function () {
    $author = createUserAndGet();
    $postId = createPost(['post_name' => 'the-reviewed-page', 'post_type' => 'page']);
    $termId = createTerm(['taxonomy' => glsr()->taxonomy, 'slug' => 'the-category']);
    $user = createUserAndGet();
    wp_set_current_user($author->ID); // the review's post_author
    createReview([
        'assigned_posts' => [$postId],
        'assigned_terms' => [$termId],
        'assigned_users' => [$user->ID],
    ]);

    $rows = glsr(ExportManager::class)->export(glsr()->args([
        'assigned_posts' => 'slug',
        'assigned_terms' => 'slug',
        'assigned_users' => 'slug',
        'author_id' => 'slug',
        'date' => '2000-01-01', // exercises the post_date cutoff without excluding anything
    ]));

    expect($rows)->toHaveCount(1)
        ->and($rows[0]['assigned_posts'])->toBe('page:the-reviewed-page')
        ->and($rows[0]['assigned_terms'])->toBe('the-category')
        ->and($rows[0]['assigned_users'])->toBe($user->user_login)
        ->and($rows[0]['author_id'])->toBe($author->user_login);
});

test('id mode exports ids, and a zero limit means no LIMIT at all', function () {
    $postId = createPost();
    createReview(['assigned_posts' => [$postId]]);

    $rows = glsr(ExportManager::class)->export(glsr()->args(['limit' => 0]));

    expect(count($rows))->toBeGreaterThanOrEqual(1);
    $row = end($rows);
    expect($row['assigned_posts'])->toBe((string) $postId);
});
