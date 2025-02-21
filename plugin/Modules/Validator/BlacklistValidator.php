<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;

class BlacklistValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        $target = implode("\n", array_filter([
            $this->request->name,
            $this->request->content,
            $this->request->email,
            $this->request->ip_address,
            $this->request->title,
        ]));
        $isValid = $this->validateBlacklist($target);
        return glsr()->filterBool('validate/blacklist', $isValid, $target, $this->request);
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            if ('reject' !== glsr_get_option('forms.blacklist.action')) {
                glsr()->sessionSet('form_blacklisted', true);
                return;
            }
            $this->fail(
                __('Your review cannot be submitted at this time.', 'site-reviews'),
                'Blacklisted submission detected.'
            );
        }
    }

    protected function blacklist(): string
    {
        return 'comments' === glsr_get_option('forms.blacklist.integration')
            ? trim(glsr(OptionManager::class)->wp('disallowed_keys'))
            : trim(glsr_get_option('forms.blacklist.entries'));
    }

    protected function validateBlacklist(string $target): bool
    {
        if (empty($blacklist = $this->blacklist())) {
            return true;
        }
        $lines = explode("\n", $blacklist);
        foreach ((array) $lines as $line) {
            $line = trim($line);
            if (empty($line) || 256 < strlen($line)) {
                continue;
            }
            $pattern = sprintf('#%s#iu', preg_quote($line, '#'));
            if (preg_match($pattern, $target)) {
                return false;
            }
        }
        return true;
    }
}
