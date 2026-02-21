<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Arguments;

/**
 * @phpstan-require-extends \GeminiLabs\SiteReviews\Modules\Html\Form
 */
interface FormContract
{
    public function args(): Arguments;

    public function app(): PluginContract;

    public function build(): string;

    public function config(): array;

    public function field(string $name, array $args): FieldContract;

    /**
     * @return FieldContract[]
     */
    public function fields(): array;

    /**
     * @return FieldContract[]
     */
    public function fieldsFor(string $group): array;

    /**
     * @return FieldContract[]
     */
    public function hidden(): array;

    public function loadSession(array $values): void;

    public function session(): Arguments;

    /**
     * @return FieldContract[]
     */
    public function visible(): array;
}
