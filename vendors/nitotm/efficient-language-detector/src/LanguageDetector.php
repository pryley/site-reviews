<?php
/**
 * @copyright 2023 Nito T.M.
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Nito T.M. (https://github.com/nitotm)
 * @package nitotm/efficient-language-detector
 * @version 2.1.0
 */

namespace GeminiLabs\Nitotm\Eld;

use GeminiLabs\Nitotm\Eld\LanguageData;
use GeminiLabs\Nitotm\Eld\LanguageResult;

/**
 * Performance critical
 */
class LanguageDetector extends LanguageData
{
    protected bool $doCleanText = false;
    /** @var array<int, string> $wordStart */
    private array $wordStart;

    public function __construct(?string $ngramsFile = null)
    {
        parent::__construct($ngramsFile);
        $this->wordStart = [' '] + array_fill(1, 70, '');
    }

    /**
     * Returns the language detected for a given UTF-8 string, as an ISO 639-1 code
     *  LanguageResult object( language => 'es', scores => ['es' => 0.5, 'et' => 0.2], isReliable() => true )
     *  LanguageResult object( language => null|string, scores => null|array, isReliable() => bool )
     */
    public function detect(string $text): LanguageResult
    {
        if ($this->doCleanText) {
            // Removes Urls, emails, alphanumerical & numbers
            $text = $this->getCleanText($text);
        }

        $text = $this->normalizeText($text);
        $byteNgrams = $this->getByteNgrams($text);
        $numNgrams = count($byteNgrams);
        $scores = $this->calculateScores($byteNgrams, $numNgrams);
        if ($scores) {
            arsort($scores);

            return new LanguageResult(key($scores), $scores, $numNgrams, $this->avgScore);
        }

        return new LanguageResult();
    }

    /**
     * Removes parts of a string, that may be considered as "noise" for language detection
     */
    public function getCleanText(string $str): string
    {
        // Remove URLS
        $str = preg_replace('@[hw]((ttps?://(www\.)?)|ww\.)([^\s/?.#-]+\.?)+(/\S*)?@i', ' ', $str);
        // Remove emails
        $str = preg_replace('/[a-zA-Z0-9.!$%&’+_`-]+@[A-Za-z0-9.-]+\.[A-Za-z0-9-]{2,64}/u', ' ', $str ?? '');
        // Remove .com domains
        $str = preg_replace('/([A-Za-z0-9-]+\.)+com(\/\S*|[^\pL])/u', ' ', $str ?? '');

        // Remove alphanumerical/number codes
        return preg_replace('/[a-zA-Z]*\d+[a-zA-Z0-9]*+/', ' ', $str ?? '');
    }

    protected function normalizeText(string $text): string
    {
        // Normalize special characters/word separators
        $text = trim(preg_replace('/[^\pL]+(?<![\x27\x60\x{2019}])/u', ' ', $text)); // Consider substr($text, 0, 1000)
        $thisLength = strlen($text);

        if ($thisLength > 350) {
            // Cut to first whitespace after 350 bytes offset, or 380 bytes
            $text = substr(
                $text,
                0,
                min(380, (strpos($text, ' ', 350) ?: 350))
            );
        }

        return mb_strtolower($text, 'UTF-8');
    }

    /**
     * Gets Ngrams from a given string.
     *
     * @return array<string, float>
     */
    protected function getByteNgrams(string $text): array
    {
        /** @var array<string, float> $byteNgrams */
        $byteNgrams = [];
        $countNgrams = 0;
        $start = $this->wordStart;

        foreach ($this->tokenizer($text) as $word) {
            $len = strlen($word);
            if ($len > 70) {
                $len = 70;
            }

            for ($j = 0; ($j + 4) < $len; $j += 3, ++$tmp, ++$countNgrams) {
                $tmp = &$byteNgrams[$start[$j] . substr($word, $j, 4)];
            }
            $tmp = &$byteNgrams[$start[$j] . substr($word, ($len !== 3 ? $len - 4 : 0)) . ' '];
            $tmp++;
            $countNgrams++;
        }

        // Frequency is multiplied by 15000 at the Ngrams database. A reduced number (13200) seems to work better.
        // Linear formulas were tried, decreasing the multiplier for fewer Ngram strings, no meaningful improvement.
        foreach ($byteNgrams as $bytes => $count) {
            $byteNgrams[$bytes] = $count / $countNgrams * 13200;
        }

        return $byteNgrams;
    }

    /**
     * @return array<int, string>
     */
    protected function tokenizer(string $str): array
    {
        return preg_split('/ /', $str, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /**
     * Calculate scores for each language from the given Ngrams
     *
     * @param array<string, float> $byteNgrams
     * @return array<string, float>
     */
    protected function calculateScores(array $byteNgrams, int $numNgrams): array
    {
        /** @var array<int, float> $langScore */
        $langScore = $this->langScore;
        /** @var array<string, float> $scores */
        $scores = [];

        foreach ($byteNgrams as $bytes => $currentFrequency) {
            if (isset($this->ngrams[$bytes])) {
                $langCount = count($this->ngrams[$bytes]);
                // Ngram score multiplier, the fewer languages found the more relevancy. Formula can be fine-tuned.
                // TODO consider make a formula that adapts for database language count, on subsets. Testing is needed
                if ($langCount === 1) {
                    $relevancy = 27;  // Handpicked relevance multiplier, trial-error
                } elseif ($langCount < 16) {
                    $relevancy = (16 - $langCount) / 2 + 1;
                } else {
                    $relevancy = 1;
                }
                // Most time-consuming loop, do only the strictly necessary inside
                foreach ($this->ngrams[$bytes] as $lang => $globalFrequency) {
                    $langScore[$lang] += ($currentFrequency > $globalFrequency ?
                            $globalFrequency / $currentFrequency
                            : $currentFrequency / $globalFrequency
                        ) * $relevancy + 2;
                }
            }
        }
        // This divisor will produce a final score between 0 - ~1, score could be >1. Can be improved.
        $resultDivisor = $numNgrams * 3.2;
        // $scoreNormalizer = $this->scoreNormalizer; // local access improves speed

        if ($this->subset) {
            // Filter here, before indexed array used by filterLangSubset, is converted to associative array
            $langScore = $this->filterLangSubset($langScore);
        }

        $langCodes = $this->langCodes; // local access improves speed
        foreach ($langScore as $lang => $score) {
            if ($score) {
                $scores[$langCodes[$lang]] = $score / $resultDivisor; // * $scoreNormalizer[$lang];
            }
        }

        return $scores;
    }

    public function cleanText(bool $bool): void
    {
        $this->doCleanText = $bool; // Already cast in the argument
    }

    public function info(): array
    {
        return [
            'Data type' => $this->dataType . ($this->isSubset ? '-' . count($this->langCodes) : ''),
            'Languages' => $this->langCodes,
            'Dynamic subset' => $this->subset ? $this->isoLanguages($this->subset) : null
        ];
    }
}
