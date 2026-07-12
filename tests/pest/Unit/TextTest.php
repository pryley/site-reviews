<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Text;

uses()->group('plugin');

test('initials', function () {
    expect(Text::initials((string) null, ' '))->toEqual('');
    expect(Text::initials('Steve', ' '))->toEqual('S');
    expect(Text::initials('Steve', '.'))->toEqual('S.');
    expect(Text::initials('Steve', '. '))->toEqual('S.');
    expect(Text::initials('Steve Jobs', ' '))->toEqual('S J');
    expect(Text::initials('Steve Jobs', '.'))->toEqual('S.J.');
    expect(Text::initials('Steve Jobs', '. '))->toEqual('S. J.');
    expect(Text::initials('Steve Paul Jobs', ' '))->toEqual('S P J');
    expect(Text::initials('Steve Paul Jobs', '.'))->toEqual('S.P.J.');
    expect(Text::initials('Steve Paul Jobs', '. '))->toEqual('S. P. J.');
});

test('name', function () {
    expect(Text::name('Steve'))->toEqual('Steve');
    expect(Text::name('Steve Jobs'))->toEqual('Steve Jobs');
    expect(Text::name('Steve Paul Jobs'))->toEqual('Steve Paul Jobs');
});

test('name first', function () {
    expect(Text::name('Steve', 'first'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'first'))->toEqual('Steve');
    expect(Text::name('Steve Paul Jobs', 'first'))->toEqual('Steve');
});

test('name first initial', function () {
    expect(Text::name('Steve', 'first_initial'))->toEqual('S');
    expect(Text::name('Steve Jobs', 'first_initial'))->toEqual('S Jobs');
    expect(Text::name('Steve Paul Jobs', 'first_initial'))->toEqual('S Jobs');
});

test('name first initial period', function () {
    expect(Text::name('Steve', 'first_initial', 'period'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'first_initial', 'period'))->toEqual('S.Jobs');
    expect(Text::name('Steve Paul Jobs', 'first_initial', 'period'))->toEqual('S.Jobs');
});

test('name first initial period space', function () {
    expect(Text::name('Steve', 'first_initial', 'period_space'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'first_initial', 'period_space'))->toEqual('S. Jobs');
    expect(Text::name('Steve Paul Jobs', 'first_initial', 'period_space'))->toEqual('S. Jobs');
});

test('name last initial', function () {
    expect(Text::name('Steve', 'last_initial'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'last_initial'))->toEqual('Steve J');
    expect(Text::name('Steve Paul Jobs', 'last_initial'))->toEqual('Steve J');
});

test('name last initial period', function () {
    expect(Text::name('Steve', 'last_initial', 'period'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'last_initial', 'period'))->toEqual('Steve J.');
    expect(Text::name('Steve Paul Jobs', 'last_initial', 'period'))->toEqual('Steve J.');
});

test('name last initial period space', function () {
    expect(Text::name('Steve', 'last_initial', 'period_space'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'last_initial', 'period_space'))->toEqual('Steve J.');
    expect(Text::name('Steve Paul Jobs', 'last_initial', 'period_space'))->toEqual('Steve J.');
});

test('name initials', function () {
    expect(Text::name('Steve', 'initials'))->toEqual('S');
    expect(Text::name('Steve Jobs', 'initials'))->toEqual('S J');
    expect(Text::name('Steve Paul Jobs', 'initials'))->toEqual('S P J');
});

test('name initials period', function () {
    expect(Text::name('Steve', 'initials', 'period'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'initials', 'period'))->toEqual('S.J.');
    expect(Text::name('Steve Paul Jobs', 'initials', 'period'))->toEqual('S.P.J.');
});

test('name initials period space', function () {
    expect(Text::name('Steve', 'initials', 'period_space'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'initials', 'period_space'))->toEqual('S. J.');
    expect(Text::name('Steve Paul Jobs', 'initials', 'period_space'))->toEqual('S. P. J.');
});
