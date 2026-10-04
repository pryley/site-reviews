const assert = require('node:assert/strict');
const path = require('path');
const { execFileSync } = require('child_process');
const { test } = require('node:test');
const { loadPublic } = require('./harness.js');

// The released addon scripts are read from the tag of each repository beside this plugin;
// where one is missing, as in CI, its tests are skipped.
const REPOSITORIES = path.resolve(__dirname, '../../../..');

// data: what PHP prints as GLSR.addons[id]; uses: the old names the script uses as the page loads
const ADDONS = {
    'site-reviews-actions': {
        data: null,
        version: '1.0.0-beta15',
        uses: ['GLSR.Utils', 'site-reviews/modal/open', 'site-reviews/pagination/handle', 'site-reviews/pagination/init'],
    },
    'site-reviews-authors': {
        data: null,
        version: '2.0.2',
        uses: ['GLSR.Utils', 'site-reviews/modal/open', 'site-reviews/pagination/handle', 'site-reviews/pagination/init'],
    },
    'site-reviews-filters': {
        data: { allowed: ['filter_by_rating', 'filter_by_term', 'search_for', 'sort_by', 'filter_by_media'], filters: [] },
        version: '4.0.3',
        uses: ['GLSR.addons'],
    },
    'site-reviews-images': {
        data: {
            acceptedfiles: 'image/avif,image/jpeg,image/png,image/webp',
            action: 'site-reviews-images',
            maxfiles: 5,
            maxfilesize: 5,
            modal: 'lightbox',
            nonce: 'abc123',
            swiper: null,
            text: { cancel: 'Cancel', imageGallery: 'Image Gallery', removeImage: 'Remove image', save: 'Save' },
        },
        version: '5.0.4',
        uses: ['GLSR.Utils', 'GLSR.addons', 'GLSR.ajax_url', 'block:site-reviews/form', 'site-reviews/modal/open', 'site-reviews/pagination/handle', 'site-reviews/pagination/popstate'],
    },
    'site-reviews-themes': {
        data: { masonry: [], swipers: [] },
        version: '1.0.0-beta63',
        uses: ['GLSR.addons', 'block:site-reviews/reviews', 'site-reviews/modal/open', 'site-reviews/pagination/handle', 'site-reviews/pagination/init'],
    },
};

const PAGE = `
    <div class="glsr glsr-default" id="reviews" data-shortcode="site_reviews">
        <div class="glsr-reviews-wrap">
            <div class="glsr-reviews"><div class="glsr-review" id="review-1"><p>A review.</p></div></div>
            <div class="glsr-pagination glsr-ajax-pagination"><a data-page="2" href="?reviews-page=2">2</a></div>
        </div>
    </div>
    <div class="glsr glsr-default" id="form" data-shortcode="site_reviews_form">
        <form class="glsr-review-form glsr-form">
            <div class="glsr-field"><input type="text" name="site-reviews[title]"></div>
            <button type="submit">Submit</button>
        </form>
    </div>`;

// A debug warning => the old key or the old event name that it reports.
const reported = (warning) => warning.match(/listens to (\S+?),/)?.[1] ?? warning.match(/\] (GLSR\.\S+) is deprecated/)?.[1] ?? warning;

const ready = () => new Promise(resolve => setTimeout(resolve, 25));

const released = (id) => {
    try {
        return execFileSync('git', ['-C', path.join(REPOSITORIES, id), 'show', `v${ADDONS[id].version}:assets/${id}.js`], {
            encoding: 'utf8',
            stdio: ['ignore', 'pipe', 'ignore'],
        });
    } catch (error) {
        return null;
    }
};

const loadAddon = (id, script, options = {}) => {
    const page = loadPublic({ added: { addons: { [id]: ADDONS[id].data } }, debug: true, html: PAGE, ...options });
    const { window } = page;
    const errors = [];
    const heard = {};
    window.addEventListener('error', (event) => errors.push(event.message))
    const { on } = window.GLSR.Event;
    window.GLSR.Event.on = (name, fn, ...args) => on(name, function (...data) {
        heard[name] = (heard[name] || 0) + 1;
        return fn.apply(this, data);
    }, ...args);
    return { ...page, errors, heard, run: () => window.eval(script) };
};

const skipped = (id, script) => null === script ? `${id} ${ADDONS[id].version} is not beside this plugin` : false;

for (const [id, { uses, version }] of Object.entries(ADDONS)) {
    const script = released(id);
    test(`${id} ${version} runs beside the compat script, and its listener of site-reviews/init is called`, { skip: skipped(id, script) }, async () => {
        const { errors, heard, run, warnings, window } = loadAddon(id, script);
        run()
        await ready()
        assert.deepEqual(errors, [])
        assert.equal(heard['site-reviews/init'], 1)
        // an addon triggers this itself after it has put markup on the page
        window.GLSR.Event.trigger('site-reviews/init')
        assert.deepEqual(errors, [])
        assert.equal(heard['site-reviews/init'], 2)
        assert.deepEqual(warnings.map(reported).sort(), uses)
    });
}

{
    const id = 'site-reviews-actions';
    const script = released(id);
    test('without the compat script, a released addon script fails as it is parsed', { skip: skipped(id, script) }, () => {
        const { run } = loadAddon(id, script, { compat: false, debug: false });
        assert.throws(run, /Cannot read properties of undefined/)
    });
}
