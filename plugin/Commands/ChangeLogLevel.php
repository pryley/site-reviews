<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ChangeLogLevel implements Contract
{
    /**
     * @var int
     */
    protected $level;

    /**
     * @var array
     */
    protected $levels = [
        Console::DEBUG,
        Console::INFO,
        Console::NOTICE,
        Console::WARNING,
    ];

    public function __construct(Request $request)
    {
        $this->level = is_numeric($request->level)
            ? Cast::toInt($request->level)
            : -1; // invalid level!
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->hasPermission('tools', 'console')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to change the console level.', 'admin-text', 'site-reviews')
            );
            return false;
        }
        if (!in_array($this->level, $this->levels)) {
            glsr(Notice::class)->addError(
                _x('Console level could not be changed.', 'admin-text', 'site-reviews')
            );
            return false;
        }
        update_option(Console::LOG_LEVEL_KEY, $this->level);
        glsr(Notice::class)->addSuccess(
            sprintf(_x('Console logging has been set to: Level %s', 'admin-text', 'site-reviews'), $this->level)
        );
        return true;
    }
}
