<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;

class ClearConsole extends AbstractCommand
{
    public function handle(): void
    {
        if (!glsr()->hasPermission('tools', 'console')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to clear the console.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        glsr(Console::class)->clear();
        glsr(Notice::class)->addSuccess(_x('Console cleared.', 'admin-text', 'site-reviews'));
    }

    public function response(): array
    {
        $data = [
            'console' => '',
            'notices' => glsr(Notice::class)->get(),
        ];
        if ($this->successful()) {
            $data['console'] = glsr(Console::class)->get();
        }
        return $data;
    }
}
