/**
 * The notices of a screen that loads no admin bundle (the dashboard, Plugins, Updates, the network admin):
 * the Notices module the bundle runs, built on its own, with the request config printed before it.
 */
import { Notices } from '@/admin/shared/notice.js';

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', () => Notices.init(), { once: true })
} else {
    Notices.init()
}
