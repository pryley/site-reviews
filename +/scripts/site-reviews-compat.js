import compat from '@/compat/public.js';
import { listen } from '@/public/core/event.js';
import report from '@/public/core/report.js';

if (window.GLSR?.load) { // the public script has run
    compat({ listen, report })
}
