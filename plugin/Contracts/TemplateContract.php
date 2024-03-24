<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface TemplateContract
{
    public function app(): PluginContract;

    public function build(string $templatePath, array $data = []): string;

    public function interpolate(string $template, string $templatePath, array $data = []): string;

    public function interpolateContext(string $text, array $context = []): string;

    public function render(string $templatePath, array $data = []): void;
}
