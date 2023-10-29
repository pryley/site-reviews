<?php

/**
 * - Added Analysis trait (isDark, isLight)
 * - Added Manipulate trait (darken, desaturate, grayscale, invert, lighten, mix, rotate, saturate)
 * 
 * @package spatie/color v1.5.3
 */

namespace GeminiLabs\Spatie\Color;

interface Color
{
    public static function fromString(string $string);

    public function red();

    public function green();

    public function blue();

    public function toCIELab(): CIELab;

    public function toHex(string $alpha = 'ff'): Hex;

    public function toHsb(): Hsb;

    public function toHsl(): Hsl;

    public function toHsla(float $alpha = 1): Hsla;

    public function toRgb(): Rgb;

    public function toRgba(float $alpha = 1): Rgba;

    public function toXyz(): Xyz;

    public function toCmyk(): Cmyk;

    public function __toString(): string;
}
