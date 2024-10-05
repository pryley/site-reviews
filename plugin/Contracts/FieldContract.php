<?php

namespace GeminiLabs\SiteReviews\Contracts;

/**
 * This class normalizes the DOM markup surrounding the field element.
 * Styled class attributes for the field are merged here.
 *
 * @property string       $after
 * @property bool         $checked
 * @property string       $class
 * @property string       $description
 * @property bool         $disabled
 * @property array        $errors
 * @property string       $group
 * @property string       $id
 * @property bool         $is_custom
 * @property bool         $is_raw
 * @property bool         $is_valid
 * @property string       $label
 * @property array        $labels
 * @property bool         $multiple
 * @property string       $name
 * @property array        $options
 * @property string       $original_name
 * @property string       $original_type
 * @property bool         $required
 * @property bool         $selected
 * @property int          $tabindex
 * @property string       $tag
 * @property string       $text
 * @property string       $type
 * @property string|array $value
 */
interface FieldContract
{
    public function build(): string;

    public function builder(): BuilderContract;

    public function buildField(): string;

    public function buildFieldElement(): string;

    public function buildFieldLabel(): string;

    public function conditions(): array;

    public function exchangeArgs(array $args): void;

    public function exchangeTag(string $tag): void;

    public function fieldElement(): FieldElementContract;

    public function isChoiceField(): bool;

    public function isMultiField(): bool;

    public function isValid(): bool;

    public function location(): string;

    public function namePrefix(): string;

    public function render(): void;

    public function rules(): array;

    public function tag(): string;

    public function toArray(): array;
}
