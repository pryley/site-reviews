<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use ET\Builder\Packages\IconLibrary\IconFont\Utils;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations as Declarations;

class StyleDeclarations
{
    public static function buttonAlignment(): callable
    {
        return static function (array $args): string {
            $declarations = new Declarations([
                'important' => false,
                'returnType' => 'string',
            ]);
            $map = [
                'left' => 'start',
                'center' => 'center',
                'right' => 'end',
            ];
            $alignment = $map[$args['attrValue']['alignment'] ?? ''] ?? 'start';
            $declarations->add('display', 'flex');
            $declarations->add('justify-content', $alignment);
            return $declarations->value();
        };
    }

    public static function buttonIcon(): callable
    {
        return static function (array $args): string {
            $declarations = new Declarations([
                'important' => [
                    'content' => true,
                    'line-height' => true,
                    'margin-left' => true,
                    'margin-right' => true,
                    'padding' => true,
                ],
                'returnType' => 'string',
            ]);
            // custom icon disabled
            if ('off' === ($args['attrValue']['enable'] ?? '')) {
                // $declarations->add('margin-right', '-1em');
                return $declarations->value();
            }
            $icon = $args['attrValue']['icon'] ?? [];
            $placement = $icon['placement'] ?? '';

            $declarations->add('line-height', '1');
            // $declarations->add('top', 'auto');
            // $declarations->add('transform', 'none');

            if (empty($icon['settings'])) {
                // has default icon
                if ('left' === $placement) {
                    $declarations->add('margin-left', '-1.3em');
                    $declarations->add('padding', '0 0 0 0.3em');
                } else {
                    $declarations->add('margin-right', '-1.3em');
                    $declarations->add('padding', '0 0.3em 0 0');
                }
            } else {
                // has custom icon
                $unicodeIcon = Utils::escape_font_icon(Utils::process_font_icon($icon['settings']));
                $declarations->add('content', "'{$unicodeIcon}'");
                if ('left' === $placement) {
                    $declarations->add('margin-left', '-1.5em');
                    $declarations->add('padding', '0 0.5em 0 0');
                } else {
                    $declarations->add('margin-right', '-1.5em');
                    $declarations->add('padding', '0 0 0 0.2em');
                }
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
// padding-right: .3em;
