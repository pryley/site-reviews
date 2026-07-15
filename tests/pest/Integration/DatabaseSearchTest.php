<?php

use GeminiLabs\SiteReviews\Database;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The four little search classes behind the review editor's autocompletes.
 *
 * Each takes a string the person typed and turns it into rows. AbstractSearch::search() forks on
 * the shape of that string BEFORE either subclass sees it: an empty term matches nothing (so a blank
 * box does not LIKE '%%' the whole table), a numeric one is looked up by id, and anything else is a
 * name search. The by-name paths and the assigned-user search are pinned in ReviewsCollectionTest;
 * this covers the by-id paths, the ->posts()/->users() hydration, and the empty-term fork.
 *
 *   SearchAssignedPosts / SearchAssignedUsers   only among posts/users a review is ALREADY assigned
 *                                               to — the box for finding the thing reviewed, or the
 *                                               reviewer, not for browsing the whole site.
 *   SearchUsers                                 any user, for the "assign to user" box.
 */

beforeEach(function () {
    resetPluginState();
});

/*
 * Assigned posts.
 */

test('an assigned post can be found by name, and hydrated to a WP_Post', function () {
    $postId = createPost(['post_title' => 'The Grand Hotel']);
    createReview(['assigned_posts' => $postId]);

    $found = glsr(Database::class)->searchAssignedPosts('Grand')->posts();

    expect($found)->toHaveCount(1)
        ->and($found[0])->toBeInstanceOf(WP_Post::class)
        ->and($found[0]->ID)->toBe($postId);
});

test('an assigned post can be found by its id', function () {
    // A numeric term takes the searchById branch — the editor uses it to re-hydrate a post it
    // already has the id for, rather than round-tripping through the title.
    $postId = createPost(['post_title' => 'The Grand Hotel']);
    createReview(['assigned_posts' => $postId]);

    $found = glsr(Database::class)->searchAssignedPosts((string) $postId)->posts();

    expect($found)->toHaveCount(1)
        ->and($found[0]->ID)->toBe($postId);
});

test('a post with no review assigned to it is not offered', function () {
    createPost(['post_title' => 'The Grand Hotel']); // exists, but nobody reviewed it

    expect(glsr(Database::class)->searchAssignedPosts('Grand')->posts())->toBe([]);
});

/*
 * Assigned users, by id — the by-name paths are in ReviewsCollectionTest.
 */

test('an assigned user can be found by their id', function () {
    $userId = createUser(['display_name' => 'Jane Doe']);
    createReview(['assigned_users' => $userId]);

    $found = glsr(Database::class)->searchAssignedUsers((string) $userId)->users();

    expect($found)->toHaveCount(1)
        ->and($found[0]->ID)->toBe($userId);
});

test('an empty search term matches nobody rather than everybody', function () {
    // The fork in AbstractSearch::search(): a blank term short-circuits to an empty result set
    // instead of a LIKE that matches every row in the table.
    createReview(['assigned_users' => createUser()]);

    expect(glsr(Database::class)->searchAssignedUsers('')->results())->toBe([])
        ->and(glsr(Database::class)->searchAssignedUsers('')->users())->toBe([]);
});

test('the assigned-user search renders nothing of its own', function () {
    // It feeds an autocomplete through ->users(); it has no HTML form, so it inherits the base
    // render() that returns an empty string (unlike SearchUsers, which does render).
    expect(glsr(Database::class)->searchAssignedUsers('anything')->render())->toBe('');
});

/*
 * Any user.
 */

test('the user search finds any user by name, assigned a review or not', function () {
    $userId = createUser(['display_name' => 'Searchable Sam', 'user_login' => 'sams']);

    $found = glsr(Database::class)->searchUsers('Searchable')->users();
    $ids = array_map(fn ($user) => $user->ID, $found);

    expect($ids)->toContain($userId);
});

test('the user search finds any user by their id', function () {
    $userId = createUser(['display_name' => 'Sam']);

    $found = glsr(Database::class)->searchUsers((string) $userId)->users();

    expect($found)->toHaveCount(1)
        ->and($found[0]->ID)->toBe($userId);
});
