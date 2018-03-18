<?php

namespace GeminiLabs\SchemaOrg;

interface Type
{
    /**
     * Set a property for this type, if the property is not allowed then throw an error.
     * @param string $method
     * @return void
     * @throws BadMethodCallException
     */
    public function __call( $method, array $arguments );

    /**
     * Create a json-ld script tag for this type, built from the data that `toArray` returns.
     * @return string
     */
    public function __toString();

    /**
     * Return an array representation of the type. If the array contains child types,
     * their context needs to be stripped if it's the same.
     * @return array
     */
    public function toArray();

    /**
     * Create a json-ld script tag for this type, built from the data that `toArray` returns.
     * @return string
     */
    public function toScript();
}
