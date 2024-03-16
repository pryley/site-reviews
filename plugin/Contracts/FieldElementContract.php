<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface FieldElementContract
{
    public function build(array $args = []): string;

    public function defaults(): array;

    public function merge(): void;

    public function required(): array;

    public function tag(): string;
}
