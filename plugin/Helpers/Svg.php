<?php

namespace GeminiLabs\SiteReviews\Helpers;

class Svg
{
    public static function contents(string $path): string
    {
        $filename = static::filePath($path);
        if (empty($filename)) {
            glsr_log()->error("Invalid SVG path: $path");
            return '';
        }
        $contents = (string) file_get_contents($filename);
        $contents = preg_replace('/\s+/', ' ', $contents);
        $contents = trim($contents);
        return $contents;
    }

    public static function encoded(string $path): string
    {
        $contents = static::contents($path);
        if (empty($contents)) {
            return '';
        }
        // $contents = str_replace('"', "'", $contents);
        return 'data:image/svg+xml;base64,'.base64_encode($contents);
    }

    public static function filePath(string $path): string
    {
        $filename = $path;
        if (!file_exists($filename)) {
            $filename = glsr()->path($path);
        }
        if (!file_exists($filename)) {
            // glsr_log()->error("Invalid SVG path: $filename");
            return '';
        }
        $check = wp_check_filetype($filename, [
            'svg' => 'image/svg+xml',
        ]);
        if ('svg' !== $check['ext'] || 'image/svg+xml' !== $check['type']) {
            // glsr_log()->error("Invalid SVG file: $filename");
            return '';
        }
        return $filename;
    }

    public static function get(string $path, array $attributes = []): string
    {
        $contents = static::contents($path);
        if (empty($contents)) {
            return '';
        }
        $processor = new \WP_HTML_Tag_Processor($contents);
        $processor->next_tag(['tag_name' => 'svg']);
        $style = $processor->get_attribute('style');
        $value = empty($style) ? 'pointer-events: none;' : $style.'; pointer-events: none;';
        $processor->set_attribute('style', $value);
        if (!empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                if ('class' === $attribute) {
                    $processor->add_class($value);
                    continue;
                }
                if ('style' === $attribute) {
                    $style = $processor->get_attribute('style');
                    $value = rtrim((string) $style, ';').'; '.$value;
                }
                $processor->set_attribute($attribute, $value);
            }
        }
        return $processor->get_updated_html();
    }
}
