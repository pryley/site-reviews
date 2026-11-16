const assert = require('node:assert/strict');
const { test } = require('node:test');
const { POPOVER, loadAdmin } = require('./harness.js');

// the markup of Modules\Html\Tooltip
const CODE = '<button id="args" type="button" class="glsr-tooltip-button" aria-label="Args" popovertarget="c-args"></button><div id="c-args" class="glsr-tooltip is-code" popover="auto"><pre><code class="language-php">[]</code></pre></div>';
const HELP = '<button id="dot" type="button" class="glsr-help glsr-tooltip-button" aria-label="More information about Style" popovertarget="h-style"></button><div id="h-style" class="glsr-tooltip" popover="auto">Read <a id="link" href="https://example.org/">the guide</a>.</div>';
const TOOLTIP = '<button id="reset" type="button" data-tooltip="t-reset"></button><span id="t-reset" class="glsr-tooltip" popover="manual" role="tooltip">Reset value to default</span>';

const DELAY = 350; // longer than the 300ms a hover waits
const GRACE = 250; // longer than the 200ms a tooltip waits for the pointer

const load = html => {
    const { document } = loadAdmin({ before: POPOVER, html }).window;
    return id => document.getElementById(id);
};
const fire = (el, type, init = {}) => {
    const { Event, FocusEvent } = el.ownerDocument.defaultView;
    const ev = 'focusout' === type ? new FocusEvent(type, { bubbles: true, ...init }) : new Event(type, { cancelable: true });
    el.dispatchEvent(ev)
    return ev;
};
const isOpen = el => 'open' in el.dataset;
const press = (el, key) => el.dispatchEvent(new el.ownerDocument.defaultView.KeyboardEvent('keydown', { bubbles: true, key }));
const wait = ms => new Promise(resolve => setTimeout(resolve, ms));

test('a tooltip shows at once on focus and after a delay on hover', async () => {
    const el = load(TOOLTIP);
    fire(el('reset'), 'focus')
    assert.equal(isOpen(el('t-reset')), true)
    fire(el('reset'), 'blur')
    assert.equal(isOpen(el('t-reset')), false)
    fire(el('reset'), 'mouseenter')
    assert.equal(isOpen(el('t-reset')), false)
    await wait(DELAY)
    assert.equal(isOpen(el('t-reset')), true)
    fire(el('reset'), 'mouseleave')
    await wait(GRACE)
    assert.equal(isOpen(el('t-reset')), false)
});

test('a tooltip stays open while the pointer is in it', async () => {
    const el = load(TOOLTIP);
    fire(el('reset'), 'mouseenter')
    await wait(DELAY)
    fire(el('reset'), 'mouseleave')
    fire(el('t-reset'), 'mouseenter')
    await wait(GRACE)
    assert.equal(isOpen(el('t-reset')), true)
    fire(el('t-reset'), 'mouseleave')
    fire(el('reset'), 'mouseenter') // back to its element
    await wait(GRACE)
    assert.equal(isOpen(el('t-reset')), true)
    fire(el('reset'), 'mouseleave')
    await wait(GRACE)
    assert.equal(isOpen(el('t-reset')), false)
});

test('a tooltip hides when its element is clicked and Escape hides it', () => {
    const el = load(TOOLTIP);
    fire(el('reset'), 'focus')
    fire(el('reset'), 'mousedown')
    assert.equal(isOpen(el('t-reset')), false)
    fire(el('reset'), 'focus') // the focus a click gives
    assert.equal(isOpen(el('t-reset')), false)
    fire(el('reset'), 'blur')
    fire(el('reset'), 'focus')
    assert.equal(isOpen(el('t-reset')), true)
    press(el('reset'), 'Escape')
    assert.equal(isOpen(el('t-reset')), false)
});

test('Escape hides a tooltip that hover opened, whatever has the focus', async () => {
    const el = load(TOOLTIP);
    fire(el('reset'), 'mouseenter')
    await wait(DELAY)
    press(el('reset').ownerDocument.body, 'Enter')
    assert.equal(isOpen(el('t-reset')), true)
    press(el('reset').ownerDocument.body, 'Escape')
    assert.equal(isOpen(el('t-reset')), false)
    fire(el('reset'), 'mouseleave')
    fire(el('reset'), 'mouseenter')
    await wait(DELAY)
    assert.equal(isOpen(el('t-reset')), true) // it shows again
});

test('a pointer that leaves before the delay never shows the tooltip', async () => {
    const el = load(TOOLTIP);
    fire(el('reset'), 'mouseenter')
    fire(el('reset'), 'mouseleave')
    await wait(DELAY)
    assert.equal(isOpen(el('t-reset')), false)
});

test('the help opens on hover and stays open while the pointer is in it', async () => {
    const el = load(HELP);
    fire(el('dot'), 'mouseenter')
    await wait(DELAY)
    assert.equal(isOpen(el('h-style')), true)
    fire(el('dot'), 'mouseleave')
    fire(el('h-style'), 'mouseenter')
    await wait(GRACE)
    assert.equal(isOpen(el('h-style')), true)
    fire(el('h-style'), 'mouseleave')
    await wait(GRACE)
    assert.equal(isOpen(el('h-style')), false)
});

test('the help opens on focus and closes when focus has left its button and its content', () => {
    const el = load(HELP);
    fire(el('dot'), 'focus')
    assert.equal(isOpen(el('h-style')), true)
    fire(el('dot'), 'focusout', { relatedTarget: el('link') })
    assert.equal(isOpen(el('h-style')), true)
    fire(el('link'), 'focusout', { relatedTarget: null })
    assert.equal(isOpen(el('h-style')), false)
});

test('a click keeps the help open and a second click closes it', async () => {
    const el = load(HELP);
    fire(el('dot'), 'mouseenter')
    await wait(DELAY)
    assert.equal(fire(el('dot'), 'click').defaultPrevented, true) // the browser would close what hover opened
    assert.equal(isOpen(el('h-style')), true)
    fire(el('dot'), 'mouseleave')
    await wait(GRACE)
    assert.equal(isOpen(el('h-style')), true)
    fire(el('dot'), 'click')
    assert.equal(isOpen(el('h-style')), false)
});

test('the help that the browser closed is not kept open the next time', async () => {
    const el = load(HELP);
    fire(el('dot'), 'click')
    el('h-style').hidePopover() // Escape, or a click outside it
    fire(el('dot'), 'mouseenter')
    await wait(DELAY)
    assert.equal(isOpen(el('h-style')), true)
    fire(el('dot'), 'mouseleave')
    await wait(GRACE)
    assert.equal(isOpen(el('h-style')), false)
});

test('a popover with a plain button is left to the browser', async () => {
    const el = load(CODE);
    assert.equal(fire(el('args'), 'click').defaultPrevented, false)
    fire(el('args'), 'mouseenter')
    fire(el('args'), 'focus')
    await wait(DELAY)
    assert.equal(isOpen(el('c-args')), false)
});

test('the admin script loads on a browser without the Popover API', () => {
    const { document } = loadAdmin({ html: TOOLTIP }).window;
    document.getElementById('reset').dispatchEvent(new document.defaultView.Event('focus'))
    assert.equal(document.getElementById('t-reset').hasAttribute('data-open'), false)
});
