<?php
/**
 * @copyright 2023 Nito T.M.
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Nito T.M. (https://github.com/nitotm)
 * @package nitotm/efficient-language-detector
 */

// Average score of each language in a correct detection, done with an extended version of big-test benchmark.
return [
    'de' => 0.0275,
    'en' => 0.0378,
    'es' => 0.0252,
    'fr' => 0.0253,
    'it' => 0.0251,
    'nl' => 0.0342,
];

/* Deprecated for now.
 Some languages score higher with the same amount of text, this multiplier evens it out for multi-language strings
 $scoreNormalizer = [0.7, 1, 1, 1, 1, 0.6, 0.98, 1, 1, 1, 0.9, 1, 1, 1, 1, 1, 1, 1, 0.6, 1, 0.7, 1, 1, 0.9, 1, 1, 0.8,
  0.6, 0.6, 1, 1, 0.5, 1, 1, 0.6, 0.7, 1, 0.95, 1, 0.6, 0.6, 1, 1, 1, 1, 1, 1, 0.9, 1, 1, 0.6, 0.6, 0.7, 0.9, 1, 1, 1,
  0.8, 1, 1.7];
*/
