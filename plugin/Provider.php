<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\ProviderContract;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

class Provider implements ProviderContract
{
    /**
     * @return void
     */
    public function register(Application $app)
    {
        $app->bind(Application::class, function () use ($app) {
            return $app;
        });
        $app->singleton(Hooks::class);
        $app->singleton(Style::class);
        $app->singleton(Translator::class);
        $app->singleton(Translation::class);
        $app->singleton(MainController::class); // this goes last
    }
}
