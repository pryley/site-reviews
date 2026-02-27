<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations as Declarations;

class StyleDeclarations
{
    public static function buttonAlignment(): callable
    {
        return static function (array $args): string {
            $map = [
                'left' => 'start',
                'center' => 'center',
                'right' => 'right',
            ];
            $alignment = $map[$args['attrValue']['alignment'] ?? ''] ?? '';
            $declarations = new Declarations([
                'important' => false,
                'returnType' => 'string',
            ]);
            if ($alignment) {
                $declarations->add('display', 'flex');
                $declarations->add('justify-content', $alignment);
            }
            return $declarations->value();
        };
    }

    public static function color(array $cssVariables): callable
    {
        return static function (array $args) use ($cssVariables): string {
            $color = $args['attrValue']['color'] ?? null;
            $declarations = new Declarations([
                'important' => false,
                'returnType' => 'string',
            ]);
            if ($color) {
                foreach ($cssVariables as $prop) {
                    $declarations->add($prop, $color);
                }
            }
            return $declarations->value();
        };
    }

    public static function orientation(): callable
    {
        return static function (array $args): string {
            $orientation = $args['attrValue']['orientation'] ?? null;
            $declarations = new Declarations([
                'important' => true,
                'returnType' => 'string',
            ]);
            if ($orientation) {
                $declarations->add('display', 'flex');
                $declarations->add('justify-content', $orientation);
            }
            return $declarations->value();
        };
    }

}
