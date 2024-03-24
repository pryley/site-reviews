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
        $rawValue = $value;
        $value = glsr()->filterString("{$this->for}/value/{$this->tag}", $value, $this);
        if (Helper::isNotEmpty($value)) {
            if (!empty($wrapWith)) {
                $value = $this->wrapValue($wrapWith, $value);
            }
            $value = glsr()->filterString("{$this->for}/wrapped", $value, $rawValue, $this);
            if (!$this->isRaw()) {
                $value = glsr(Builder::class)->div([
                    'class' => sprintf('glsr-%s-%s', $this->for, $this->tag),
                    'text' => $value,
                ]);
            }
        }
        return glsr()->filterString("{$this->for}/wrap/{$this->tag}", $value, $rawValue, $this);
    }

    protected function handle(): string
    {
        return $this->value();
    }

    protected function hideOption(): string
    {
        return $this->tag;
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

    protected function wrapValue(string $tag, string $value): string
    {
        return glsr(Builder::class)->$tag([
            'class' => 'glsr-tag-value',
            'text' => $value,
        ]);
    }
}
