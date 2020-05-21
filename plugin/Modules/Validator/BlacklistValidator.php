<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;

class BlacklistValidator
{
    /**
     * @return bool
     */
    public function isValid(array $review)
    {
        $target = implode("\n", array_filter([
            $review['name'],
            $review['content'],
            $review['email'],
            $review['ip_address'],
            $review['title'],
        ]));
        return glsr()->filterBool('validate/blacklist', $this->validate($target), $target, $review);
    }

    protected function getBlacklist()
    {
        $option = glsr(OptionManager::class)->get('settings.submissions.blacklist.integration');
        return $option == 'comments'
            ? trim(glsr(OptionManager::class)->getWP('blacklist_keys'))
            : trim(glsr(OptionManager::class)->get('settings.submissions.blacklist.entries'));
    }

    /**
     * @param string $target
     * @return bool
     */
    protected function validate($target)
    {
        $blacklist = $this->getBlacklist();
        if (empty($blacklist)) {
            return true;
        }
        $lines = explode("\n", $blacklist);
        foreach ((array) $lines as $line) {
            $line = trim($line);
            if (empty($line) || 256 < strlen($line)) {
                continue;
            }
            $pattern = sprintf('#%s#i', preg_quote($line, '#'));
            if (preg_match($pattern, $target)) {
                return false;
            }
        }
        return true;
    }
}
