<?php
/**
 * @copyright 2023 Nito T.M.
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Nito T.M. (https://github.com/nitotm)
 * @package nitotm/efficient-language-detector
 */

namespace GeminiLabs\Nitotm\Eld;

/**
 * Performance critical
 */
final class LanguageResult
{

    public ?string $language;
    public ?array $scores;
    private ?int $numNgrams;
    private array $avgScore;

    /**
     * @param null|array<string, float> $scores
     * @param array<string, float> $avgScore
     */
    public function __construct(
        ?string $language = null,
        ?array $scores = null,
        ?int $numNgrams = null,
        array $avgScore = []
    ) {
        $this->language = $language;
        $this->scores = $scores;
        $this->numNgrams = $numNgrams;
        $this->avgScore = $avgScore;
    }

    public function __debugInfo()
    {
        return [
            'language' => $this->language,
            'scores' => $this->scores,
            'isReliable()' => $this->isReliable()
        ];
    }

    public function isReliable(): bool
    {
        if (!$this->language || $this->numNgrams < 3 || !$this->scores) {
            return false;
        }
        // A minimum of a 24% from the average score
        if ($this->avgScore[$this->language] * 0.24 > ($this->scores[$this->language] / $this->numNgrams)
            || 0.01 > abs($this->scores[$this->language] - next($this->scores))) {
            return false;
        }
        return true;
    }
}
