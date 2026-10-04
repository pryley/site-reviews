const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadBoth } = require('./harness.js');

const plain = (value) => JSON.parse(JSON.stringify(value));

const ORDERS = [['public', 'admin'], ['admin', 'public']];

test('both scripts run on one page without an error, in either order', () => {
    for (const order of ORDERS) {
        const { errors, window } = loadBoth({ order });
        assert.deepEqual(errors, [], order.join(', '))
        assert.equal(typeof window.GLSR.Form.init, 'function')
        assert.equal(typeof window.GLSR.Util.debounce, 'function')
        assert.equal(typeof window.GLSR_init, 'function')
    }
});

test('the config holds the subjects of both scripts', () => {
    for (const order of ORDERS) {
        const { config } = loadBoth({ order }).window.GLSR;
        assert.equal(config.nonce['toggle-filters'], 'abc123') // admin
        assert.equal(config.validation.field, 'glsr-field') // public
        assert.deepEqual(plain(config.rating), { clearable: false, max: 5, min: 0, tooltip: 'Select a Rating' })
        assert.equal(config.text.cancel, 'Cancel')
        assert.equal(config.text.closeModal, 'Close Modal')
        assert.equal(Object.isFrozen(config), true)
    }
});

test('the two scripts share one list of listeners', () => {
    for (const order of ORDERS) {
        const { window } = loadBoth({ html: '<div class="glsr"><form class="glsr-review-form"><button type="submit">Submit</button></form></div>', order });
        const heard = [];
        window.GLSR.Event.on('site-reviews/initialized', ({ root }) => heard.push(root === window.document))
        window.GLSR_init()
        assert.deepEqual(heard, [true], order.join(', '))
        assert.equal(window.GLSR.Form.instances.length, 1)
    }
});

test('the two scripts share one registry, which runs the compat script of each', () => {
    for (const order of ORDERS) {
        const { window } = loadBoth({ debug: true, order });
        const ids = plain(window.GLSR.registry.defined()).filter(({ ran }) => ran).map(({ id }) => id).sort();
        assert.deepEqual(ids, ['compat.admin', 'compat.public', 'debug.admin', 'debug.public'], order.join(', '))
        // the pieces that each script was handed are not kept
        assert.deepEqual(Object.keys(window.GLSR.registry[window.Symbol.for('site-reviews.registry')].hosted), [])
        // a key of the public script, a key of the admin script, and one that both define
        assert.equal(window.GLSR.forms.length, 0)
        assert.equal(window.GLSR.addonsurl, window.GLSR.config.urls.addons)
        assert.equal(window.GLSR.nameprefix, 'site-reviews')
    }
});

test('a deprecated key can be assigned, and then holds what was assigned', () => {
    const { errors, window } = loadBoth();
    window.eval('"use strict"; GLSR.validation_strings = { required: "Mine" }; GLSR.Utils = 1;')
    assert.deepEqual(errors, [])
    assert.deepEqual(plain(window.GLSR.validation_strings), { required: 'Mine' })
    assert.equal(window.GLSR.Utils, 1)
    assert.equal(window.GLSR.config.validation.strings.required, 'This field is required.')
});
