<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Form extends \ArrayObject
{
    protected array $fields = [];
    protected array $hidden = [];
    protected array $visible = [];

    public function __construct(array $visible, array $hidden = [])
    {
        $this->fields = array_merge($hidden, $visible);
        $this->hidden = $hidden;
        $this->visible = $visible;
        parent::__construct($this->fields, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    public function __toString(): string
    {
        return array_reduce($this->getArrayCopy(), fn ($carry, $field) => $carry.$field, '');
    }

    /**
     * @return \GeminiLabs\SiteReviews\Modules\Html\Field|array|null
     */
    public function hidden(?string $key = null)
    {
        return is_null($key) ? $this->hidden : Arr::get($this->hidden, $key, null);
    }

    /**
     * @param string $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        }
    }

    /**
     * @return \GeminiLabs\SiteReviews\Modules\Html\Field|array|null
     */
    public function visible(?string $key = null)
    {
        return is_null($key)
            ? $this->visible
            : Arr::get($this->visible, $key, null);
    }
}
