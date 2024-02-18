<?php

namespace GeminiLabs\SiteReviews\Contracts;

/**
 * @property \GeminiLabs\SiteReviews\Arguments $args
 * @property bool $render
 * @property string $tag
 * @property string $type
 * 
 * @method string a(string|array ...$params)
 * @method string button(string|array ...$params)
 * @method string div(string|array ...$params)
 * @method string i(string|array ...$params)
 * @method string img(string|array ...$params)
 * @method string input(string|array ...$params)
 * @method string li(string|array ...$params)
 * @method string label(string|array ...$params)
 * @method string option(string|array ...$params)
 * @method string p(string|array ...$params)
 * @method string select(string|array ...$params)
 * @method string small(string|array ...$params)
 * @method string span(string|array ...$params)
 * @method string textarea(string|array ...$params)
 * @method string ul(string|array ...$params)
 */
interface BuilderContract
{
    public function build(string $tag, array $args = []): string;
    public function buildClosingTag(): string;
    public function buildCustom(string $tag): string;
    public function buildDefaultElement(string $text = ''): string;
    public function buildElement(): string;
    public function buildFormElement(): string;
    public function buildOpeningTag(): string;
    public function raw(array $field): string;
    public function setArgs(array $args = [], string $type = ''): void;
    public function setRender(bool $bool): void;
    public function setTag(string $tag): void;
}
