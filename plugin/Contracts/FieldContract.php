<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Contracts\FieldElementContract;

interface FieldContract
{
    public function build(): string;

    public function builder(): BuilderContract;

    public function exchangeTag(string $tag): void;

    public function fieldElement(): FieldElementContract;

    public function isValid(): bool;

    public function location(): string;

    public function namePrefix(): string;

    public function render(): void;

    public function tag(): string;

    public function toArray(): array;
}
