import debug from '@/debug/index.js';
import * as events from '@/debug/events.js';
import { listeners } from '@/public/core/event.js';
import { attach } from '@/public/core/report.js';

if (window.GLSR?.load) { // the public script has run
    debug({ attach, listeners }, events)
}
