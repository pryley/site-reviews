<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ChangeLogLevel extends AbstractCommand
{
    protected int $level = -1; // -1 is an invalid level
    protected array $levels = [
        Console::DEBUG,
        Console::INFO,
        Console::NOTICE,
        Console::WARNING,
    ];

    public function __construct(Request $request)
    {
        if (is_numeric($request->level)) {
            $this->level = $request->cast('level', 'int');
        }
    }

    public function handle(): void
    {
        if (!glsr()->hasPermission('tools', 'console')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to change the console level.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        if (!in_array($this->level, $this->levels)) {
            glsr(Notice::class)->addError(
                _x('Console level could not be changed.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        update_option(Console::LOG_LEVEL_KEY, $this->level);
        glsr(Notice::class)->addSuccess(
            sprintf(_x('Console logging has been set to: Level %s', 'admin-text', 'site-reviews'), $this->level)
        );
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
