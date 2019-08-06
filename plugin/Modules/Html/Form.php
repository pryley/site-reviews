<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

class Form
{
    /**
     * @param string $id
     * @return string
     */
    public function buildFields($id)
    {
        return array_reduce($this->getFields($id), function ($carry, $field) {
            return $carry.$field;
        });
    }

    /**
     * @param string $id
     * @return array
     */
    public function getFields($id)
    {
        $fields = [];
        foreach (glsr()->config('forms/'.$id) as $name => $field) {
            $fields[] = new Field(wp_parse_args($field, ['name' => $name]));
        }
        return $fields;
    }

    /**
     * @param string $id
     * @return void
     */
    public function renderFields($id)
    {
        echo $this->buildFields($id);
    }
}
