/**
 * The keys that the inline script printed before 8.4.0. Imports nothing: config.js needs it.
 */

// The name of a member before 8.4.0 => its name in the config.
export const KEYS = {
    captcha: {
        captcha_type: 'captchaType',
        token_field: 'tokenField',
    },
    text: {
        close_modal: 'closeModal',
    },
    validation_config: {
        field_error: 'fieldError',
        field_hidden: 'fieldHidden',
        field_message: 'fieldMessage',
        field_required: 'fieldRequired',
        field_valid: 'fieldValid',
        form_error: 'formError',
        form_message: 'formMessage',
        form_message_failed: 'formMessageFailed',
        form_message_success: 'formMessageSuccess',
        input_error: 'inputError',
        input_valid: 'inputValid',
    },
};

// A key before 8.4.0 => its path in the config. validation_config goes before
// validation_strings: flatConfig() writes the strings into the object it made.
export const PATHS = {
    action: 'request.ajax.action',
    ajax_pagination: 'pagination.fixed',
    ajax_url: 'request.ajax.url',
    captcha: 'captcha',
    modal_wrapped_by: 'modal.wrappedBy',
    nameprefix: 'nameprefix',
    rest_nonce: 'request.nonce',
    rest_url: 'request.url',
    stars_config: 'rating',
    text: 'text',
    url_parameter: 'pagination.urlParameter',
    validation_config: 'validation',
    validation_strings: 'validation.strings',
};

export const rename = (value, names) => {
    if (!names || !value || 'object' !== typeof value || Array.isArray(value)) {
        return value;
    }
    return Object.fromEntries(Object.entries(value).map(([key, item]) => [names[key] ?? key, item]));
}

/**
 * Builds the config from the 8.x keys of a page that was cached before 8.4.0.
 */
export const flatConfig = (GLSR) => {
    const config = {};
    Object.entries(PATHS).forEach(([key, path]) => {
        if (!(key in GLSR)) return;
        const parts = path.split('.');
        const last = parts.pop();
        const parent = parts.reduce((value, part) => (value[part] = value[part] || {}), config);
        parent[last] = rename(GLSR[key], KEYS[key]);
    })
    return config;
}
