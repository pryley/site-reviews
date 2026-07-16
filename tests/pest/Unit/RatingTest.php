<?php

use GeminiLabs\SiteReviews\Modules\Rating;

uses()->group('plugin');

/*
 * The rating arithmetic behind every star and every "4.5 out of 5" the plugin prints.
 * Rating counts arrive as [rating => count] arrays; index 0 is the 0-star bucket, which
 * is ignored in averages by default (the rating/ignore-zero-stars filter).
 */

test('the labels follow the maximum rating', function () {
    // Five stars have names; any other scale falls back to counting.
    expect(Rating::labels())->toBe([
        5 => 'Excellent',
        4 => 'Very good',
        3 => 'Average',
        2 => 'Poor',
        1 => 'Terrible',
    ]);

    $threeStars = fn () => 3;
    add_filter('site-reviews/const/MAX_RATING', $threeStars);
    try {
        expect(Rating::labels())->toBe([
            3 => '3 stars',
            2 => '2 stars',
            1 => '1 star',
        ]);
    } finally {
        remove_filter('site-reviews/const/MAX_RATING', $threeStars);
    }
});

test('the options list survives a nooped plural without a placeholder', function () {
    // Arr::unique() flattens defaults by value, so a translation that dropped the %s would
    // otherwise collapse every option into one — the number is prefixed back on.
    $options = glsr(Rating::class)->optionsArray(_n_noop('Star', 'Stars', 'site-reviews'));

    expect($options[5])->toBe('5 Stars')
        ->and($options[1])->toBe('1 Star');
    expect(array_keys($options))->toBe([5, 4, 3, 2, 1]);
});

test('the overall percentage is the average against the maximum', function () {
    // two 3-star and two 5-star ratings: average 4 of 5 = 80%
    expect(glsr(Rating::class)->overallPercentage([3 => 2, 5 => 2]))->toBe(80.0);
});

test('percentages of nothing are all zero, not a division by zero', function () {
    expect(glsr(Rating::class)->percentages([]))
        ->toEqual([5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0, 0 => 0]);
});

test('rounded percentages always sum to exactly 100', function () {
    // Three equal thirds floor to 99; the largest remainder (ties broken by highest rating)
    // absorbs the missing point. A bar chart that sums to 99% gets bug reports.
    $percentages = glsr(Rating::class)->percentages([1 => 1, 2 => 1, 3 => 1]);

    expect(array_sum($percentages))->toEqual(100);
    expect($percentages)->toEqual([3 => 34, 2 => 33, 1 => 33]);
});

test('the wilson lower bound ranks up/down votes conservatively', function () {
    // [down, up] counts. No votes ranks zero; 8 of 10 positive at 95% confidence is
    // 0.4902 — the formula's own answer, worked from the source.
    $rating = glsr(Rating::class);

    expect($rating->lowerBound())->toBe(0.0);
    expect($rating->lowerBound([2, 8], 95))->toEqualWithDelta(0.4902, 0.0005);
    // more of the same evidence moves the bound up
    expect($rating->lowerBound([20, 80], 95))->toBeGreaterThan($rating->lowerBound([2, 8], 95));
});

test('the z-score ranking rewards both quality and quantity', function () {
    $rating = glsr(Rating::class);

    // No ratings: weight 3 minus the uncertainty penalty — 3 - 1.64485 * sqrt(2/6), from
    // the formula with every bucket getting its +1 smoothing.
    expect($rating->rankingUsingZScores([0, 0, 0, 0, 0, 0]))->toEqualWithDelta(2.0503, 0.001);

    $few = $rating->rankingUsingZScores([0, 0, 0, 0, 0, 2]);
    $many = $rating->rankingUsingZScores([0, 0, 0, 0, 0, 20]);
    expect($many)->toBeGreaterThan($few); // same average, more evidence
    expect($few)->toBeGreaterThan($rating->rankingUsingZScores([0, 2, 0, 0, 0, 0]));
});
