/**
 * Loads the BUILT scripts (assets/scripts): run `make build:assets` first.
 */

const fs = require('fs');
const path = require('path');
const { JSDOM } = require('jsdom');

const ASSETS = path.resolve(__dirname, '../../../assets/scripts');

// EnqueueAdminAssets::config(), with the keys that InlineScript::build() adds.
const adminConfig = () => ({
    compat: true,
    debug: { enabled: false },
    nameprefix: 'site-reviews',
    nonce: { 'toggle-filters': 'abc123' },
    rating: { max: 5, min: 0 },
    request: {
        ajax: { action: 'glsr_admin_action', rest: 'glsr_rest_request', url: 'https://example.org/wp-admin/admin-ajax.php' },
        nonce: 'def456',
        url: 'https://example.org/wp-json/site-reviews/v1/',
    },
    text: {
        cancel: 'Cancel',
        cancelling: 'Cancelling, please wait...',
        importError: 'Your server restricts file uploads to less than 2 MB in size.',
        rollbackError: 'Rollback failed',
        searching: 'Searching...',
        systemInfo500: 'Site Reviews was unable to fetch the System Info because WordPress crashed.',
        systemInfoError: 'Site Reviews was unable to fetch the System Info: %1$s %2$s',
        systemInfoFailed: 'Unable to fetch the System Info.',
    },
    tinymce: {
        plugins: { glsr_shortcode: 'https://example.org/wp-content/plugins/site-reviews/assets/scripts/mce-plugin.js' },
        required: { site_review: { post_id: 'Enter a Review Post ID' } },
    },
    urls: { addons: 'https://example.org/wp-admin/edit.php?post_type=site-review&page=glsr-addons' },
});

// EnqueuePublicAssets::config(), with the keys that InlineScript::build() adds.
const publicConfig = () => ({
    captcha: [],
    compat: true,
    debug: { enabled: false },
    modal: { wrappedBy: ['block'] },
    nameprefix: 'site-reviews',
    pagination: { fixed: ['#wpadminbar', '.site-navigation-fixed'], urlParameter: 'reviews-page' },
    rating: { clearable: false, tooltip: 'Select a Rating' },
    request: {
        ajax: { action: 'glsr_public_action', rest: 'glsr_rest_request', url: 'https://example.org/wp-admin/admin-ajax.php' },
        nonce: false,
        routes: { 'submit-review': 'submissions' },
        url: 'https://example.org/wp-json/site-reviews/v1/',
    },
    styles: Object.fromEntries(['variables', 'keyframes', 'base', 'loaders', 'pagination', 'star-rating', 'review', 'flags', 'summary', 'form', 'field-label', 'field-input', 'field-choice', 'field-toggle', 'field-range', 'field-rating', 'button', 'modal', 'debug'].map(c => [c, `https://example.org/wp-content/plugins/site-reviews/assets/styles/base/${c}.css?ver=9.0.0`])),
    text: { closeModal: 'Close Modal' },
    validation: {
        field: 'glsr-field',
        fieldError: 'glsr-field-is-invalid',
        fieldHidden: 'glsr-hidden',
        fieldMessage: 'glsr-field-error',
        fieldRequired: 'glsr-required',
        fieldValid: 'glsr-field-is-valid',
        form: 'glsr-form',
        formError: 'glsr-form-is-invalid',
        formMessage: 'glsr-form-message',
        formMessageFailed: 'glsr-form-failed',
        formMessageSuccess: 'glsr-form-success',
        inputError: 'glsr-is-invalid',
        inputValid: 'glsr-is-valid',
        strings: { errors: 'Please fix the submission errors.', required: 'This field is required.' },
    },
});

// InlineScript::build(); `added` is the keys that an 8.x localize filter added
const inlineScript = (config, added = {}) => {
    const values = { GLSR_CONFIG: config, GLSR_DEPRECATED: added, GLSR_VERSION: '8.4.0' };
    return read('inline-script.js').replace(/GLSR_(CONFIG|DEPRECATED|VERSION)/g, name => JSON.stringify(values[name]));
}

const read = (file) => fs.readFileSync(path.join(ASSETS, file), 'utf8');

// `before` runs between the inline script and the built script, as a snippet or an optimisation plugin would
const load = (file, inline, { after = [], before = '', html = '', url = 'https://example.org/' } = {}) => {
    const infos = [];
    const warnings = [];
    const { window } = new JSDOM(`<!doctype html><html><body>${html}</body></html>`, {
        runScripts: 'outside-only',
        url,
    });
    window.console.info = (...args) => infos.push(args.join(' '));
    window.console.warn = (...args) => warnings.push(args.join(' '));
    window.eval(inline)
    window.eval(before)
    window.eval(fs.readFileSync(path.join(ASSETS, file), 'utf8'))
    after.forEach(name => window.eval(read(name)))
    return { infos, read, warnings, window };
};

// the admin script calls jQuery(fn) and _.debounce(fn) as it parses; the ready callback is kept, not run
const DEPENDENCIES = 'window.jQuery = (fn) => { window.onReady = fn }; window._ = { debounce: (fn) => fn };';

// jsdom has no dialog.showModal(), which the modal needs to count as supported.
const DIALOG = 'HTMLDialogElement.prototype.showModal = function () {};';

const scripts = (name, modes) => ['compat', 'debug'].filter(mode => modes[mode]).map(mode => `${name}-${mode}.js`);

/**
 * @param {Object} options
 * @param {Object} options.added  The keys that an 8.x localize filter added
 * @param {string} options.before Code that runs before the built script
 * @param {boolean} options.compat Compat mode is on: the config says so and the compat script follows
 * @param {boolean} options.debug Debug mode is on: the config says so and the debug script follows
 * @param {Object} options.config Subjects that replace those of the default config
 * @param {string} options.inline The whole inline script, in place of the default
 */
const loadAdmin = ({ added, before = '', compat = true, config = {}, debug = false, html, inline } = {}) => load(
    'site-reviews-admin.js',
    inline ?? inlineScript({ ...adminConfig(), ...config, compat, debug: { enabled: debug, ...config.debug } }, added),
    { after: scripts('site-reviews-admin', { compat, debug }), before: DEPENDENCIES + before, html }
);

const loadPublic = ({ added, before = '', inline, compat = !inline, config = {}, debug = false, html, url } = {}) => load(
    'site-reviews.js',
    inline ?? inlineScript({ ...publicConfig(), ...config, compat, debug: { enabled: debug, ...config.debug } }, added),
    { after: scripts('site-reviews', { compat, debug }), before: DIALOG + before, html, url }
);

// The rollback script runs inside jQuery(fn); fn runs at once here, with the real jQuery as its argument.
const JQUERY = fs.readFileSync(require.resolve('jquery/dist/jquery.js'), 'utf8')
    + ';window.jQuery = (fn) => fn(window.$);';

// wp.ajax.send() keeps each request in window.sent and sends nothing.
const WP = 'window.sent = []; window.wp = { ajax: { send: (request) => { window.sent.push(request); return { always: () => {} }; } }, i18n: { __: (text) => text } };';

// jsdom does not give a form its fields as properties (form.version), which browsers do.
const FORM_FIELDS = 'document.querySelectorAll("form").forEach(form => [...form.elements].filter(el => el.name).forEach(el => Object.defineProperty(form, el.name, { get: () => form.elements.namedItem(el.name) })));';

const loadRollback = ({ before = '', html } = {}) => load(
    'rollback.js',
    inlineScript(adminConfig()),
    { before: JQUERY + WP + FORM_FIELDS + before, html }
);

// the block editor: both scripts on one page; `order` names which runs first
const loadBoth = ({ debug = false, html = '', order = ['public', 'admin'] } = {}) => {
    const infos = [];
    const warnings = [];
    const errors = [];
    const { window } = new JSDOM(`<!doctype html><html><body>${html}</body></html>`, {
        runScripts: 'outside-only',
        url: 'https://example.org/wp-admin/post.php',
    });
    window.console.error = (...args) => errors.push(args.join(' '));
    window.console.info = (...args) => infos.push(args.join(' '));
    window.console.warn = (...args) => warnings.push(args.join(' '));
    const inline = {
        admin: inlineScript({ ...adminConfig(), compat: true, debug: { enabled: debug } }),
        public: inlineScript({ ...publicConfig(), compat: true, debug: { enabled: debug } }),
    };
    const names = { admin: 'site-reviews-admin', public: 'site-reviews' };
    window.eval(DEPENDENCIES + DIALOG)
    // PHP prints the admin's inline script first
    window.eval(inline.admin)
    window.eval(inline.public)
    order.forEach(bundle => {
        window.eval(read(`${names[bundle]}.js`))
        scripts(names[bundle], { compat: true, debug }).forEach(file => window.eval(read(file)))
    })
    return { errors, infos, warnings, window };
};

module.exports = { DIALOG, adminConfig, inlineScript, loadAdmin, loadBoth, loadPublic, loadRollback, publicConfig, read };
