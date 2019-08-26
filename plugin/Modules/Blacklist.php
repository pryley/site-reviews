<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;

class Blacklist
{
    /**
     * @return bool
     */
    public function isBlacklisted(array $review)
    {
        $target = implode("\n", array_filter([
            $review['name'],
            $review['content'],
            $review['email'],
            $review['ip_address'],
            $review['title'],
        ]));
        return (bool) apply_filters('site-reviews/blacklist/is-blacklisted',
            $this->check($target),
            $review,
            $target
        );
    }

    /**
     * @param string $target
     * @return bool
     */
    protected function check($target)
    {
        $blacklist = $this->getBlacklist();
        if (empty($blacklist)) {
            return false;
        }
        $lines = explode("\n", $blacklist);
        foreach ((array) $lines as $line) {
            $line = trim($line);
            if (empty($line) || 256 < strlen($line)) {
                continue;
            }
            $pattern = sprintf('#%s#i', preg_quote($line, '#'));
            if (preg_match($pattern, $target)) {
                return true;
            }
        }
        return false;
    }

    protected function getBlacklist()
    {
        $option = glsr(OptionManager::class)->get('settings.submissions.blacklist.integration');
        return $option == 'comments'
            ? trim(glsr(OptionManager::class)->getWP('blacklist_keys'))
            : trim(glsr(OptionManager::class)->get('settings.submissions.blacklist.entries'));
    }
}
