<?php

namespace GeminiLabs\Spatie\Color;

trait Manipulate
{
    public function darken(int $amount = 10): Color
    {
        return $this->lighten(-$amount);
    }

    public function desaturate(int $amount = 10): Color
    {
        return $this->saturate(-$amount);
    }

    public function grayscale(): Color
    {
        return $this->desaturate(100);
    }

    public function invert(): Color
    {
        $rgba = $this->toRgba();
        $red = 255 - $rgba->red();
        $green = 255 - $rgba->green();
        $blue = 255 - $rgba->blue();
        $color = new Rgba($red, $green, $blue, $rgba->alpha());
        return $this->fromColor($color);
    }

    public function lighten(int $amount = 10): Color
    {
        $hsla = $this->toHsla();
        $lightness = max(0, min(100, $hsla->lightness() + $amount));
        $color = new Hsla($hsla->hue(), $hsla->saturation(), $lightness, $hsla->alpha());
        return $this->fromColor($color);
    }

    public function mix(string $withColor, float $ratio = 0): Color
    {
        $lab1 = $this->toCIELab();
        $lab2 = Factory::fromString($withColor)->toCIELab();
        $ratio = max(0, min(1, $ratio));
        $l = ($lab1->l() * (1 - $ratio) + $lab2->l() * $ratio);
        $a = ($lab1->a() * (1 - $ratio) + $lab2->a() * $ratio);
        $b = ($lab1->b() * (1 - $ratio) + $lab2->b() * $ratio);
        // CIE Lightness values less than 0% must be clamped to 0%.
        // Values greater than 100% are permitted for forwards compatibility with HDR.
        $l = max(0, min(400, $l));
        $color = new CIELab($l, $a, $b);
        return $this->fromColor($color);
    }

    public function rotate(int $amount = 180): Color
    {
        $hsla = $this->toHsla();
        $hue = round($hsla->hue() + $amount);
        $color = new Hsla($hue, $hsla->saturation(), $hsla->lightness(), $hsla->alpha());
        return $this->fromColor($color);
    }

    public function saturate(int $amount = 10): Color
    {
        $hsla = $this->toHsla();
        $saturation = max(0, min(100, $hsla->saturation() + $amount));
        $color = new Hsla($hsla->hue(), $saturation, $hsla->lightness(), $hsla->alpha());
        return $this->fromColor($color);
    }

    abstract public function toCIELab(): CIELab;

    abstract public function toHsla(float $alpha = 1): Hsla;

    abstract public function toRgb(): Rgb;

    protected function fromColor(Color $color): Color
    {
        $method = 'to'.(new \ReflectionClass($this))->getShortName();
        return call_user_func([$color, $method]);
    }
}
