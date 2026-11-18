<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Tests\SubmitsReviews;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(SubmitsReviews::class);

/*
 * What a visitor types into the review form is text. However it is written, the page that shows
 * the review has the elements and attributes it has for a review of plain words, and no others.
 *
 * Each value is submitted as a logged-out visitor in the title, the review and the name, then
 * the review is rendered by the shortcodes and the block with excerpts on and off. The markup
 * is compared with that of the same review written in plain words.
 */

beforeEach(function () {
    resetPluginState();
    $this->setUpSubmitsReviews();
    wp_set_current_user(0);
});

// the name of every tag and of each of its attributes, in document order
function markupOf(string $html): array
{
    $markup = [];
    $tags = new WP_HTML_Tag_Processor($html);
    while ($tags->next_tag()) {
        $attributes = $tags->get_attribute_names_with_prefix('') ?? [];
        sort($attributes);
        $markup[] = trim($tags->get_tag().' '.implode(' ', $attributes));
    }
    return $markup;
}

// a tag whose entities are encoded again and again, as decimal references
function encodedDeep(string $value, int $times): string
{
    for ($i = 0; $i < $times; ++$i) {
        $value = strtr($value, ['&' => '&#38;', '"' => '&#34;', '<' => '&#60;', '>' => '&#62;']);
    }
    return $value;
}

dataset('typed', [
    'a double quote' => ['He said "hello" and left'],
    'a less-than sign' => ['Five < six and six > five'],
    'a tag' => ['<em data-typed="1">word</em>'],
    'a tag with an event' => ['<strong onmouseover="typed()">word</strong>'],
    'a link to a script' => ['<a href="javascript:typed()" title="typed">word</a>'],
    'an image' => ['<img src="typed.png" onerror="typed()">'],
    'a tag encoded eight times' => [encodedDeep('<em data-typed="1">word</em>', 8)],
    'a tag inside a tag' => ['<em da<em>ta-typed="1">word</em>'],
    'a tag with a space after its bracket' => ['< em data-typed="1">word</em>'],
    'a tag with a vertical tab after its bracket' => ["<\x0Bstrong data-typed=\"1\">word</strong>"],
    'braces' => ['{site_title} and {{ review_id }}'],
    'doubled square brackets' => ['[[site_reviews]] and [site_reviews_form]'],
    'the character an excerpt once marked a tag with' => ['one ⍈ two <em class="⍈">three</em>'],
]);

test('a review shows what a visitor typed as text', function (string $typed, string $field) {
    $words = str_repeat('word ', 60); // longer than an excerpt
    $submit = function (string $value) use ($field): int {
        $values = $this->request([
            'content' => 'A review of plain words.',
            'email' => 'visitor@example.org',
            'name' => 'Visitor',
            'rating' => 5,
            'terms' => 1,
            'title' => 'A title',
            $field => $value,
        ]);
        unset($values['form_id']);
        $values[glsr(Honeypot::class)->hash('')] = '';
        return (int) $this->assertJsonSuccess($values)->data->review->ID;
    };
    $render = function (int $reviewId): array { // the review is the only one on the site
        $markup = [];
        foreach (['yes', 'no'] as $excerpts) {
            glsr(OptionManager::class)->set('settings.reviews.excerpts', $excerpts);
            $list = do_shortcode('[site_reviews]');
            expect($list)->toContain('word word'); // the review is on the page
            $markup[] = markupOf($list);
            $markup[] = markupOf(do_shortcode('[site_review post_id="'.$reviewId.'"]'));
            $markup[] = markupOf(do_blocks('<!-- wp:site-reviews/reviews /-->'));
        }
        wp_delete_post($reviewId, true);
        return $markup;
    };

    $plain = $render($submit("plain {$words}"));
    $shown = $render($submit("{$typed} {$words}"));

    expect($shown)->toBe($plain);
})->with('typed')->with(['content', 'name', 'title']);

test('a field sent as a list where text is expected adds nothing to the page', function (string $field) {
    $values = $this->request([
        'content' => 'A review of plain words.',
        'email' => 'visitor@example.org',
        'name' => 'Visitor',
        'rating' => 5,
        'terms' => 1,
        'title' => 'A title',
        $field => ['key' => '<em data-typed="1">word</em>'],
    ]);
    unset($values['form_id']);
    $values[glsr(Honeypot::class)->hash('')] = '';

    $this->performAjaxRequest($values); // refused or accepted, either way

    expect(do_shortcode('[site_reviews]'))->not->toContain('data-typed');
})->with(['content', 'name', 'title']);
