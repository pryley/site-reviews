<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Defaults\TaxonomyDefaults;

class RegisterTaxonomy extends AbstractCommand
{
    public array $args = [];

    public function __construct(array $input = [])
    {
        $this->args = glsr(TaxonomyDefaults::class)->merge($input);
    }

    public function handle(): void
    {
        register_taxonomy(glsr()->taxonomy, glsr()->post_type, $this->args);
        register_taxonomy_for_object_type(glsr()->taxonomy, glsr()->post_type);
    }
}
