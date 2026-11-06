import compat from '@/compat/admin.js';
import { shared } from '@/admin/shared.js';
import report from '@/public/core/report.js';

const pieces = shared();
if (pieces) { // the admin script has run
    compat({ ...pieces, report })
}
