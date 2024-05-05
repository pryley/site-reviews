<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface ControllerContract
{
    /**
     * Proxy for WordPress defined filter/action hook callbacks.
     *
     * Since we cannot ensure third-party code will pass the correct data declared
     * by WordPress DocBlocks, this Trait (when used in Controller classes) allows
     * us to use method parameter type hints in WordPress defined filter/action hook
     * callbacks and prevents fatal errors without introducing complexity.
     */
    public function proxy(string $method): callable;
}
