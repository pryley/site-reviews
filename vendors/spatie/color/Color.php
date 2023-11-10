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
    public function __toString(): string;
    public function blue();
    public function darken(int $amount = 10): Color;
    public function desaturate(int $amount = 10): Color;
    public function grayscale(): Color;
    public function green();
    public function invert(): Color;
    public function isDark(): bool;
    public function isLight(): bool;
    public function lighten(int $amount = 10): Color;
    public function mix(string $withColor, float $ratio = 0): Color;
    public function red();
    public function rotate(int $amount = 180): Color;
    public function saturate(int $amount = 10): Color;
    public function toCIELab(): CIELab;
    public function toCmyk(): Cmyk;
    public function toHex(string $alpha = 'ff'): Hex;
    public function toHsb(): Hsb;
    public function toHsl(): Hsl;
    public function toHsla(float $alpha = 1): Hsla;
    public function toRgb(): Rgb;
    public function toRgba(float $alpha = 1): Rgba;
    public function toXyz(): Xyz;
}
