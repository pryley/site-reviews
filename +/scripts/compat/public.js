/** global: FormData, GLSR */

import Ajax from '@/public/ajax.js';
import events from '@/compat/events.js';
import { KEYS, PATHS } from '@/compat/keys.js';
import deprecated, { alias, aliasPaths, reportTo } from '@/compat/deprecated.js';

// A key before 8.0.1 => its path in the config. 8.0.1 renamed these without an alias.
const PATHS_80 = {
    ajaxpagination: 'pagination.fixed',
    ajaxurl: 'request.ajax.url',
    starsconfig: 'rating',
    urlparameter: 'pagination.urlParameter',
    validationconfig: 'validation',
    validationstrings: 'validation.strings',
};

// A feature's config subject => its module. Its 8.x key in GLSR.addons is site-reviews-{subject}.
const FEATURES = {
    actions: 'Actions',
    alerts: 'Alerts',
    authors: 'Authors',
    emails: 'Emails',
    filters: 'Filters',
    forms: 'Forms',
    images: 'Images',
    themes: 'Themes',
};

/**
 * Each id keeps one object: the 8.x addons assign to GLSR.addons[id].
 */
export const aliasAddons = () => {
    // read as PHP printed it: when both scripts share a page, the key is already a getter
    const addons = Object.getOwnPropertyDescriptor(window.GLSR, 'addons')?.value || {};
    alias(window.GLSR, 'addons', 'GLSR.addons', 'GLSR.config.{feature} and the module of the feature', () => {
        Object.entries(FEATURES).forEach(([subject, module]) => {
            const values = window.GLSR.config[subject];
            if (values || window.GLSR[module]) {
                const id = `site-reviews-${subject}`;
                addons[id] = Object.assign(addons[id] || {}, values, window.GLSR[module]);
            }
        })
        return addons;
    })
}

// The form of an 8.x addon: only the admin-ajax Router knows its action.
const postToRouter = async (formData, action) => {
    const { ajax } = window.GLSR.config.request;
    deprecated(`The form action "${action}" over admin-ajax`, 'a REST route, named for the action with the site-reviews/rest-api/routes filter')
    const body = new FormData();
    formData.forEach((value, key) => body.append(key, value))
    body.append('action', ajax.action)
    body.append('_ajax_request', true)
    try {
        const response = await fetch(ajax.url, { body, headers: { 'X-Requested-With': 'XMLHttpRequest' }, method: 'POST' });
        const json = await response.json();
        return { data: json.data, status: response.status, success: true === json.success };
    } catch (e) {
        return { data: { message: e.message }, status: 0, success: false };
    }
}

const withoutStrings = ({ strings, ...classes }) => classes;

export default ({ FormInstance, ModalInstance, report, retainForms, submitUnrouted, ...pieces }) => {
    const { Form, Modal, Request, Review } = window.GLSR;
    reportTo(report)
    events(pieces)
    aliasAddons()
    submitUnrouted(postToRouter)
    alias(window.GLSR, 'forms', 'GLSR.forms', 'GLSR.Form.instances', () => Form.instances, (forms) => {
        retainForms(forms) // an 8.x addon assigns the forms that are still on the page
    })
    alias(window.GLSR, 'pagination', 'GLSR.pagination', 'GLSR.Review.pagination.instances', () => Review.pagination.instances)
    alias(FormInstance.prototype, 'form', 'The form of a form instance', 'its el', function () {
        return this.el;
    })
    alias(window.GLSR, 'ajax', 'GLSR.ajax', 'GLSR.Request', () => Ajax)
    alias(window.GLSR, 'request', 'GLSR.request', 'GLSR.Request', () => ({ ...Request, data: Ajax.data }))
    aliasPaths(PATHS, KEYS, {
        text: (text) => ({ ...text, closemodal: text.close_modal }), // close_modal was closemodal before 8.0.1
        validation_config: withoutStrings,
    })
    aliasPaths(PATHS_80, { validationconfig: KEYS.validation_config }, {
        validationconfig: withoutStrings,
    })
    alias(window.GLSR, 'Utils', 'GLSR.Utils', 'GLSR.Util', () => window.GLSR.Util)
    alias(Modal, 'modify', 'GLSR.Modal.modify()', 'GLSR.Modal.get()', () => (id, callback) => {
        const modal = Modal.get(id);
        if (modal) {
            callback(modal)
        }
    })
    alias(ModalInstance.prototype, 'dom', 'The dom of a modal', 'its header(), content(), footer(), style() and hideClose()', function () {
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
