const assert = require('node:assert/strict');
const { test } = require('node:test');
const vm = require('node:vm');
const { read } = require('./harness.js');

/*
 * The compat script of the block editor: it gives the form, reviews and summary blocks a
 * deprecated version that reads a block saved before 8.0.0 and returns today's attributes.
 * WordPress applying that version to a saved block is checked in a browser, not here.
 */

// as block.json declares them today
const TODAY = {
    assigned_posts: { default: [], items: { type: 'string' }, type: 'array' },
    assigned_terms: { default: [], items: { type: 'string' }, type: 'array' },
    assigned_users: { default: [], items: { type: 'string' }, type: 'array' },
    className: { default: '', type: 'string' },
    hide: { default: [], items: { type: 'string' }, type: 'array' },
    id: { default: '', type: 'string' },
};
const WITH_SCHEMA = {
    ...TODAY,
    rating: { default: 1, type: 'number' },
    schema: { default: 0, enum: [0, 1], type: 'number' },
    type: { default: '', type: 'string' },
};

// a value made by the script belongs to its own context, which deepEqual tells apart
const plain = (value) => JSON.parse(JSON.stringify(value));

const settings = (attributes) => ({ attributes, save: () => null, supports: { html: false } });

// the callback the script adds to blocks.registerBlockType
const filter = () => {
    const filters = [];
    const hooks = { addFilter: (hook, namespace, callback) => filters.push({ callback, hook, namespace }) };
    vm.runInNewContext(read('site-reviews-blocks-compat.js'), { window: { wp: { hooks } } })
    assert.equal(filters.length, 1)
    assert.equal(filters[0].hook, 'blocks.registerBlockType')
    return filters[0].callback;
};

const version = (name, attributes = WITH_SCHEMA) => {
    const { deprecated } = filter()(settings(attributes), name, null);
    assert.equal(deprecated.length, 1)
    return deprecated[0];
};

test('the form, reviews and summary blocks get one deprecated version and no other block does', () => {
    const callback = filter();
    for (const name of ['site-reviews/form', 'site-reviews/reviews', 'site-reviews/summary']) {
        assert.equal(callback(settings(TODAY), name, null).deprecated.length, 1)
    }
    assert.equal(callback(settings(TODAY), 'site-reviews/review', null).deprecated, undefined)
    assert.equal(callback(settings(TODAY), 'core/paragraph', null).deprecated, undefined)
});

test('a deprecated version that WordPress passes through the filter is left as it is', () => {
    const callback = filter();
    const deprecation = version('site-reviews/form');
    assert.equal(callback(deprecation, 'site-reviews/form', deprecation), deprecation)
});

test('the version reads a saved block with the attributes of today and those that 8.0.0 replaced', () => {
    const { attributes, save, supports } = version('site-reviews/reviews');
    assert.deepEqual(plain(attributes.rating), WITH_SCHEMA.rating) // not the 0 of 7.x
    assert.deepEqual(plain(attributes.type), WITH_SCHEMA.type) // not the "local" of 7.x
    assert.deepEqual(plain(attributes.hide.type), ['array', 'string'])
    assert.deepEqual(plain(attributes.schema.type), ['boolean', 'number'])
    assert.equal(attributes.assigned_to.type, 'string')
    assert.equal(save(), null)
    assert.deepEqual(plain(supports), { html: false })
});

test('a block saved before 8.0.0 is eligible and a block saved since is not', () => {
    const { isEligible } = version('site-reviews/reviews');
    assert.equal(isEligible({ assign_to: 'post_id' }), true)
    assert.equal(isEligible({ assigned_to: 'custom', assigned_posts: '12' }), true)
    assert.equal(isEligible({ category: 7 }), true)
    assert.equal(isEligible({ hide: 'title,date' }), true)
    assert.equal(isEligible({ post_id: '9' }), true)
    assert.equal(isEligible({ schema: true }), true)
    assert.equal(isEligible({ user: 'author_id' }), true)
    assert.equal(isEligible({}), false)
    assert.equal(isEligible({ assigned_posts: ['post_id'], className: 'is-wide', hide: ['title'], schema: 1 }), false)
});

test('a form block saved before 8.0.0 becomes a form block of today', () => {
    const { migrate } = version('site-reviews/form', TODAY);
    const read = { assign_to: 'post_id', assigned_posts: '', assigned_terms: '', assigned_users: '', category: '', className: 'is-wide', hide: 'title, name', id: 'abc', post_id: '', schema: false, user: 'user_id' };
    assert.deepEqual(plain(migrate(read)), {
        assigned_posts: ['post_id'],
        assigned_terms: [],
        assigned_users: ['user_id'],
        className: 'is-wide',
        hide: ['title', 'name'],
        id: 'abc',
    })
});

test('a reviews block saved before 8.0.0 keeps its typed IDs, its category and its schema', () => {
    const { migrate } = version('site-reviews/reviews');
    const read = { assigned_to: 'custom', assigned_posts: '12, 15', assigned_terms: '', assigned_users: '', category: 7, className: '', hide: '', id: '', post_id: '9', rating: 1, schema: true, type: '', user: 'custom' };
    assert.deepEqual(plain(migrate(read)), {
        assigned_posts: ['12', '15'],
        assigned_terms: ['7'],
        assigned_users: [],
        className: '',
        hide: [],
        id: '',
        rating: 1,
        schema: 1,
        type: '',
    })
});

test('the script does nothing on a page without the hooks of WordPress', () => {
    assert.doesNotThrow(() => vm.runInNewContext(read('site-reviews-blocks-compat.js'), { window: {} }))
});
