/** global: GLSR */

import events from '@/compat/events.js';
import { removed, reportTo } from '@/compat/deprecated.js';

// A key before 9.0.0 that 9.0.0 removed => what to use.
const REMOVED = {
    action: 'GLSR.config.request.ajax.action',
    addons: 'GLSR.config.{feature}',
    ajax: 'GLSR.Request',
    ajax_pagination: 'GLSR.config.pagination.fixed',
    ajax_url: 'GLSR.config.request.ajax.url',
    ajaxpagination: 'GLSR.config.pagination.fixed', // before 8.0.1
    ajaxurl: 'GLSR.config.request.ajax.url', // before 8.0.1
    captcha: 'GLSR.config.captcha',
    forms: 'GLSR.Form.instances',
    modal_wrapped_by: 'GLSR.config.modal.wrappedBy',
    nameprefix: 'GLSR.config.nameprefix',
    pagination: 'GLSR.Review.pagination.instances',
    request: 'GLSR.Request',
    rest_nonce: 'GLSR.config.request.nonce',
    rest_url: 'GLSR.config.request.url',
    stars_config: 'GLSR.config.rating',
    starsconfig: 'GLSR.config.rating', // before 8.0.1
    text: 'GLSR.config.text',
    url_parameter: 'GLSR.config.pagination.urlParameter',
    urlparameter: 'GLSR.config.pagination.urlParameter', // before 8.0.1
    Utils: 'GLSR.Util',
    validation_config: 'GLSR.config.validation',
    validation_strings: 'GLSR.config.validation.strings',
    validationconfig: 'GLSR.config.validation', // before 8.0.1
    validationstrings: 'GLSR.config.validation.strings', // before 8.0.1
};

/**
 * Report the 8.x globals that 9.0.0 removed, and trigger the 8.x event names.
 */
export default ({ listen, report }) => {
    reportTo(report)
    events({ listen, report })
    Object.entries(REMOVED).forEach(([key, replacement]) => removed(window.GLSR, key, `GLSR.${key}`, replacement))
}
