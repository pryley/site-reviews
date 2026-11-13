<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class SignatureValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        $isValid = true;
        $signedValues = $this->request->signedValues(['form_id' => '']);
        foreach ($signedValues as $key => $signedValue) {
            if ($this->hasValueChanged($signedValue, $this->request->get($key))) {
                $isValid = false;
                glsr_log()->debug($signedValues);
                break;
            }
        }
        return glsr()->filterBool('validate/signature', $isValid, $signedValues, $this->request);
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            $this->fail(
                __('This review cannot be submitted, please refresh the page and try again.', 'site-reviews'),
                'The form signature could not be verified.'
            );
        }
    }

    /**
     * @param string|array{hidden?: string[], visible?: int} $signedValue
     * @param mixed                                          $requestValue
     */
    protected function hasValueChanged($signedValue, $requestValue): bool
    {
        if (!is_array($signedValue)) {
            return is_array($requestValue) || Cast::toString($signedValue) !== Cast::toString($requestValue);
        }
        if (!Arr::isIndexedAndFlat($requestValue)) {
            return true;
        }
        $requestValue = array_map([Cast::class, 'toString'], $requestValue);
        foreach (Arr::consolidate($signedValue['hidden'] ?? []) as $hidden) {
            $index = array_search(Cast::toString($hidden), $requestValue, true);
            if (false === $index) {
                return true;
            }
            unset($requestValue[$index]);
        }
        return count($requestValue) > Cast::toInt($signedValue['visible'] ?? 0);
    }
}
