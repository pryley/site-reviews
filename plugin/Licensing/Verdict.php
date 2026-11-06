<?php

namespace GeminiLabs\SiteReviews\Licensing;

/**
 * What the licence server's answer about a key means for this site.
 */
enum Verdict: string
{
    case Disabled = 'disabled';
    case Exhausted = 'exhausted'; // not active here, no activations left
    case Expired = 'expired';
    case Inactive = 'inactive'; // not active here, activations left
    case Invalid = 'invalid';
    case Mismatch = 'mismatch'; // another product's key
    case Other = 'other';
    case Unreachable = 'unreachable';
    case Valid = 'valid';
}
