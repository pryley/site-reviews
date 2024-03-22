<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class AbstractColumnFilter
{
    protected $enabledFilters = [];
    protected $name;

    public function action(): string
    {
        return sprintf('filter-%s', $this->name());
    }

    public function data(): array
    {
        return [
            'class' => ($this->enabled() ? 'glsr-filter' : 'glsr-filter is-hidden hidden'), // @compat with other WP filters
            'id' => $this->id(),
            'name' => $this->name(),
            'options' => $this->options(),
            'placeholder' => $this->placeholder(),
            'value' => $this->value(),
        ];
    }

    public function enabled(): bool
    {
        return in_array($this->name(), $this->enabledFilters);
    }

    public function filter(): string
    {
        $filter = glsr(Builder::class)->select($this->data());
        $label = $this->filterLabel();
        return $label.$filter;
    }

    public function filterDynamic(): string
    {
        $data = wp_parse_args($this->data(), [
            'action' => $this->action(),
            'selected' => $this->selected(),
        ]);
        $filter = glsr()->build('partials/listtable/filter', $data);
        $label = $this->filterLabel();
        return $label.$filter;
    }

    public function filterLabel(): string
    {
        return glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => $this->id(),
            'text' => $this->label(),
        ]);
    }

    public function handle(array $enabledFilters = []): string
    {
        $this->enabledFilters = $enabledFilters;
        return $this->render();
    }

    public function id(): string
    {
        return sprintf('glsr-filter-by-%s', $this->name());
    }

    public function label(): string
    {
        return '';
    }

    public function name(): string
    {
        if (empty($this->name)) {
            $name = (new \ReflectionClass($this))->getShortName();
            $name = Str::removePrefix($name, 'ColumnFilter');
            $name = Str::snakeCase($name);
            $this->name = $name;
        }
        return $this->name;
    }

    public function options(): array
    {
        return [];
    }

    public function placeholder(): string
    {
        return '';
    }

    public function render(): string
    {
        return $this->filter();
    }

    public function selected(): string
    {
        return $this->placeholder();
    }

    public function title(): string
    {
        return Str::titleCase($this->name());
    }

    public function value(): string
    {
        return (string) filter_input(INPUT_GET, $this->name());
    }
}
