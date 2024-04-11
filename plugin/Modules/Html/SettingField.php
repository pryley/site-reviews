<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * @property mixed  $default
 * @property array  $depends_on
 * @property bool   $is_hidden
 * @property array  $tags
 * @property string $tooltip
 *
 * @todo fix checked/selected attribute values...
 */
class SettingField extends Field
{
    public function __construct(array $args = [])
    {
        $field = wp_parse_args($args, [
            'default' => '',
            'depends_on' => [],
            'tags' => [],
            'tooltip' => '',
        ]);
        parent::__construct($field);
    }

    public function builder(): BuilderContract
    {
        return glsr(SettingBuilder::class);
    }

    public function buildField(): string
    {
        $data = [
            'context' => [
                'class' => $this->classAttrField(),
                'depends_on' => esc_js($this->offsetGet('data-depends') ?? ''),
                'field' => $this->buildFieldElement(),
                'label' => $this->buildFieldLabel(),
                'legend' => $this->label,
            ],
            'field' => $this,
        ];
        return $this->isChoiceField()
            ? glsr(Template::class)->build('partials/form/table-row-multiple', $data)
            : glsr(Template::class)->build('partials/form/table-row', $data);
    }

    public function buildFieldAfter(): string
    {
        if (empty($this->after)) {
            return '';
        }
        return "&nbsp;{$this->after}";
    }

    public function buildFieldDescription(): string
    {
        if (empty($this->description)) {
            return '';
        }
        return $this->builder()->p([
            'class' => 'description',
            'text' => $this->description,
        ]);
    }

    public function buildFieldElement(): string
    {
        $element = $this->fieldElement()->build([
            'label' => '', // prevent the field label from being built
        ]);
        $after = $this->buildFieldAfter();
        $description = $this->buildFieldDescription();
        return $element.$after.$description;
    }

    public function buildFieldLabel(): string
    {
        return $this->builder()->label([
            'for' => !$this->isChoiceField() ? $this->id : '',
            'text' => $this->label.$this->buildFieldTooltip(),
        ]);
    }

    public function buildFieldTooltip(): string
    {
        if (empty($this->tooltip)) {
            return '';
        }
        return $this->builder()->span([
            'class' => 'glsr-tooltip dashicons-before dashicons-editor-help',
            'data-tippy-allowHTML' => true,
            'data-tippy-content' => $this->tooltip,
            'data-tippy-delay' => [200, null],
            'data-tippy-interactive' => true,
            'data-tippy-offset' => [-10, 10],
            'data-tippy-placement' => 'top-start',
            // 'data-tippy-animation' => 'scale',
            // 'data-tippy-inertia' => true,
            // 'data-tippy-trigger' => 'click',
        ]);
    }

    public function location(): string
    {
        return 'setting';
    }

    public function namePrefix(): string
    {
        return OptionManager::databaseKey();
    }

    protected function classAttrField(): string
    {
        $classes = ['glsr-setting-field'];
        if ($this->is_hidden) {
            $classes[] = 'hidden';
        }
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }
}
