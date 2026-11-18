<?php

declare(strict_types=1);

namespace GeminiLabs\SiteReviews\PhpStan;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * The PHPStan rule that makes a REST route open to everyone say why: a route whose
 * permission_callback is __return_true is an error until its line is ignored with the reason.
 *
 * @implements Rule<ArrayItem>
 */
class PublicRouteRule implements Rule
{
    public function getNodeType(): string
    {
        return ArrayItem::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->key instanceof String_ || 'permission_callback' !== $node->key->value) {
            return [];
        }
        if (!$node->value instanceof String_ || '__return_true' !== $node->value->value) {
            return [];
        }
        return [
            RuleErrorBuilder::message('This REST route is open to everyone; name its capability, or ignore this line with the reason it is public.')
                ->identifier('siteReviews.publicRoute')
                ->build(),
        ];
    }
}
