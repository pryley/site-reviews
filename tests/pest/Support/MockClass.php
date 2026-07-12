<?php

namespace GeminiLabs\SiteReviews\Tests;

/**
 * A stringable object, for the casting tests.
 */
class MockClass
{
    public function __toString()
    {
        return '123';
    }
}
