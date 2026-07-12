<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_8_1_0;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

test('backfills missing post date gmt with dst offsets', function () {
    update_option('timezone_string', 'America/New_York');
    $winter = createBrokenReview('2026-01-15 12:00:00'); // EST (UTC-5)
    $summer = createBrokenReview('2026-07-15 12:00:00'); // EDT (UTC-4)
    expect((new Migrate_8_1_0())->run())->toBeTrue();
    expect(postDateGmt($winter))->toBe('2026-01-15 17:00:00');
    expect(postDateGmt($summer))->toBe('2026-07-15 16:00:00');
    // parity with the WordPress core conversion
    expect(get_gmt_from_date('2026-01-15 12:00:00'))->toBe(postDateGmt($winter));
    expect(get_gmt_from_date('2026-07-15 12:00:00'))->toBe(postDateGmt($summer));
});

test('backfills dst boundary dates', function () {
    update_option('timezone_string', 'America/New_York');
    // 02:30 does not exist (clocks jump 02:00 -> 03:00 on 2026-03-08)
    $gap = createBrokenReview('2026-03-08 02:30:00');
    // 01:30 occurs twice (clocks fall back 02:00 -> 01:00 on 2026-11-01)
    $ambiguous = createBrokenReview('2026-11-01 01:30:00');
    expect((new Migrate_8_1_0())->run())->toBeTrue();
    // Either UTC offset is a defensible interpretation of a nonexistent
    // or ambiguous local time; the regression being guarded against is
    // boundary rows being skipped and left with a zero date.
    expect(['2026-03-08 06:30:00', '2026-03-08 07:30:00'])->toContain(postDateGmt($gap));
    expect(['2026-11-01 05:30:00', '2026-11-01 06:30:00'])->toContain(postDateGmt($ambiguous));
});

test('backfills with fixed offset timezone', function () {
    update_option('timezone_string', '');
    update_option('gmt_offset', 5.5); // UTC+05:30 (no DST)
    $review = createBrokenReview('2026-01-15 12:00:00');
    expect((new Migrate_8_1_0())->run())->toBeTrue();
    expect(postDateGmt($review))->toBe('2026-01-15 06:30:00');
});

test('ignores unaffected posts', function () {
    update_option('timezone_string', 'America/New_York');
    $valid = createBrokenReview('2026-01-15 12:00:00', '2026-01-15 17:00:00'); // post_date_gmt is already set
    $zeroDates = createBrokenReview('0000-00-00 00:00:00'); // no post_date to backfill from
    $notReview = createPost();
    setPostDates($notReview, '2026-01-15 12:00:00', '0000-00-00 00:00:00');
    expect((new Migrate_8_1_0())->run())->toBeTrue();
    expect(postDateGmt($valid))->toBe('2026-01-15 17:00:00');
    expect(postDateGmt($zeroDates))->toBe('0000-00-00 00:00:00');
    expect(postDateGmt($notReview))->toBe('0000-00-00 00:00:00');
});

test('is idempotent', function () {
    update_option('timezone_string', 'America/New_York');
    $review = createBrokenReview('2026-01-15 12:00:00');
    $migration = new Migrate_8_1_0();
    expect($migration->run())->toBeTrue();
    expect($migration->run())->toBeTrue(); // nothing left to migrate
    expect(postDateGmt($review))->toBe('2026-01-15 17:00:00');
});

function createBrokenReview(string $postDate, string $postDateGmt = '0000-00-00 00:00:00'): int
{
    $postId = createPost(['post_type' => glsr()->post_type]);
    setPostDates($postId, $postDate, $postDateGmt);
    return $postId;
}

function postDateGmt(int $postId): string
{
    clean_post_cache($postId);
    return get_post($postId)->post_date_gmt;
}

function setPostDates(int $postId, string $postDate, string $postDateGmt): void
{
    global $wpdb;
    $wpdb->update($wpdb->posts,
        ['post_date' => $postDate, 'post_date_gmt' => $postDateGmt],
        ['ID' => $postId]
    );
    clean_post_cache($postId);
    // Verify the fixture before migrating, so a failed date write cannot
    // pass vacuously or fail with a misleading assertion message later.
    $post = get_post($postId);
    expect($postDate)->toBe($post->post_date);
    expect($postDateGmt)->toBe($post->post_date_gmt);
}
