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
 * The PHPStan rule that keeps a value out of the statement given to sql(). The statement is
 * written in the code, with a placeholder for each name and value passed after it. A part of
 * it may also be a whole number or what a method returns, such as a clause of the query builder.
 *
 * @implements Rule<MethodCall>
 */
class QuerySqlRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier || 'sql' !== $node->name->toString()) {
            return [];
        }
        $args = $node->getArgs();
        if (empty($args)) {
            return [];
        }
        $callee = $scope->getType($node->var);
        if (!in_array('GeminiLabs\SiteReviews\Database\Sql', $callee->getObjectClassNames(), true)
            && !$callee->hasMethod('sqlHandle')->yes()) {
            return [];
        }
        $statement = $args[0]->value;
        if ($scope->getType($statement)->isLiteralString()->yes()) {
            return [];
        }
        if ($statement instanceof \PhpParser\Node\Scalar\InterpolatedString || $statement instanceof \PhpParser\Node\Scalar\Encapsed) {
            $values = [];
            foreach ($statement->parts as $part) {
                if ($part instanceof \PhpParser\Node\InterpolatedStringPart || $part instanceof \PhpParser\Node\Scalar\EncapsedStringPart) {
                    continue;
                }
                if ($part instanceof MethodCall || $part instanceof \PhpParser\Node\Expr\StaticCall) {
                    continue; // what a method returns
                }
                $type = $scope->getType($part);
                if ($type->isLiteralString()->yes() || $type->isInteger()->yes()) {
                    continue; // text written in the code, or a number
                }
                $values[] = $part;
            }
            if (empty($values)) {
                return [];
            }
        }
        return [
            RuleErrorBuilder::message('The statement given to sql() holds a value; write the statement in the code and pass the value after it.')
                ->identifier('siteReviews.querySql')
                ->build(),
        ];
    }
}
