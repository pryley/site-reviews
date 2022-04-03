<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class ColumnFilter
{
    protected $enabledFilters = [];
    protected $name;

    /**
     * @return string
     */
    public function action()
    {
        return sprintf('filter-%s', $this->name());
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'class' => ($this->enabled() ? '' : 'is-hidden hidden'), // @compat with other WP filters
            'id' => $this->id(),
            'name' => $this->name(),
            'options' => $this->options(),
            'placeholder' => $this->placeholder(),
            'value' => $this->value(),
        ];
    }

    /**
     * @return bool
     */
    public function enabled()
    {
        return in_array($this->name(), $this->enabledFilters);
    }

    /**
     * @return string
     */
    public function filter()
    {
        $filter = glsr(Builder::class)->select($this->data());
        $label = $this->filterLabel();
        return $label.$filter;
    }

    /**
     * @return string
     */
    public function filterDynamic()
    {
        $data = wp_parse_args($this->data(), [
            'action' => $this->action(),
            'selected' => $this->selected(),
        ]);
        $filter = glsr()->build('partials/listtable/filter', $data);
        $label = $this->filterLabel();
        return $label.$filter;
    }

    /**
     * @return string
     */
    public function filterLabel()
    {
        return glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => $this->id(),
            'text' => $this->label(),
        ]);
    }

    /**
     * @return string
     */
    public function handle(array $enabledFilters = [])
    {
        $this->enabledFilters = $enabledFilters;
        return $this->render();
    }

    /**
     * @return string
     */
    public function id()
    {
        return sprintf('glsr-filter-by-%s', $this->name());
    }

    /**
     * @return string
     */
    public function label()
    {
        return '';
    }

    /**
     * @return string
     */
    public function name()
    {
        if (empty($this->name)) {
            $name = (new \ReflectionClass($this))->getShortName();
            $name = Str::removePrefix($name, 'ColumnFilter');
            $name = Str::snakeCase($name);
            $this->name = $name;
        }
        return $this->name;
    }

    /**
     * @return array
     */
    public function options()
    {
        return [];
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return '';
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->filter();
    }

    /**
     * @return string
     */
    public function selected()
    {
        return $this->placeholder();
    }

    /**
     * @return string
     */
    public function title()
    {
        return Str::titleCase($this->name());
    }

    /**
     * @return string|int
     */
    public function value()
    {
        return filter_input(INPUT_GET, $this->name());
    }
}
