<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Modules\Html\Field;

/**
 * @property Arguments $args
 * @property string    $tag
 *
 * @method string a(string|array ...$params)
 * @method string button(string|array ...$params)
 * @method string div(string|array ...$params)
 * @method string form(string|array ...$params)
 * @method string h1(string|array ...$params)
 * @method string h2(string|array ...$params)
 * @method string h3(string|array ...$params)
 * @method string h4(string|array ...$params)
 * @method string h5(string|array ...$params)
 * @method string h6(string|array ...$params)
 * @method string i(string|array ...$params)
 * @method string img(string|array ...$params)
 * @method string input(string|array ...$params)
 * @method string label(string|array ...$params)
 * @method string li(string|array ...$params)
 * @method string nav(string|array ...$params)
 * @method string ol(string|array ...$params)
 * @method string optgroup(string|array ...$params)
 * @method string option(string|array ...$params)
 * @method string p(string|array ...$params)
 * @method string pre(string|array ...$params)
 * @method string section(string|array ...$params)
 * @method string select(string|array ...$params)
 * @method string small(string|array ...$params)
 * @method string span(string|array ...$params)
 * @method string textarea(string|array ...$params)
 * @method string ul(string|array ...$params)
 */
interface BuilderContract
{
    public function args(): Arguments;

    /**
     * This uses the existing Builder instance to build an element
     * and overwrites existing tag and arguments
     * with the passed arguments.
     */
    public function build(string $tag, array $args = []): string;

    /**
     * This uses the existing Builder instance to build an element
     * from a Field instance and overwrites existing tag and arguments
     * with the normalized Field arguments.
     */
    public function buildField(FieldContract $field): string;

    public function field(array $args): FieldContract;

    public function set(string $key, $value): void;

    public function tag(): string;
}
