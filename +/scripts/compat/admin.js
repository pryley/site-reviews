/** global: GLSR */

import { removed, reportTo } from '@/compat/deprecated.js';

// A key before 9.0.0 that 9.0.0 removed => what to use.
const REMOVED = {
    action: null,
    addons: 'GLSR.config.{feature}',
    addonsurl: 'GLSR.config.urls.addons',
    ajax: 'GLSR.Request',
    autosize: null,
    keys: 'event.key',
    maxrating: 'GLSR.config.rating.max',
    minrating: 'GLSR.config.rating.min',
    nameprefix: 'GLSR.config.nameprefix',
    nonce: null,
    notices: 'GLSR.Notice',
    shortcode: null,
    shortcodes: null,
    stars: 'GLSR.Rating',
    text: 'GLSR.config.text',
    tinymce: null,
    Tippy: 'GLSR.lib.tippy',
    Utils: 'GLSR.Util',
};

export default ({ report }) => {
    reportTo(report)
    Object.entries(REMOVED).forEach(([key, replacement]) => removed(window.GLSR, key, `GLSR.${key}`, replacement))
}
