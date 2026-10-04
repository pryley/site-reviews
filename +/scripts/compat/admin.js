/** global: GLSR */

import { aliasAddons } from '@/compat/public.js';
import { alias, aliasPaths, reportTo } from '@/compat/deprecated.js';

// The name of a member before 8.4.0 => its name in the config.
const KEYS = {
    text: {
        import_error: 'importError',
        rollback_error: 'rollbackError',
        system_info_500: 'systemInfo500',
        system_info_error: 'systemInfoError',
        system_info_failed: 'systemInfoFailed',
    },
};

// A key before 8.4.0 => its path in the config.
const PATHS = {
    action: 'request.ajax.action',
    addonsurl: 'urls.addons',
    filters: 'filters',
    maxrating: 'rating.max',
    minrating: 'rating.min',
    nameprefix: 'nameprefix',
    nonce: 'nonce',
    shortcodes: 'tinymce.required',
    text: 'text',
    tinymce: 'tinymce.plugins',
};

// Review Forms 3.1 calls GLSR.autosize(), which does nothing: a textarea grows with CSS field-sizing.
const autosize = Object.assign(() => {}, { destroy: () => {}, update: () => {} });

const keys = { ALT: 18, DOWN: 40, ENTER: 13, ESC: 27, SPACE: 32, TAB: 9, UP: 38 };

export default ({ Ajax, notices, report, shortcode }) => {
    const { GLSR } = window;
    reportTo(report)
    aliasAddons()
    aliasPaths(PATHS, KEYS)
    alias(GLSR, 'ajax', 'GLSR.ajax', 'GLSR.Request', () => Ajax)
    alias(GLSR, 'autosize', 'GLSR.autosize', null, () => autosize)
    alias(GLSR, 'keys', 'GLSR.keys', 'event.key', () => keys)
    alias(GLSR, 'notices', 'GLSR.notices', 'GLSR.Notice', notices)
    alias(GLSR, 'shortcode', 'GLSR.shortcode', 'GLSR.Tinymce', shortcode)
    alias(GLSR, 'stars', 'GLSR.stars', 'GLSR.Rating', () => GLSR.Rating)
    alias(GLSR, 'Tippy', 'GLSR.Tippy', 'GLSR.lib.tippy', () => GLSR.lib.tippy)
    alias(GLSR, 'Utils', 'GLSR.Utils', 'GLSR.Util', () => GLSR.Util)
}
