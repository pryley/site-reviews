<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Defaults\PostTypeColumnDefaults;
use GeminiLabs\SiteReviews\Defaults\PostTypeDefaults;
use GeminiLabs\SiteReviews\Defaults\PostTypeLabelDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class RegisterPostType implements Contract
{
    public $args;
    public $columns;

    public function __construct()
    {
        $this->args = glsr(PostTypeDefaults::class)->merge([
            'labels' => glsr(PostTypeLabelDefaults::class)->defaults(),
        ]);
        $this->columns = glsr(PostTypeColumnDefaults::class)->merge([]);
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!in_array(glsr()->post_type, get_post_types(['_builtin' => true]))) {
            register_post_type(glsr()->post_type, $this->args);
            $this->setColumns();
        }
    }

    /**
     * @return void
     */
    protected function setColumns()
    {
        if (array_key_exists('category', $this->columns)) {
            $keys = array_keys($this->columns);
            $keys[array_search('category', $keys)] = 'taxonomy-'.Application::TAXONOMY;
            $this->columns = array_combine($keys, $this->columns);
        }
        if (array_key_exists('pinned', $this->columns)) {
            $this->columns['pinned'] = glsr(Builder::class)->span('<span>'.$this->columns['pinned'].'</span>',
                ['class' => 'pinned-icon']
            );
        }
        if (count(glsr()->reviewTypes) < 2) {
            unset($this->columns['review_type']);
        }
        $columns = wp_parse_args(glsr()->retrieve('columns', []), [
            glsr()->post_type => $this->columns,
        ]);
        glsr()->store('columns', $columns);
    }
}
