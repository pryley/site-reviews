const assert = require('node:assert/strict');
const { test } = require('node:test');
const { PUBLIC_INLINE_83, loadPublic, publicConfig } = require('./harness.js');

const UTIL = ['debounce', 'dom', 'fadeIn', 'fadeOut', 'isEmpty', 'parseJson', 'selectText', 'throttle'];

// Values that cross the jsdom realm are compared as JSON.
const plain = (value) => JSON.parse(JSON.stringify(value));

test('a listener added before the script runs is kept, in order, with its context', () => {
    const { window } = loadPublic({
        before: `
            window.calls = [];
            window.stub = GLSR.Event;
            GLSR.Event.on('probe', function (value) { calls.push(['first', value, this.tag]) }, { tag: 'context' });
            GLSR.Event.on('probe', (value) => calls.push(['second', value]));
        `,
    });
    window.GLSR.Event.trigger('probe', 7)
    assert.deepEqual(plain(window.calls), [['first', 7, 'context'], ['second', 7]])
    // a script that kept a reference to the stub still holds the real module
    assert.equal(window.stub, window.GLSR.Event)
});

test('Event has four members and its listeners are private', () => {
    const { window } = loadPublic();
    assert.deepEqual(Object.keys(window.GLSR.Event).sort(), ['off', 'on', 'once', 'trigger'])
});

test('Event.listeners exists only in debug mode and returns frozen snapshots', () => {
    assert.equal('listeners' in loadPublic().window.GLSR.Event, false)
    const { window } = loadPublic({ debug: true });
    const listener = () => {};
    window.GLSR.Event.on('probe', listener, 'context')
    window.GLSR.Event.once('probe', listener)
    assert.equal(window.GLSR.Event.listeners().probe, 2)
    assert.equal(Object.isFrozen(window.GLSR.Event.listeners()), true)
    const [first, second] = window.GLSR.Event.listeners('probe');
    assert.equal(first.fn, listener)
    assert.equal(first.context, 'context')
    assert.equal(second.fn, listener) // the function given to once(), not its wrapper
    assert.equal(window.GLSR.Event.listeners('nothing').length, 0)
});

test('GLSR_init is a bare global function that binds the plugin listeners once', () => {
    const { window } = loadPublic();
    let loaded = 0;
    window.GLSR.Event.on('site-reviews/loaded', () => loaded++)
    assert.equal(typeof window.GLSR_init, 'function')
    window.GLSR_init()
    window.GLSR_init()
    assert.equal(loaded, 2)
});

test('the config is the one PHP printed, deep-frozen', () => {
    const { window } = loadPublic();
    const { config } = window.GLSR;
    assert.deepEqual(plain(config), publicConfig())
    assert.equal(Object.isFrozen(config), true)
    assert.equal(Object.isFrozen(config.request.ajax), true)
    assert.equal(Object.isFrozen(config.pagination.fixed), true)
    assert.equal(Object.isFrozen(config.validation.strings), true)
    assert.equal(window.GLSR.version, '8.4.0')
});

test('the built inline script names each value that PHP supplies once, so that none is printed twice', () => {
    const script = loadPublic().read('inline-script.js');
    for (const name of ['GLSR_CONFIG', 'GLSR_DEPRECATED', 'GLSR_VERSION']) {
        assert.equal(script.split(name).length - 1, 1, name)
    }
});

test('Util has the eight helpers', () => {
    const { window } = loadPublic();
    assert.deepEqual(Object.keys(window.GLSR.Util).sort(), UTIL)
});

test('Modal has four members', () => {
    const { window } = loadPublic();
    assert.deepEqual(Object.keys(window.GLSR.Modal).sort(), ['close', 'get', 'init', 'open'])
});

test('GLSR.Utils is a hidden getter onto GLSR.Util', () => {
    const { window } = loadPublic();
    const descriptor = Object.getOwnPropertyDescriptor(window.GLSR, 'Utils');
    assert.equal(window.GLSR.Utils, window.GLSR.Util)
    assert.equal(descriptor.configurable, true) // the admin script defines the key too when both share a page
    assert.equal(descriptor.enumerable, false)
});

test('GLSR.Modal.modify calls back with the modal that GLSR.Modal.get returns', () => {
    const { window } = loadPublic();
    let received = null;
    window.GLSR.Modal.init('glsr-modal-probe')
    window.GLSR.Modal.modify('glsr-modal-probe', (modal) => (received = modal))
    assert.notEqual(received, null)
    assert.equal(received, window.GLSR.Modal.get('glsr-modal-probe'))
    assert.deepEqual(Object.keys(received.dom).sort(), ['body', 'close', 'content', 'dialog', 'footer', 'header'])
});

test('each key that 8.3 printed is a hidden getter onto the config', () => {
    const { window } = loadPublic({
        config: {
            captcha: { captchaType: 'frictionless', class: 'procaptcha', tokenField: 'procaptcha-response', type: 'procaptcha' },
        },
    });
    const { GLSR } = window;
    const { config } = GLSR;
    assert.equal(GLSR.action, config.request.ajax.action)
    assert.equal(GLSR.ajax_pagination, config.pagination.fixed)
    assert.equal(GLSR.ajax_url, config.request.ajax.url)
    assert.equal(GLSR.modal_wrapped_by, config.modal.wrappedBy)
    assert.equal(GLSR.nameprefix, config.nameprefix)
    assert.equal(GLSR.rest_nonce, config.request.nonce)
    assert.equal(GLSR.rest_url, config.request.url)
    assert.equal(GLSR.stars_config, config.rating)
    assert.equal(GLSR.url_parameter, config.pagination.urlParameter)
    assert.equal(GLSR.validation_strings, config.validation.strings)
    assert.deepEqual(plain(GLSR.captcha), { captcha_type: 'frictionless', class: 'procaptcha', token_field: 'procaptcha-response', type: 'procaptcha' })
    assert.deepEqual(plain(GLSR.text), { close_modal: 'Close Modal', closemodal: 'Close Modal' })
    assert.equal(GLSR.validation_config.field_error, 'glsr-field-is-invalid')
    assert.equal(GLSR.validation_config.form_message_success, 'glsr-form-success')
    assert.equal(GLSR.validation_config.field, 'glsr-field')
    assert.equal('strings' in GLSR.validation_config, false)
    assert.deepEqual(Object.keys(GLSR).filter((key) => /^[a-z]/.test(key)).sort(), ['config', 'lib', 'registry', 'version'])
});

test('the seven names that 8.0.1 renamed read the same values', () => {
    const { GLSR } = loadPublic().window;
    assert.equal(GLSR.ajaxurl, GLSR.config.request.ajax.url)
    assert.deepEqual(plain(GLSR.ajaxpagination), plain(GLSR.config.pagination.fixed))
    assert.deepEqual(plain(GLSR.starsconfig), plain(GLSR.config.rating))
    assert.equal(GLSR.urlparameter, 'reviews-page')
    assert.deepEqual(plain(GLSR.validationconfig), plain(GLSR.validation_config))
    assert.deepEqual(plain(GLSR.validationstrings), plain(GLSR.config.validation.strings))
    assert.equal(GLSR.text.closemodal, 'Close Modal')
    assert.equal(Object.keys(GLSR).includes('ajaxurl'), false)
});

test('the compat script is handed its pieces, and puts none of them on GLSR', () => {
    const { GLSR } = loadPublic().window;
    for (const key of ['FormInstance', 'ModalInstance', 'report', 'retainForms']) {
        assert.equal(key in GLSR, false)
    }
    // the same script on a page whose main script did not run does nothing
    const { JSDOM } = require('jsdom');
    const { read } = loadPublic();
    const bare = new JSDOM('', { runScripts: 'outside-only' }).window;
    bare.eval(read('site-reviews-compat.js'))
    assert.equal('GLSR' in bare, false)
});

test('GLSR.addons keeps one object for each id, with what an 8.x filter wrote', () => {
    const { window } = loadPublic({
        added: { addons: { 'site-reviews-images': { maxfiles: 5, swiper: null } } },
        config: { themes: { swiper: { loop: true } } },
    });
    const { GLSR } = window;
    GLSR.Themes = { instances: [] };
    assert.equal(GLSR.addons['site-reviews-images'].maxfiles, 5)
    // an 8.x script can assign to its entry
    GLSR.addons['site-reviews-images'].swiper = 'assigned';
    assert.equal(GLSR.addons['site-reviews-images'].swiper, 'assigned')
    // a feature on the 8.4.0 API is read through its config subject and its module
    assert.deepEqual(plain(GLSR.addons['site-reviews-themes']), { instances: [], swiper: { loop: true } })
    assert.equal(Object.getOwnPropertyDescriptor(GLSR, 'addons').enumerable, false)
});

test('a key that an 8.x filter added, and 8.4.0 does not know, stays a plain key', () => {
    const { window } = loadPublic({ added: { my_snippet: { value: 1 } } });
    assert.deepEqual(plain(window.GLSR.my_snippet), { value: 1 })
});

test('a page cached with the inline script of 8.3 gets its config from the 8.x keys', () => {
    const { window } = loadPublic({ inline: PUBLIC_INLINE_83 });
    const { config } = window.GLSR;
    assert.equal(config.request.ajax.action, 'glsr_public_action')
    assert.equal(config.request.ajax.url, 'https://example.org/wp-admin/admin-ajax.php')
    assert.equal(config.request.nonce, false)
    assert.equal(config.request.url, 'https://example.org/wp-json/site-reviews/v1/')
    assert.deepEqual(plain(config.pagination), { fixed: ['#wpadminbar', '.site-navigation-fixed'], urlParameter: 'reviews-page' })
    assert.deepEqual(plain(config.modal), { wrappedBy: ['block'] })
    assert.deepEqual(plain(config.captcha), { class: 'glsr-cf-turnstile', sitekey: 'key', tokenField: 'cf-turnstile-response', type: 'turnstile' })
    assert.deepEqual(plain(config.text), { closeModal: 'Close Modal' })
    assert.deepEqual(plain(config.validation), {
        field: 'glsr-field',
        fieldError: 'glsr-field-is-invalid',
        form: 'glsr-form',
        strings: { errors: 'Please fix the submission errors.' },
    })
    assert.equal(Object.isFrozen(config), true)
    // the keys that the old inline script printed are still there, as it printed them
    assert.equal(window.GLSR.ajax_url, 'https://example.org/wp-admin/admin-ajax.php')
    assert.equal(window.GLSR.validation_config.field_error, 'glsr-field-is-invalid')
    // that page has no compat script, so the keys that the script defined are not
    assert.equal('Utils' in window.GLSR, false)
    assert.equal('ajax' in window.GLSR, false)
});

test('a deprecated key warns once, and only in debug mode', () => {
    const quiet = loadPublic();
    quiet.window.GLSR.Utils
    quiet.window.GLSR.ajax_url
    assert.equal(quiet.warnings.length, 0)

    const debug = loadPublic({ debug: true });
    debug.window.GLSR.Utils
    debug.window.GLSR.Utils
    debug.window.GLSR.ajax_url
    assert.equal(debug.warnings.length, 2)
    assert.match(debug.warnings[0], /GLSR\.Utils is deprecated; use GLSR\.Util instead\.$/)
    assert.match(debug.warnings[1], /GLSR\.ajax_url is deprecated; use GLSR\.config\.request\.ajax\.url instead\.$/)
});

test('no deprecated key is defined when the layer is off', () => {
    const { window } = loadPublic({ compat: false });
    assert.equal('Utils' in window.GLSR, false)
    assert.equal('ajax_url' in window.GLSR, false)
    assert.equal('addons' in window.GLSR, false)
    assert.equal('modify' in window.GLSR.Modal, false)
});

test('an invalid form shows the messages and classes that the config names', () => {
    const { window } = loadPublic({
        // jsdom has no layout: the validation takes offsetParent as "the field is visible"
        before: 'Object.defineProperty(HTMLElement.prototype, "offsetParent", { get () { return this.parentNode } });',
        html: `
            <div class="glsr">
                <form class="glsr-review-form glsr-form">
                    <div class="glsr-field">
                        <input type="text" name="site-reviews[title]" required>
                        <div class="glsr-field-error"></div>
                    </div>
                    <div class="glsr-form-message"></div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        `,
    });
    const { document } = window;
    const form = document.querySelector('form');
    window.GLSR_init()
    form.dispatchEvent(new window.Event('submit', { cancelable: true }))
    assert.equal(form.classList.contains('glsr-form-is-invalid'), true)
    assert.equal(document.querySelector('.glsr-field').classList.contains('glsr-field-is-invalid'), true)
    assert.equal(document.querySelector('input').classList.contains('glsr-is-invalid'), true)
    assert.equal(document.querySelector('.glsr-field-error').innerHTML, 'This field is required.')
    assert.equal(document.querySelector('.glsr-form-message').innerHTML, 'Please fix the submission errors.')
    assert.equal(document.querySelector('.glsr-form-message').classList.contains('glsr-form-failed'), true)
});
