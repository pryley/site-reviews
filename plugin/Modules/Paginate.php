<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Defaults\PaginationDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Paginate
{
    public $args;
    public $style;

    public function __construct(array $args = [])
    {
        $base = wp_specialchars_decode(get_pagenum_link());
        $args = wp_parse_args($args, compact('base'));
        $args = glsr(PaginationDefaults::class)->restrict($args);
        $parts = explode('?', $base);
        if ($parts[1] ?? false) {
            $format = explode('?', str_replace('%_%', $args['format'], $args['base']));
            $formatQuery = $format[1] ?? '';
            wp_parse_str($formatQuery, $formatArgs);
            wp_parse_str($parts[1], $urlQueryArgs);
            foreach ($formatArgs as $arg => $value) {
                unset($urlQueryArgs[$arg]);
            }
            $verificationKeys = ['review_id', 'verified']; // remove verification keys from pagination URLs
            if (empty(array_diff_key(array_fill_keys($verificationKeys, ''), $urlQueryArgs))) {
                foreach ($verificationKeys as $key) {
                    unset($urlQueryArgs[$key]);
                }
            }
            $args['add_args'] = array_merge($args['add_args'], (array) urlencode_deep($urlQueryArgs));
        }
        $this->args = glsr()->args($args);
        $this->style = glsr_get_option('general.style');
    }

    public function linkPage(int $page): array
    {
        $format = 1 == $page ? '' : $this->args->format;
        return $this->link('page', [
            'class' => 'page-numbers',
            'data-page' => $page,
            'href' => $this->href($page, $format),
            'text' => trim($this->args->before_page_number.number_format_i18n($page)),
        ]);
    }

    public function linkCurrent(int $page): array
    {
        return $this->link('current', [
            'aria-current' => 'page',
            'class' => 'page-numbers current',
            'data-page' => $page,
            'href' => $this->href($page, $this->args->format),
            'text' => trim($this->args->before_page_number.number_format_i18n($page)),
        ], 'span');
    }

    public function linkDots(): array
    {
        return $this->link('dots', [
            'class' => 'page-numbers dots',
            'text' => __('&hellip;', 'site-reviews'),
        ], 'span');
    }

    public function linkNext(int $page): array
    {
        return $this->link('next', [
            'class' => 'page-numbers next',
            'data-page' => $page,
            'href' => $this->href($page, $this->args->format),
            'text' => $this->args->next_text,
        ]);
    }

    public function linkPrevious(int $page): array
    {
        $format = 2 == $this->args->current ? '' : $this->args->format;
        return $this->link('prev', [
            'class' => 'page-numbers prev',
            'data-page' => $page,
            'href' => $this->href($page, $format),
            'text' => $this->args->prev_text,
        ]);
    }

    public function links(): array
    {
        $args = $this->args;
        $dots = false;
        $minimum = max(0, $args->mid_size * 2);
        $firstPage = min($args->total - $minimum, max(1, $args->current - $args->mid_size));
        $lastPage = min($args->total, max($minimum + 1, $args->current + $args->mid_size));
        $links = [];
        if ($args->total < 2) {
            return $links;
        }
        if (1 < $args->current) {
            $links[] = $this->linkPrevious($this->args->current - 1);
        }
        for ($num = 1; $num <= $args->total; ++$num) {
            if ($num === $args->current) {
                $dots = true;
                $links[] = $this->linkCurrent($num);
            } else {
                $hasFirst = $num <= $args->end_size;
                $hasLast = $num > $args->total - $args->end_size;
                if ($hasFirst || ($num >= $firstPage && $num <= $lastPage) || $hasLast) {
                    $dots = true;
                    $links[] = $this->linkPage($num);
                } elseif ($dots && $args->end_size > 0) {
                    $dots = false;
                    $links[] = $this->linkDots();
                }
            }
        }
        if ($args->current < $args->total) {
            $links[] = $this->linkNext($this->args->current + 1);
        }
        return $links;
    }

    protected function href(int $page, string $format): string
    {
        $href = str_replace('%_%', $format, $this->args->base);
        $href = str_replace('%#%', (string) $page, $href);
        $href = add_query_arg($this->args->add_args, $href);
        return esc_url(apply_filters('paginate_links', $href));
    }

    protected function link(string $type, array $args, string $tag = 'a'): array
    {
        $builder = glsr(Builder::class);
        $link = [
            'link' => $builder->build($tag, $args),
            'type' => $type,
        ];
        return glsr()->filterArray('paginate_link', $link, $args, $builder, $this);
    }
}
