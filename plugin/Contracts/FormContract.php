<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface FormContract
{
    public function build(): string;

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

    /**
     * @return FieldContract[]
     */
    public function visible(): array;
}
