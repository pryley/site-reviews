<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

interface EmailContract
{
    public function app(): PluginContract;

    public function compose(array $email, array $data = []): EmailContract;

    public function data(): Arguments;

    public function defaults(): DefaultsAbstract;

    public function read(string $format = ''): string;

    public function send(): bool;

    public function template(): TemplateContract;

    public function buildPlainTextMessage($phpmailer): void;
}
