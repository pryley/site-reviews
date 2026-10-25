<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\TagContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class Tag implements TagContract
{
    public Arguments $args;
    public string $for = '';
    public string $tag;
    /** @var mixed */
    public $value;
    /** @var mixed */
    public $with;
    /** @var array<string,string> the attributes of the value's element beside its class */
    protected array $valueAttributes = [];

    public function __construct($tag, array $args = [])
    {
        $this->args = glsr()->args($args);
        $this->tag = $tag;
    }

    /**
     * @param mixed $value
     * @param mixed $with
     */
    public function handleFor(string $for, $value = null, $with = null): string
    {
        $this->for = $for;
        if (!$this->validate($with)) {
            return '';
        }
        $this->value = trim(Cast::toString($value, false));
        $this->with = $with;
        return $this->handle();
    }

    public function isEnabled(string $path): bool
    {
        if ($this->isRaw() || glsr()->retrieveAs('bool', 'api', false)) {
            return true;
        }
        return glsr_get_option($path, true, 'bool');
    }

    public function isHidden(string $path = ''): bool
    {
        $isHidden = in_array($this->hideOption(), $this->args->cast('hide', 'array'));
        return ($isHidden && !$this->isRaw()) || !$this->isEnabled($path);
    }

    public function isRaw(): bool
    {
        return Cast::toBool($this->args->raw);
    }

    public function wrap(string $value, ?string $wrapWith = null): string
    {
        $content = $this->content($value, $wrapWith);
        if ('' === $content) {
            return '';
        }
        $wrapped = glsr()->filterString("{$this->for}/wrapped", $content, $value, $this); // the content that is wrapped
        if (!$this->isRaw()) {
            $wrapped = glsr(Builder::class)->div([
                'class' => sprintf('glsr-%s-%s', $this->for, $this->tag),
                'text' => $wrapped,
            ]);
        }
        return glsr()->filterString("{$this->for}/wrap/{$this->tag}", $wrapped, $value, $this);
    }

    /**
     * Return the tag's unwrapped content
     */
    protected function content(string $value, ?string $wrapWith): string
    {
        $notice = $this->isRaw() ? '' : $this->slot('notice', 'div');
        $value = $this->slot('value', $wrapWith, $value, $this->valueAttributes);
        if ('' === $value) {
            return $notice; // value is empty but a notice need showing
        }
        $label = $this->slot('label', 'span', $this->label());
        return $notice.$label.$value;
    }

    protected function handle(): string
    {
        return $this->value();
    }

    protected function hideOption(): string
    {
        return $this->tag;
    }

    protected function label(): string
    {
        return '';
    }

    /**
     * Fill the notice, label, or value slot of the tag.
     */
    protected function slot(string $slot, ?string $htmlTag, string $default = '', array $attributes = []): string
    {
        $text = glsr()->filterString("{$this->for}/{$slot}", $default, $this);
        $text = glsr()->filterString("{$this->for}/{$slot}/{$this->tag}", $text, $this);
        if (Helper::isEmpty($text)) {
            return '';
        }
        if (empty($htmlTag)) {
            return $text; // i.e. rating and avatar tags provide their own markup
        }
        return glsr(Builder::class)->$htmlTag($attributes + [
            'class' => "glsr-tag-{$slot}",
            'text' => $text,
        ]);
    }

    /**
     * @param mixed $with
     */
    protected function validate($with): bool
    {
        return true;
    }

    protected function value(): string
    {
        return $this->value;
    }
}
