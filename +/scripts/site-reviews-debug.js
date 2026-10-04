import debug from '@/debug/index.js';
import * as events from '@/debug/events.js';

window.GLSR?.registry?.define('debug.public', (pieces) => debug(pieces, events))
