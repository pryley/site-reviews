<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\TemplateContract;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Template implements TemplateContract
{
    public function app(): PluginContract
    {
        return glsr();
    }

    public function build(string $templatePath, array $data = [], bool $minify = false): string
    {
        $data = $this->normalize($data);
        $path = str_replace('templates/', '', $templatePath);
        $template = $this->app()->build($templatePath, $data);
        $template = $this->app()->filterString("build/template/{$path}", $template, $data);
        $template = $this->interpolate($template, $path, $data);
        $template = $this->app()->filterString('rendered/template', $template, $templatePath, $data);
        $template = $this->app()->filterString("rendered/template/{$path}", $template, $data);
        if ($minify) {
            return $this->minify($template);
        }
        return $template;
    }

    public function interpolate(string $template, string $templatePath, array $data = []): string
    {
        $context = $this->normalizeContext(Arr::get($data, 'context', []));
        $context = $this->app()->filterArray("interpolate/{$templatePath}", $context, $template, $data);
        return $this->interpolateContext($template, $context);
    }

    public function interpolateContext(string $text, array $context = []): string
    {
        foreach ($context as $key => $value) {
            $text = strtr(
                $text,
                array_fill_keys(['{'.$key.'}', '{{ '.$key.' }}'], $value)
            );
        }
        return trim($text);
    }

    /**
     * This provides support for :empty in CSS rules
     */
    public function minify(string $html): string
    {
        $html = trim($html);
        $html = preg_replace('/\v+/u', '', $html);
        $html = preg_replace('/>\s+</u', '><', $html);
        return $html;
    }

    public function render(string $templatePath, array $data = []): void
    {
        echo $this->build($templatePath, $data);
    }

    public function renderMultiple(string $templatePath, array $dataArr): void
    {
        foreach ($dataArr as $data) {
            if (is_array($data)) {
                $this->render($templatePath, $data);
            }
        }
    }

    protected function normalize(array $data): array
    {
        $arrayKeys = ['context', 'globals'];
        $data = wp_parse_args($data, array_fill_keys($arrayKeys, []));
        foreach ($arrayKeys as $key) {
            if (!is_array($data[$key])) {
                $data[$key] = [];
            }
        }
        return $data;
    }

    protected function normalizeContext(array $context): array
    {
        $context = Arr::flatten($context);
        $context = array_filter($context, fn ($value) => !is_array($value) && !is_object($value));
        return array_map(fn ($value) => (string) $value, $context);
    }
}
