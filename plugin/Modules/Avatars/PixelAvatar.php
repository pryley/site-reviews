<?php

/**
 * Adapted from: https://github.com/BinaryMoon/wp-toolbelt/tree/master/modules/avatars.
 *
 * @see: https://www.binarymoon.co.uk/2020/08/pixel-avatars-a-privacy-first-gravatar-replacement/
 */

namespace GeminiLabs\SiteReviews\Modules\Avatars;

use GeminiLabs\Spatie\Color\Hsl;

class PixelAvatar extends AbstractSvgAvatar
{
    public const HEIGHT = 11;
    public const WIDTH = 11;

    public array $data = [];

    public string $hash = '';

    public int $hashIndex = 0;

    public array $pixels = [
        'palette' => [
            'all' => [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 210, 215, 220, 230, 240, 250, 260, 270, 280, 290, 300, 310, 320, 330, 340, 350],
            'skin' => [60, 80, 100, 120, 140, 160, 180, 220, 240, 280, 300, 320, 340],
        ],
        'face' => [
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 1, 0, 0, 2, 0, 0, 1, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 2, 1, 1, 9, 1, 9, 1, 1, 2, 0],
                [0, 2, 1, 1, 1, 1, 1, 1, 1, 2, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
        ],
        'mouth' => [
            [1, 1, 1],
            [1, 1, 1],
            [1, 1, 1],
            [0, 1, 0],
            [0, 1, 1],
            [1, 1, 0],
        ],
        'body' => [
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 1, 1, 2, 1, 1, 1, 2, 1, 1, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 1, 2, 2, 2, 1, 0, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 2, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 2, 1, 2, 1, 2, 1, 0, 0],
                [0, 0, 1, 2, 1, 2, 1, 2, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 2, 9, 2, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 9, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 9, 1, 1, 1, 0, 0],
            ],
        ],
        'hair' => [
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 2, 2, 2, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 2, 0, 0, 0, 2, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 2, 2, 0, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 2, 2, 0, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 1, 2, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 2, 2, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 2, 2, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 2, 0, 0, 0, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 1, 2, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 1, 2, 1, 0, 0, 0],
                [0, 0, 0, 2, 2, 2, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 2, 2, 2, 1, 0, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 2, 2, 2, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 2, 2, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0],
                [0, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 2, 2, 0, 0, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 2, 2, 2, 2, 2, 2, 0, 0],
            ],
            [
                [0, 0, 0, 8, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 8, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 2, 2, 2, 2, 2, 2, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 1, 1, 1, 2, 2, 2, 1, 1, 1, 0],
                [0, 1, 1, 2, 0, 0, 0, 2, 1, 1, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0],
                [0, 0, 1, 2, 2, 1, 2, 2, 1, 0, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 2, 2, 2, 2, 2, 1, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 2, 2, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 2, 0, 0, 0, 2, 1, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
            ],
        ],
    ];

    public function generate(string $from): string
    {
        $this->data = $this->newData();
        $this->hash = $this->filename($from);
        $this->hashIndex = 0;
        $this->addBody();
        $this->addFace();
        $this->addMouth();
        $this->addHair();
        return $this->draw();
    }

    protected function addBody(): void
    {
        $color = $this->getColor(50, 45);
        $pixels = $this->getPixels('body');
        $yOffset = 8;
        for ($y = 0; $y < static::HEIGHT - $yOffset; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $pixelColor = $this->setPixelColour($pixels[$y][$x], $this->data[$y + $yOffset][$x], $color);
                $this->data[$y + $yOffset][$x] = $pixelColor;
            }
        }
    }

    protected function addFace(): void
    {
        $color = $this->getColor(40, 65, 'skin');
        $pixels = $this->getPixels('face');
        $numPixels = count($pixels);
        $yOffset = 3;
        for ($y = 0; $y < $numPixels; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $pixelColor = $this->setPixelColour($pixels[$y][$x], $this->data[$y + $yOffset][$x], $color);
                $this->data[$y + $yOffset][$x] = $pixelColor;
            }
        }
    }

    protected function addHair(): void
    {
        $color = $this->getColor(70, 45);
        $pixels = $this->getPixels('hair');
        $numPixels = count($pixels);
        for ($y = 0; $y < $numPixels; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $pixelColor = $this->setPixelColour($pixels[$y][$x], $this->data[$y][$x], $color);
                $this->data[$y][$x] = $pixelColor;
            }
        }
    }

    protected function addMouth(): void
    {
        $color = $this->getColor(60, 30);
        $pixels = $this->getPixels('mouth');
        if (1 === $pixels[0]) {
            $this->data[6][4] = $color[0];
        }
        if (1 === $pixels[1]) {
            $this->data[6][5] = $color[0];
        }
        if (1 === $pixels[2]) {
            $this->data[6][6] = $color[0];
        }
    }

    protected function draw(): string
    {
        $paths = [];
        $background = $this->getColor(85, 85);
        $commands = [];
        $commands[$background[0]] = sprintf('M0 0h%dv%dH0z', static::WIDTH, static::HEIGHT);
        for ($y = 0; $y < static::HEIGHT; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                if ($fill = $this->data[$y][$x]) {
                    $commands[$fill] = $commands[$fill] ?? '';
                    $commands[$fill] .= sprintf('M%d %dh1v1H%dz', $x, $y, $x);
                }
            }
        }
        foreach ($commands as $fill => $d) {
            $paths[] = sprintf('<path fill="%s" d="%s"/>', $fill, $d);
        }
        return sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d">%s</svg>',
            static::WIDTH,
            static::HEIGHT,
            implode('', $paths)
        );
    }

    protected function filename(string $from): string
    {
        $hash = md5(strtolower(trim($from)));
        $hash = substr($hash, 0, 15);
        return $hash;
    }

    protected function getColor(int $saturation, int $lightness, string $paletteKey = 'all'): array
    {
        $palette = $this->pixels['palette'][$paletteKey];
        $index = $this->indexVal() % count($palette);
        $hue = $palette[$index];
        return [
            (string) (new Hsl($hue, $saturation, $lightness))->toHex(),
            (string) (new Hsl($hue, ($saturation + 10), ($lightness - 20)))->toHex(),
        ];
    }

    protected function getPixels(string $name): array
    {
        $index = $this->indexVal() % count($this->pixels[$name]);
        return $this->pixels[$name][$index];
    }

    /**
     * Get the value of the next character in the hash.
     */
    protected function indexVal(): int
    {
        ++$this->hashIndex;
        $this->hashIndex = $this->hashIndex % strlen($this->hash);
        return ord($this->hash[$this->hashIndex]) + (ord("\0") << 8);
    }

    protected function newData(): array
    {
        $data = [];
        for ($y = 0; $y < static::HEIGHT; ++$y) {
            $data[$y] = [];
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $data[$y][$x] = null;
            }
        }
        return $data;
    }

    protected function setPixelColour(int $pixel, ?string $current, array $palette): string
    {
        $color = (string) $current;
        switch ($pixel) {
            case 1:
                $color = $palette[0];
                break;
            case 2:
                $color = $palette[1];
                break;
            case 8:
                $color = '#fff';
                break;
            case 9:
                $color = '#000';
                break;
        }
        return $color;
    }
}
