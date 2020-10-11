<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Template
{
    /**
     * @param string $templatePath
     * @return void|string
     */
    public function build($templatePath, array $data = [])
    {
        $data = $this->normalize($data);
        $path = str_replace('templates/', '', $templatePath);
        $template = glsr()->build($templatePath, $data);
        $template = glsr()->filterString('build/template/'.$path, $template, $data);
        $template = $this->interpolate($template, $data, $path);
        $template = glsr()->filterString('rendered/template', $template, $templatePath, $data);
        $template = glsr()->filterString('rendered/template/'.$path, $template, $data);
        return $template;
    }

    /**
     * Interpolate context values into template placeholders.
     * @param string $template
     * @param string $templatePath
     * @return string
     */
    public function interpolate($template, array $data = [], $templatePath)
    {
        $context = $this->normalizeContext(Arr::get($data, 'context', []));
        $context = glsr()->filterArray('interpolate/'.$templatePath, $context, $template, $data);
        return $this->interpolateContext($template, $context);
    }

    /**
     * Interpolate context values into template placeholders.
     * @param string $text
     * @return string
     */
    public function interpolateContext($text, array $context = [])
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
     * @param string $templatePath
     * @return void|string
     */
    public function render($templatePath, array $data = [])
    {
        echo $this->build($templatePath, $data);
    }

    /**
     * @return array
     */
    protected function normalize(array $data)
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

    /**
     * @return array
     */
    protected function normalizeContext(array $context)
    {
        $context = array_filter($context, function ($value) {
            return !is_array($value) && !is_object($value);
        });
        return array_map(function ($value) {
            return (string) $value;
        }, $context);
    }
}
