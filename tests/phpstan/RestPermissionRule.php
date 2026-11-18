<?php

declare(strict_types=1);

namespace GeminiLabs\SiteReviews\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * The PHPStan rule that keeps hasPermission() out of the REST controllers: the classes in
 * plugin/Controllers/Api/ and any class named Rest…Controller.
 *
 * @implements Rule<MethodCall>
 */
class RestPermissionRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier || 'hasPermission' !== $node->name->toString()) {
            return [];
        }
        $file = str_replace('\\', '/', $scope->getFile());
        $isRestController = str_contains($file, '/plugin/Controllers/Api/')
            || 1 === preg_match('#/Rest[A-Za-z]*Controller\.php$#', $file); // RestController.php, RestImagesController.php
        if (!$isRestController) {
            return [];
        }
        return [
            RuleErrorBuilder::message('hasPermission() passes whenever is_admin() is false; a REST permission callback checks with glsr()->can().')
                ->identifier('siteReviews.restPermission')
                ->build(),
        ];
    }
}
