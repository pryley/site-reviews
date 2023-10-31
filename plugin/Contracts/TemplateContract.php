<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Contracts\PluginContract;

interface TemplateContract
{
    public function app(): PluginContract;

    public function build(string $templatePath, array $data = []): string;

    /**
     * Interpolate context values into template placeholders.
     */
    public function interpolate(string $template, string $templatePath, array $data = []): string;

    /**
     * Interpolate context values into template placeholders.
     */
    public function interpolateContext(string $text, array $context = []): string;

    public function render(string $templatePath, array $data = []): void;
}
