import debug from '@/debug/index.js';
import { shared } from '@/admin/shared.js';
import { listeners } from '@/public/core/event.js';
import { attach } from '@/public/core/report.js';

if (shared()) { // the admin script has run
    debug({ attach, listeners })
}
