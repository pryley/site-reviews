<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Contracts\PluginContract;

interface TemplateContract
{
    public function app(): PluginContract;

    /**
     * @param string $templatePath
     * @return void|string
     */
    public function build($templatePath, array $data = []);

    /**
     * Interpolate context values into template placeholders.
     * @param string $template
     * @param string $templatePath
     * @return string
     */
    public function interpolate($template, $templatePath, array $data = []);

    /**
     * Interpolate context values into template placeholders.
     * @param string $text
     * @return string
     */
    public function interpolateContext($text, array $context = []);

    /**
     * @param string $templatePath
     * @return void|string
     */
    public function render($templatePath, array $data = []);
}
