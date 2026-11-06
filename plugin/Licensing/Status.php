<?php

namespace GeminiLabs\SiteReviews\Licensing;

/**
 * The licence state of the site.
 */
final class Status
{
    public bool $expired = false; // if a licensed addon's key has run out
    public bool $invalid = false; // if a licensed addon's key is refused by the server
    public bool $licensed = false; // if an addon that needs a licence is registered or the premium key is saved
    public bool $missing = false; // if a licensed addon has no key saved
    public bool $premium = false; // if premium is registered or a saved key is premium
    public string $premiumKey = ''; // the first valid key that includes premium
}
