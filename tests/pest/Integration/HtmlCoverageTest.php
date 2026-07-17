<?php

use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\FieldCondition;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxField;
use GeminiLabs\SiteReviews\Modules\Html\SettingBuilder;
use GeminiLabs\SiteReviews\Modules\Html\WidgetBuilder;
use GeminiLabs\SiteReviews\Modules\Html\WidgetField;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The corners of the HTML machinery the rendering tests never turn: attribute
 * normalization edges, the raw field variants, and the condition evaluator.
 */

beforeEach(fn () => resetPluginState());

/*
 * Attributes.
 */

test('attributes can be replaced wholesale and read back', function () {
    $attributes = glsr(Attributes::class)->div(['id' => 'before']);
    $attributes->set(['id' => 'after']);

    expect($attributes->toArray())->toBe(['id' => 'after']);
});

test('an attribute value that is not a scalar is not rendered', function () {
    // an array on a non-data attribute survives normalization but cannot be printed
    expect(glsr(Attributes::class)->div(['title' => [1, 2], 'id' => 'kept'])->toString())
        ->toBe('id="kept"');
});

test('a bare attribute name is rendered as a boolean or empty attribute', function () {
    // ['required'] instead of ['required' => true]: the shorthand a config uses
    expect(glsr(Attributes::class)->input(['required', 'type' => 'text'])->toString())
        ->toContain('required');
    expect(glsr(Attributes::class)->div(['data-foo'])->toString())
        ->toContain('data-foo=""');
});

test('an input attribute that does not apply to its type is dropped', function () {
    // required has no meaning on a hidden input (ATTRIBUTES_INPUT_EXCLUDED)
    $attributes = glsr(Attributes::class)->input(['type' => 'hidden', 'required' => true])->toArray();

    expect($attributes)->not->toHaveKey('required')
        ->and($attributes['type'])->toBe('hidden');
});

/*
 * Builder.
 */

test('the builder refuses to build nothing, closes nothing on void tags, and exposes its seams', function () {
    $builder = glsr(Builder::class);

    expect(call_user_func([$builder, ''], ['text' => 'x']))->toBe(''); // a dynamic call gone wrong
    expect($builder->img(['src' => 'https://example.org/x.png']))->not->toContain('</img>');
    expect($builder->field(['name' => 'x', 'type' => 'text']))->toBeInstanceOf(Field::class);
    expect($builder->buildField($builder->field(['name' => 'x', 'type' => 'text'])))->toContain('<input');

    $builder->set('class', 'set-through-the-seam');
    expect($builder->args()->class)->toBe('set-through-the-seam');
});

test('checkbox options without a field id get no per-option ids', function () {
    $html = glsr(Builder::class)->build('input', [
        'type' => 'checkbox',
        'name' => 'x',
        'options' => ['a' => 'A', 'b' => 'B'],
    ]);

    expect($html)->toContain('type="checkbox"')
        ->and($html)->not->toContain('id="-0"');
});

/*
 * FieldCondition.
 */

test('a condition judges only its own field, and only with an operator it knows', function () {
    $field = new Field(['name' => 'rating', 'type' => 'number', 'value' => 4]);

    // somebody else's condition: valid by definition
    expect((new FieldCondition(['name' => 'other_field', 'operator' => 'eq', 'value' => '1'], $field))->isValid())
        ->toBeTrue();
    // no operator: nothing to test
    expect((new FieldCondition(['name' => 'rating'], $field))->isValid())
        ->toBeTrue();
    // an operator the evaluator has no method for is refused
    $condition = new FieldCondition(['name' => 'rating', 'operator' => 'eq', 'value' => '4'], $field);
    $condition['operator'] = 'bogus';
    expect($condition->isValid())->toBeFalse();
});

/*
 * Field elements.
 */

test('field conditions ride into the checkbox input', function () {
    $field = new \GeminiLabs\SiteReviews\Modules\Html\ReviewField([
        'conditions' => 'all|rating:>:3',
        'name' => 'terms',
        'options' => ['1' => 'I agree'],
        'type' => 'checkbox',
    ]);

    expect($field->buildFieldElement())->toContain('data-conditions');
});

test('a checkbox option given as a one-string list is its label', function () {
    $field = new Field([
        'name' => 'x',
        'options' => ['k' => ['Only label']],
        'type' => 'checkbox',
    ]);

    expect($field->options['k'])->toBe('Only label');
});

test('an empty checkbox value with checked on ticks every option', function () {
    $field = new Field([
        'checked' => true,
        'name' => 'x',
        'options' => ['a' => 'A', 'b' => 'B'],
        'type' => 'checkbox',
        'value' => '',
    ]);

    expect($field->value)->toBe(['a', 'b']);
});

test('an email field keeps the value it was given', function () {
    $field = new Field(['name' => 'email', 'type' => 'email', 'value' => 'a@example.org']);

    expect($field->value)->toBe('a@example.org');
});

test('a textarea outside the settings screen is just a textarea', function () {
    expect(new Field(['name' => 'content', 'type' => 'textarea', 'value' => 'x']))
        ->build()->toContain('<textarea');
});

test('a field style attribute is sanitized on merge', function () {
    $field = new Field(['name' => 'x', 'type' => 'text', 'style' => 'color: red; behavior: url(evil)']);

    expect($field->style)->toBe('color:red;');
});

test('a multi-value field keeps its array value untouched by the base normalizer', function () {
    $field = new Field([
        'multiple' => true,
        'name' => 'x',
        'options' => ['a' => 'A'],
        'type' => 'select',
        'value' => ['a'],
    ]);

    expect($field->value)->toBe(['a']);
});

/*
 * The raw variants and the builders that make them.
 */

test('a raw metabox or widget field builds only its element', function () {
    $metabox = new MetaboxField(['name' => 'x', 'type' => 'hidden', 'is_raw' => true, 'value' => 'v']);
    expect($metabox->buildField())->toStartWith('<input')
        ->and($metabox->buildField())->not->toContain('<div');

    $widget = new WidgetField(['name' => 'x', 'type' => 'hidden', 'is_raw' => true]);
    expect($widget->buildField())->toStartWith('<input');
});

test('each form builder makes its own kind of field', function () {
    expect(glsr(WidgetBuilder::class)->field(['name' => 'x', 'type' => 'text']))
        ->toBeInstanceOf(WidgetField::class);
    expect(glsr(SettingBuilder::class)->field(['name' => 'x', 'type' => 'text']))
        ->toBeInstanceOf(\GeminiLabs\SiteReviews\Modules\Html\SettingField::class);
});

/*
 * Tags.
 */

test('a local review avatar is regenerated when the site asked for that', function () {
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.reviews.avatars_regenerate', 'yes');
    $review = createReview();
    $tag = new \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAvatarTag('avatar');

    $regenerated = $tag->handleFor('review', 'https://example.org/old-avatar.png', $review);

    expect($regenerated)->not->toContain('old-avatar.png');
});

test('a summary tag refuses anything that is not a ratings list', function () {
    $tag = new \GeminiLabs\SiteReviews\Modules\Html\Tags\SummaryRatingTag('rating');

    expect($tag->handleFor('summary', '', 'not-ratings'))->toBe('')
        ->and($tag->handleFor('summary', '', ['a' => 1]))->toBe(''); // keyed, so not a ratings list
});

test('raw review content skips the excerpt machinery entirely', function () {
    // excerpts are ON by default; raw mode (the REST API, the email tags) must
    // hand back the whole text regardless
    $review = createReview(['content' => 'A very reasonable review body.']);
    $tag = new \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewContentTag('content', ['raw' => true]);

    $html = $tag->handleFor('review', $review->content, $review);
    expect($html)->toContain('A very reasonable review body.')
        ->and($html)->not->toContain('glsr-hidden-text'); // no read-more machinery
});

test('the metabox form renders custom array values as json, and any hidden fields first', function () {
    $postId = createPost();
    $review = createReview(['assigned_posts' => [$postId]]);
    add_filter('site-reviews/config/forms/metabox-fields', function ($config) {
        $config['assigned_posts'] = ['label' => 'Assigned', 'type' => 'text'];
        return $config;
    });
    // an addon can register a hidden metabox field; it renders before the visible ones
    add_filter('site-reviews/metabox-form/fields/hidden', function ($fields, $form) {
        $fields['addon_state'] = $form->field('addon_state', ['type' => 'hidden', 'value' => 'x']);
        return $fields;
    }, 10, 2);

    $html = (new \GeminiLabs\SiteReviews\Modules\Html\MetaboxForm($review))->build();

    // the review's own values ride into data-value, arrays as json
    expect($html)->toContain(esc_attr(wp_json_encode([$postId])))
        ->and($html)->toContain('addon_state');
});

test('a condition on a list field with an operator lists cannot use is refused', function () {
    $field = new Field(['multiple' => true, 'name' => 'x', 'options' => ['a' => 'A'], 'type' => 'select', 'value' => ['a']]);
    $condition = new FieldCondition(['name' => 'x', 'operator' => 'gt', 'value' => 'a'], $field);

    $refused = \GeminiLabs\SiteReviews\Tests\protectedMethod(FieldCondition::class, 'isArrayValid')
        ->invoke($condition, ['a']);

    expect($refused)->toBeFalse();
});

test('a settings textarea without autosize is a plain textarea', function () {
    $field = new \GeminiLabs\SiteReviews\Modules\Html\SettingField([
        'name' => 'settings.custom.note',
        'type' => 'textarea',
        'value' => 'x',
    ]);

    expect($field->buildFieldElement())->toContain('<textarea')
        ->and($field->buildFieldElement())->not->toContain('has-reset-button');
});

test('a multi-file field keeps its list value under the base normalizer', function () {
    // file has no element class of its own, so the ABSTRACT normalizeValue runs
    $field = new Field(['multiple' => true, 'name' => 'x', 'type' => 'file', 'value' => ['a.jpg']]);

    expect($field->value)->toBe(['a.jpg']);
});
