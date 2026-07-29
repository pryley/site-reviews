<?php

use GeminiLabs\SiteReviews\Defaults\ReviewTagsDefaults;
use GeminiLabs\SiteReviews\Modules\Html\ReviewTags;

/*
 * The review tag registry: the one description of what a template tag is
 * fit for, projected into the reserved list, a template editor's chips
 * and a layout builder's palette.
 *
 * The census matters more than the descriptors. A tag is whatever a
 * build produces, so the registry has to answer for tags it has never
 * heard of — otherwise describing tags becomes a gate on having them.
 */

beforeEach(function () {
    glsr()->discard('review_tags');
});

afterEach(function () {
    glsr()->discard('review_tags');
});

it('names every tag an actual build produces', function () {
    $names = glsr(ReviewTags::class)->names();
    $context = array_keys(glsr_get_review(0)->build()->context);

    expect(array_values(array_diff($context, $names)))->toBe([])
        ->and(array_values(array_diff($names, $context)))->toBe([]);
});

it('answers for a tag nobody described', function () {
    // A feature that adds a tag through review/build/after alone still
    // has a tag; it simply takes the cautious defaults.
    add_filter('site-reviews/review/build/after', function (array $tags): array {
        $tags['undeclared_tag'] = '';
        return $tags;
    });
    glsr()->discard('review_tags');

    $tags = glsr(ReviewTags::class)->all();

    expect($tags)->toHaveKey('undeclared_tag')
        ->and($tags['undeclared_tag'])->toBe(ReviewTagsDefaults::DESCRIPTOR)
        ->and(glsr(ReviewTags::class)->names())->toContain('undeclared_tag')
        ->and(array_keys(glsr(ReviewTags::class)->displayable()))->not->toContain('undeclared_tag');
});

it('fills a partial descriptor from the defaults', function () {
    add_filter('site-reviews/defaults/review-tags/defaults', function (array $tags): array {
        $tags['partial_tag'] = ['display' => true];
        return $tags;
    });
    glsr()->discard('review_tags');

    expect(glsr(ReviewTags::class)->all()['partial_tag'])->toBe([
        'display' => true,
        'insert' => true,
        'source' => 'review',
    ]);
});

it('separates what may be inserted from what may be displayed', function () {
    $tags = glsr(ReviewTags::class);

    // The aggregate of every custom value: a name to reserve, not a tag
    // to write or a block to draw.
    expect($tags->names())->toContain('custom')
        ->and(array_keys($tags->insertable()))->not->toContain('custom')
        ->and(array_keys($tags->displayable()))->not->toContain('custom')
        // The JSON blob of assignments, read by scripts off the wrapper.
        ->and(array_keys($tags->insertable()))->not->toContain('assigned')
        // A raw scalar: worth writing into an attribute, not a block.
        ->and(array_keys($tags->insertable()))->toContain('review_id')
        ->and(array_keys($tags->displayable()))->not->toContain('review_id')
        // A block.
        ->and(array_keys($tags->displayable()))->toContain('author');
});

it('knows which tags a form supplies instead of the review', function () {
    $source = array_keys(glsr(ReviewTags::class)->source('form'));

    expect($source)->toContain('content')
        ->and($source)->toContain('title')
        ->and($source)->not->toContain('author');
});

it('survives a filter that asks for the tags while the review is being built', function () {
    // The census builds a mock review, which runs filters — one of which
    // may want the tags. The guard answers from the declared list rather
    // than recursing until the stack gives out.
    add_filter('site-reviews/review/build/after', function (array $tags): array {
        $tags['reentrant_tag'] = implode(',', glsr(ReviewTags::class)->names());
        return $tags;
    });
    glsr()->discard('review_tags');

    expect(glsr(ReviewTags::class)->names())->toContain('reentrant_tag');
});
