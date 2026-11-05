/** global: GLSR */

import { whenAttached } from '@/public/core/index.js';
import Ajax from '@/public/modules/request/ajax.js';
import events from '@/compat/events.js';
import { KEYS, PATHS } from '@/compat/keys.js';
import { alias, aliasPaths, reportTo } from '@/compat/deprecated.js';

// A key before 8.0.1 => its path in the config. 8.0.1 renamed these without an alias.
const PATHS_80 = {
    ajaxpagination: 'pagination.fixed',
    ajaxurl: 'request.ajax.url',
    starsconfig: 'rating',
    urlparameter: 'pagination.urlParameter',
    validationconfig: 'validation',
    validationstrings: 'validation.strings',
};

const withoutStrings = ({ strings, ...classes }) => classes;

/**
 * Define the modal's 8.x globals once the modal module has attached.
 */
const aliasModal = ({ Instance }) => {
    const { Modal } = window.GLSR;
    alias(Modal, 'modify', 'GLSR.Modal.modify()', 'GLSR.Modal.get()', () => (id, callback) => {
        const modal = Modal.get(id);
        if (modal) {
            callback(modal)
        }
    })
    alias(Instance.prototype, 'dom', 'The dom of a modal', 'its header(), content(), footer(), style() and hideClose()', function () {
        return {
            body: this._body,
            close: this._close,
            content: this._regions ? this._regions.content : null,
            dialog: this._dialog,
            footer: this._regions ? this._regions.footer : null,
            header: this._regions ? this._regions.header : null,
        }
    })
}

/**
 * Define the 8.x globals that the bootstrap can serve before any module has loaded.
 */
export default ({ listen, report }) => {
    reportTo(report)
    events({ listen })
    whenAttached('modal').then(aliasModal)
    alias(window.GLSR, 'ajax', 'GLSR.ajax', 'GLSR.Request', () => Ajax)
    alias(window.GLSR, 'request', 'GLSR.request', 'GLSR.Request', () => ({ ...window.GLSR.Request, data: Ajax.data }))
    aliasPaths(PATHS, KEYS, {
        text: (text) => ({ ...text, closemodal: text.close_modal }), // close_modal was closemodal before 8.0.1
        validation_config: withoutStrings,
    })
    aliasPaths(PATHS_80, { validationconfig: KEYS.validation_config }, {
        validationconfig: withoutStrings,
    })
    alias(window.GLSR, 'Utils', 'GLSR.Utils', 'GLSR.Util', () => window.GLSR.Util)
}
