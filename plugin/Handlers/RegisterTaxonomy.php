<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy as Command;

class RegisterTaxonomy
{
    /**
     * @return void
     */
    public function handle(Command $command)
    {
        register_taxonomy(Application::TAXONOMY, Application::POST_TYPE, $command->args);
        register_taxonomy_for_object_type(Application::TAXONOMY, Application::POST_TYPE);
    }
}
