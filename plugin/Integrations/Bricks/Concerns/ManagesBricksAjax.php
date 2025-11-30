<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Concerns;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

trait ManagesBricksAjax
{
    protected function formatResults(array $items, bool $prefixTitle = true): array
    {
        $results = [];
        foreach ($items as $id => $title) {
            $display = $prefixTitle && is_numeric($id)
                ? "$id: $title"
                : $title;
            $results["id::$id"] = $display;
        }
        return $results;
    }

    protected function missingIds(array $results, array $required): array
    {
        $required = array_map(fn ($id) => Str::removePrefix((string) $id, 'id::'), $required);
        $required = Arr::uniqueInt($required);
        return array_filter($required, fn ($id) => !array_key_exists($id, $results));
    }

    protected function verifyNonce(): void
    {
        $nonce = 'bricks-nonce-builder';
        if (method_exists('Bricks\Ajax', 'verify_request')) { // @phpstan-ignore-line
            \Bricks\Ajax::verify_request($nonce);
            return;
        }
        if (!check_ajax_referer($nonce, 'nonce', false)) {
            wp_send_json_error("verify_nonce: \"$nonce\" is invalid.");
        }
    }
}
