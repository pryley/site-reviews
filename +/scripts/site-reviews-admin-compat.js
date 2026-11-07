import compat from '@/compat/admin.js';
import { shared } from '@/admin/shared.js';
import report from '@/public/core/report.js';

if (shared()) { // the admin script has run
    compat({ report })
}
