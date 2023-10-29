<?php

namespace GeminiLabs\Spatie\Color;

trait Analysis
{
    public function isDark(): bool
    {
        return !$this->isLight();
    }

    public function isLight(): bool
    {
        $rgb = $this->toRgb();
        return (($rgb->red() * 299 + $rgb->green() * 587 + $rgb->blue() * 114) / 1000 / 255) >= 0.5;
    }

    abstract public function toRgb(): Rgb;
}
