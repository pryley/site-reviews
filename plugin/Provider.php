<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\ProviderContract;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

class Provider implements ProviderContract
{
    /**
     * @return void
     */
    public function register(Application $app)
    {
        $app->bind(Application::class, $app);
        $app->singleton(Actions::class, Actions::class);
        $app->singleton(Filters::class, Filters::class);
        $app->singleton(OptionManager::class, OptionManager::class);
        $app->singleton(Translator::class, Translator::class);
        $app->singleton(Translation::class, Translation::class);
        $app->singleton(MainController::class, MainController::class); // this goes last
    }
}
