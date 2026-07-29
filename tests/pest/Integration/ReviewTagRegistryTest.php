<?php

use GeminiLabs\SiteReviews\Defaults\ReviewTagDefaults;
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

    // The cautious end of every flag: a name to reserve, and nothing
    // offered anywhere until somebody says what it is for.
    expect($tags)->toHaveKey('undeclared_tag')
        ->and($tags['undeclared_tag'])->toBe(glsr(ReviewTagDefaults::class)->defaults())
        ->and(glsr(ReviewTags::class)->names())->toContain('undeclared_tag')
        ->and(array_keys(glsr(ReviewTags::class)->displayable()))->not->toContain('undeclared_tag')
        ->and(array_keys(glsr(ReviewTags::class)->insertable()))->not->toContain('undeclared_tag');
});

it('fills a partial descriptor from the defaults', function () {
    add_filter('site-reviews/defaults/review-tags/defaults', function (array $tags): array {
        $tags['partial_tag'] = ['display' => true];
        return $tags;
    });
    glsr()->discard('review_tags');

    expect(glsr(ReviewTags::class)->all()['partial_tag'])->toBe([
        'display' => true,
        'group' => 'other',
        'insert' => false,
        'label' => '',
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

it('files the tags a template editor offers under a heading', function () {
    $default = array_keys(glsr(ReviewTags::class)->group('default'));
    $other = array_keys(glsr(ReviewTags::class)->group('other'));

    // The handful an author reaches for, and the rest.
    expect($default)->toContain('author')
        ->and($default)->toContain('content')
        ->and($default)->toContain('rating')
        ->and($other)->toContain('review_id')
        ->and($other)->toContain('assigned_data')
        // Grouping is a division of the insertable tags, nothing more.
        ->and(array_intersect($default, $other))->toBe([])
        ->and(count($default) + count($other))->toBe(count(glsr(ReviewTags::class)->insertable()));
});

it('keeps the stored spellings out of the editor', function () {
    // Reserved so a custom field cannot take the name, never offered as
    // a tag: each is a value shown elsewhere under a better name.
    $insertable = array_keys(glsr(ReviewTags::class)->insertable());

    expect($insertable)->not->toContain('date_gmt')
        ->and($insertable)->not->toContain('ip_address')
        ->and($insertable)->not->toContain('name')
        ->and($insertable)->not->toContain('status')
        ->and(glsr(ReviewTags::class)->names())->toContain('status');
});

it('renamed assigned to assigned_data and kept the old name working', function () {
    $context = glsr_get_review(0)->build()->context;

    expect($context)->toHaveKey('assigned_data')
        ->and($context)->toHaveKey('assigned')
        ->and($context['assigned'])->toBe($context['assigned_data'])
        ->and(array_keys(glsr(ReviewTags::class)->insertable()))->toContain('assigned_data')
        ->and(array_keys(glsr(ReviewTags::class)->insertable()))->not->toContain('assigned');
});

it('sanitizes what a declaration puts in a descriptor', function () {
    // Declared by hand, and what it says decides what an editor offers:
    // a truthy string must not read as "yes, display it", an unknown
    // heading must not create a group nobody renders, and a label is
    // shown as text.
    add_filter('site-reviews/defaults/review-tags/defaults', function (array $tags): array {
        $tags['sloppy_tag'] = [
            'display' => 'yes',
            'group' => 'somewhere-else',
            'insert' => 1,
            'label' => '<b>Bold</b> claim',
            'kind' => 'gone', // no such key
        ];
        return $tags;
    });
    glsr()->discard('review_tags');

    expect(glsr(ReviewTags::class)->all()['sloppy_tag'])->toBe([
        'display' => true,
        'group' => 'other',
        'insert' => true,
        'label' => 'Bold claim',
    ]);
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

it('removes a tag the template still holds but nothing produces', function () {
    // A template written while a feature was active outlives it. The tag
    // is removed before interpolation rather than after, so a reviewer's
    // own braces — by then part of the template — survive.
    add_filter('site-reviews/review/build/after', function (array $tags): array {
        $tags['live_tag'] = 'I am here';
        return $tags;
    });
    $template = 'a:{{ live_tag }} b:{{ retired_tag }} c:{{ another_one }}';
    $filtered = glsr()->filterString('build/template/review', $template, [
        'context' => ['live_tag' => 'I am here'],
    ]);

    expect($filtered)->toBe('a:{{ live_tag }} b: c:');
});

it('leaves alone what is not a tag', function () {
    $template = '{{ data.title }} {{ NOT-A-TAG }} {{ }}';

    expect(glsr()->filterString('build/template/review', $template, ['context' => []]))
        ->toBe($template);
});
