<?php

use GeminiLabs\SiteReviews\Modules\Html\SettingField;
use GeminiLabs\SiteReviews\Modules\Html\Tooltip;

uses()->group('plugin');

/*
 * The tooltips and popovers of the plugin screens.
 *
 * PHP prints the content as an element of the page, so what may be in it is decided here:
 * inline formatting in a tooltip, the same with web links in a popover, lists and paragraphs
 * too unless the popover is inside a paragraph, and nothing else in any of them.
 */

test('a tooltip keeps its inline formatting and loses a link and a list', function () {
    $html = glsr(Tooltip::class)->tooltip('probe-tooltip', 'Change the <u>minimum</u> level, see <a href="https://example.org/">the guide</a>.<ul><li>One</li></ul>');

    expect($html)->toStartWith('<span class="glsr-tooltip" id="probe-tooltip" popover="manual" role="tooltip">')
        ->toContain('<u>minimum</u>')
        ->toContain('the guide') // the text of the link stays
        ->toEndWith('One</span>') // a span holds no list
        ->and($html)->not->toContain('<a');
    expect($html)->not->toContain('<ul');
    expect($html)->not->toContain('<li');
});

test('a tooltip that sits below its element says so', function () {
    expect(glsr(Tooltip::class)->tooltip('probe-tooltip', 'Screen Options', ['placement' => 'bottom']))
        ->toContain('class="glsr-tooltip is-below"');
});

test('a tooltip does not show code', function () {
    expect(glsr(Tooltip::class)->tooltip('probe-tooltip', 'Reset', ['syntax' => 'php']))
        ->toBe('<span class="glsr-tooltip" id="probe-tooltip" popover="manual" role="tooltip">Reset</span>');
});

test('a popover keeps a web link, a list and its formatting', function () {
    $html = glsr(Tooltip::class)->popover('probe-popover', 'Use the <code>form</code> option, see <a href="https://example.org/guide" target="_blank" title="Guide">the guide</a>.<ol><li>One</li></ol>');

    expect($html)->toStartWith('<div class="glsr-tooltip" id="probe-popover" popover="auto">')
        ->toEndWith('<ol><li>One</li></ol></div>')
        ->toContain('<code>form</code>')
        ->toContain('<a href="https://example.org/guide" target="_blank">the guide</a>'); // title is not kept
});

test('a popover keeps no other tag and no address that is not a web address', function () {
    $html = glsr(Tooltip::class)->popover('probe-popover', '<div class="box">A <strong>bold</strong> word<img src="a.png"></div> <a href="mailto:name@example.org">Write</a>');

    expect($html)->toContain('A <strong>bold</strong> word')
        ->and($html)->not->toContain('class="box"');
    expect($html)->not->toContain('<img');
    expect($html)->not->toContain('mailto:');
});

test('a popover inside a paragraph is a span that keeps a link and loses a list', function () {
    $html = glsr(Tooltip::class)->popover('probe-popover', 'Deleted by <a href="https://example.org/user">Jane</a>.<ul><li>One</li></ul>', ['inline' => true]);

    expect($html)->toBe('<span class="glsr-tooltip" id="probe-popover" popover="auto">Deleted by <a href="https://example.org/user">Jane</a>.One</span>');
});

test('a popover that shows code is never a span', function () {
    expect(glsr(Tooltip::class)->popover('probe-code', '[]', ['inline' => true, 'syntax' => 'php']))
        ->toBe('<div class="glsr-tooltip is-code" id="probe-code" popover="auto"><pre><code class="language-php">[]</code></pre></div>');
});

test('a popover takes a class and says where it sits', function () {
    expect(glsr(Tooltip::class)->popover('probe-popover', 'Some help.', ['class' => 'is-wide', 'placement' => 'top-start']))
        ->toStartWith('<div class="glsr-tooltip is-start is-wide" id="probe-popover" popover="auto">');
    expect(glsr(Tooltip::class)->popover('probe-popover', 'Some help.', ['placement' => 'left']))
        ->toStartWith('<div class="glsr-tooltip" id="probe-popover" popover="auto">'); // not a placement
});

test('the help is a button named after its setting, followed by its popover', function () {
    $html = glsr(Tooltip::class)->help('probe-help', '<strong>Style</strong>', 'Some help.', ['class' => 'is-wide']);

    expect($html)->toBe(
        '<button class="dashicons-before dashicons-editor-help glsr-help glsr-tooltip-button" aria-label="More information about Style" popovertarget="probe-help" type="button"></button>'. // the class is the popover's
        '<div class="glsr-tooltip is-wide" id="probe-help" popover="auto">Some help.</div>'
    );
});

test('code is shown as it was written', function () {
    $html = glsr(Tooltip::class)->popover('probe-code', "['a' => '<b>']", ['syntax' => 'php']);

    expect($html)->toStartWith('<div class="glsr-tooltip is-code" id="probe-code" popover="auto"><pre><code class="language-php">')
        ->toContain('=&gt;')
        ->toContain('&lt;b&gt;')
        ->and($html)->not->toContain('<b>');
});

test('a popover in a language the code styles do not know shows text', function () {
    expect(glsr(Tooltip::class)->popover('probe-code', 'A <strong>bold</strong> word', ['syntax' => 'ruby']))
        ->toBe('<div class="glsr-tooltip" id="probe-code" popover="auto">A <strong>bold</strong> word</div>');
});

test('the icon button of a popover names it and points at it', function () {
    expect(glsr(Tooltip::class)->button('probe-popover', 'Log', ['icon' => 'dashicons-format-aside']))
        ->toBe('<button class="dashicons-before dashicons-format-aside glsr-tooltip-button" aria-label="Log" popovertarget="probe-popover" type="button"></button>');
});

test('a button opens its popover on hover and focus when it is told to', function () {
    // the script reads glsr-help
    expect(glsr(Tooltip::class)->button('probe-popover', 'Deleted by', ['hover' => true, 'icon' => 'dashicons-info']))
        ->toBe('<button class="dashicons-before dashicons-info glsr-help glsr-tooltip-button" aria-label="Deleted by" popovertarget="probe-popover" type="button"></button>');
});

test('the help of a setting sits beside its label, not inside it', function () {
    // A button inside the label would be read as part of the setting's own name, and a click
    // on it would also act on the field.
    $html = (new SettingField([
        'label' => 'Style',
        'name' => 'settings.fake.style',
        'tooltip' => 'Read <a href="https://example.org/">the guide</a>.',
        'type' => 'text',
    ]))->build();

    expect($html)->toMatch('/<button[^>]+aria-label="More information about Style" popovertarget="([^"]+)" type="button"><\/button><div class="glsr-tooltip is-start" id="\1" popover="auto">Read <a href="https:\/\/example.org\/">the guide<\/a>\.<\/div><label/s');
    preg_match('/<label[^>]*>(.*?)<\/label>/s', $html, $label);
    expect($label[1])->toBe('Style');
});
