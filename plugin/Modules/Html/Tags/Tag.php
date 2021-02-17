<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Contracts\TagContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class Tag implements TagContract
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $args;

    /**
     * @var string
     */
    public $for;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var mixed
     */
    public $with;

    public function __construct($tag, array $args = [])
    {
        $this->args = glsr()->args($args);
        $this->tag = $tag;
    }

    /**
     * @param string|null $value
     * @param string|null $with
     * @return string|void
     */
    public function handleFor($for, $value = null, $with = null)
    {
        $this->for = $for;
        if ($this->validate($with)) {
            $this->with = $with;
            return $this->handle($value);
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isEnabled($path)
    {
        return Cast::toBool(glsr_get_option($path, true));
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isHidden($path = '')
    {
        return in_array($this->hideOption(), $this->args->hide) || !$this->isEnabled($path);
    }

    /**
     * @param string $value
     * @param string $wrapWith
     * @return string
     */
    public function wrap($value, $wrapWith = null)
    {
        $rawValue = $value;
        $value = glsr()->filterString($this->for.'/value/'.$this->tag, $value, $this);
        if (Helper::isNotEmpty($value)) {
            if (!empty($wrapWith)) {
                $value = glsr(Builder::class)->$wrapWith($value);
            }
            $value = glsr()->filterString($this->for.'/wrapped', $value, $rawValue, $this);
            $value = glsr(Builder::class)->div([
                'class' => sprintf('glsr-%s-%s', $this->for, $this->tag),
                'text' => $value,
            ]);
        }
        return glsr()->filterString($this->for.'/wrap/'.$this->tag, $value, $rawValue, $this);
    }

    /**
     * @param string $value
     * @return string|void
     */
    protected function handle($value = null)
    {
        return $value;
    }

    /**
     * @return string
     */
    protected function hideOption()
    {
        return $this->tag;
    }

    /**
     * @param mixed $with
     * @return bool
     */
    protected function validate($with)
    {
        return true;
    }
}
