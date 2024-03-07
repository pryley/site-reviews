<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Defaults\PostTypeColumnDefaults;
use GeminiLabs\SiteReviews\Defaults\PostTypeDefaults;
use GeminiLabs\SiteReviews\Defaults\PostTypeLabelDefaults;

class RegisterPostType extends AbstractCommand
{
    public array $args = [];
    public array $columns = [];

    public function __construct(array $input = [])
    {
        $input = wp_parse_args($input, [
            'labels' => glsr(PostTypeLabelDefaults::class)->defaults(),
        ]);
        $this->args = glsr(PostTypeDefaults::class)->merge($input);
        $this->columns = glsr(PostTypeColumnDefaults::class)->defaults();
    }

    public function handle(): void
    {
        register_post_type(glsr()->post_type, $this->args);
        $this->setColumns();
        $this->setDefaultHiddenColumns();
    }

    /**
     * @return void
     */
    protected function setColumns()
    {
        if (array_key_exists('category', $this->columns)) {
            $keys = array_keys($this->columns);
            $keys[array_search('category', $keys)] = 'taxonomy-'.glsr()->taxonomy;
            $this->columns = array_combine($keys, $this->columns);
        }
        if (array_key_exists('is_pinned', $this->columns)) {
            $pinnedValue = $this->columns['is_pinned'];
            $this->columns['is_pinned'] = sprintf('<span class="pinned-icon"><span>%s</span></span>', $pinnedValue);
        }
        if (array_key_exists('is_verified', $this->columns)) {
            $verifiedValue = $this->columns['is_verified'];
            $this->columns['is_verified'] = sprintf('<span class="verified-icon"><span>%s</span></span>', $verifiedValue);
        }
        if (count(glsr()->retrieveAs('array', 'review_types')) < 2) {
            unset($this->columns['type']);
        }
        $columns = wp_parse_args(glsr()->retrieveAs('array', 'columns', []), [
            glsr()->post_type => $this->columns,
        ]);
        glsr()->store('columns', $columns);
    }

    /**
     * @return void
     */
    protected function setDefaultHiddenColumns()
    {
        $columns = wp_parse_args(glsr()->retrieveAs('array', 'columns_hidden', []), [
            glsr()->post_type => [
                'taxonomy-'.glsr()->taxonomy,
                'assigned_users',
                'author_name',
                'author_email',
                'ip_address',
                'response',
            ],
        ]);
        glsr()->store('columns_hidden', $columns);
    }
}
