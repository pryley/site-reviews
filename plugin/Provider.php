<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Compat\Hooks as CompatHooks;
use GeminiLabs\SiteReviews\Contracts\ProviderContract;
use GeminiLabs\SiteReviews\Licensing\Hooks as LicensingHooks;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;
use GeminiLabs\SiteReviews\Overrides\ScheduledActionsTable;

class Provider implements ProviderContract
{
    public function register(Application $app): void
    {
        $app->bind(Application::class, fn () => $app);
        $app->singleton(CompatHooks::class);
        $app->singleton(Hooks::class);
        $app->singleton(LicensingHooks::class);
        $app->singleton(Multilingual::class);
        $app->singleton(Notice::class);
        $app->singleton(Queue::class);
        $app->singleton(Router::class);
        $app->singleton(ScheduledActionsTable::class);
        $app->singleton(Style::class);
        $app->singleton(Translation::class);
        $app->singleton(Translator::class);
    }
}
