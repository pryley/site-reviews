<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Migrations\Migrate_8_1_0;
use WP_UnitTestCase;

/**
 * Regression test for the post_date_gmt backfill migration.
 * @group plugin
 */
class Migrate_8_1_0Test extends WP_UnitTestCase
{
    use Setup;

    public function test_backfills_missing_post_date_gmt_with_dst_offsets()
    {
        update_option('timezone_string', 'America/New_York');
        $winter = $this->createBrokenReview('2026-01-15 12:00:00'); // EST (UTC-5)
        $summer = $this->createBrokenReview('2026-07-15 12:00:00'); // EDT (UTC-4)
        $this->assertTrue((new Migrate_8_1_0())->run());
        $this->assertSame('2026-01-15 17:00:00', $this->postDateGmt($winter));
        $this->assertSame('2026-07-15 16:00:00', $this->postDateGmt($summer));
        // parity with the WordPress core conversion
        $this->assertSame(get_gmt_from_date('2026-01-15 12:00:00'), $this->postDateGmt($winter));
        $this->assertSame(get_gmt_from_date('2026-07-15 12:00:00'), $this->postDateGmt($summer));
    }

    public function test_backfills_dst_boundary_dates()
    {
        update_option('timezone_string', 'America/New_York');
        // 02:30 does not exist (clocks jump 02:00 -> 03:00 on 2026-03-08)
        $gap = $this->createBrokenReview('2026-03-08 02:30:00');
        // 01:30 occurs twice (clocks fall back 02:00 -> 01:00 on 2026-11-01)
        $ambiguous = $this->createBrokenReview('2026-11-01 01:30:00');
        $this->assertTrue((new Migrate_8_1_0())->run());
        // Either UTC offset is a defensible interpretation of a nonexistent
        // or ambiguous local time; the regression being guarded against is
        // boundary rows being skipped and left with a zero date.
        $this->assertContains($this->postDateGmt($gap), ['2026-03-08 06:30:00', '2026-03-08 07:30:00']);
        $this->assertContains($this->postDateGmt($ambiguous), ['2026-11-01 05:30:00', '2026-11-01 06:30:00']);
    }

    public function test_backfills_with_fixed_offset_timezone()
    {
        update_option('timezone_string', '');
        update_option('gmt_offset', 5.5); // UTC+05:30 (no DST)
        $review = $this->createBrokenReview('2026-01-15 12:00:00');
        $this->assertTrue((new Migrate_8_1_0())->run());
        $this->assertSame('2026-01-15 06:30:00', $this->postDateGmt($review));
    }

    public function test_ignores_unaffected_posts()
    {
        update_option('timezone_string', 'America/New_York');
        $valid = $this->createBrokenReview('2026-01-15 12:00:00', '2026-01-15 17:00:00'); // post_date_gmt is already set
        $zeroDates = $this->createBrokenReview('0000-00-00 00:00:00'); // no post_date to backfill from
        $notReview = self::factory()->post->create();
        $this->setPostDates($notReview, '2026-01-15 12:00:00', '0000-00-00 00:00:00');
        $this->assertTrue((new Migrate_8_1_0())->run());
        $this->assertSame('2026-01-15 17:00:00', $this->postDateGmt($valid));
        $this->assertSame('0000-00-00 00:00:00', $this->postDateGmt($zeroDates));
        $this->assertSame('0000-00-00 00:00:00', $this->postDateGmt($notReview));
    }

    public function test_is_idempotent()
    {
        update_option('timezone_string', 'America/New_York');
        $review = $this->createBrokenReview('2026-01-15 12:00:00');
        $migration = new Migrate_8_1_0();
        $this->assertTrue($migration->run());
        $this->assertTrue($migration->run()); // nothing left to migrate
        $this->assertSame('2026-01-15 17:00:00', $this->postDateGmt($review));
    }

    protected function createBrokenReview(string $postDate, string $postDateGmt = '0000-00-00 00:00:00'): int
    {
        $postId = self::factory()->post->create(['post_type' => glsr()->post_type]);
        $this->setPostDates($postId, $postDate, $postDateGmt);
        return $postId;
    }

    protected function postDateGmt(int $postId): string
    {
        clean_post_cache($postId);
        return get_post($postId)->post_date_gmt;
    }

    protected function setPostDates(int $postId, string $postDate, string $postDateGmt): void
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
        $this->assertSame($postDate, $post->post_date);
        $this->assertSame($postDateGmt, $post->post_date_gmt);
    }
}
