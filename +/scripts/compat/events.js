/** global: GLSR, GLSR_init */

/**
 * The event names of Site Reviews before 9.0.0, each triggered beside its replacement.
 */

import { whenAttached } from '@/public/core/index.js';
import deprecated from '@/compat/deprecated.js';

// The shortcode of a block => the event that the block editor triggered for it.
const BLOCKS = {
    site_review: 'block:site-reviews/review',
    site_reviews: 'block:site-reviews/reviews',
    site_reviews_form: 'block:site-reviews/form',
    site_reviews_summary: 'block:site-reviews/summary',
};

const element = (el) => 1 === el?.nodeType ? el : document;

export default ({ listen }) => {
    const { Event } = window.GLSR;

    let isRelaying = false; // an old name relayed from here must not run its command
    let running = null; // the command that is running: its own relay is skipped
    let paginated = null; // the list whose page changed, until it is set up again

    const relay = (name, ...args) => {
        if (name === running) return;
        const wasRelaying = isRelaying;
        isRelaying = true;
        try {
            Event.trigger(name, ...args)
        } finally {
            isRelaying = wasRelaying;
        }
    }

    // a command that waits for its module keeps its name as running until the module has run it
    const command = (name, replacement, fn) => listen(name, (...args) => {
        if (isRelaying) return;
        deprecated(`Triggering ${name}`, replacement)
        running = name;
        const done = () => { running = null; };
        try {
            const result = fn(...args);
            if (result?.then) {
                result.then(done, done)
            } else {
                done()
            }
        } catch (error) {
            done()
            throw error;
        }
    })

    // the module may not be on the page yet: the command waits for it
    const review = (fn) => window.GLSR.load('reviews').then(() => fn(window.GLSR.Review))

    listen('site-reviews/initialized', ({ root }) => {
        if (root === document) {
            return relay('site-reviews/loaded');
        }
        // a block is its wrapper, or holds it (Divi)
        const block = root.dataset?.shortcode ? root : root.querySelector('.glsr[data-shortcode]');
        if (BLOCKS[block?.dataset.shortcode]) {
            relay(BLOCKS[block.dataset.shortcode], block, {})
        }
    })
    listen('site-reviews/review/paginated', ({ response, review }) => {
        paginated = review;
        relay('site-reviews/pagination/handle', response, review.pagination)
    })
    listen('site-reviews/review/initialized', ({ instances, root }) => {
        const isPageChange = paginated && paginated === instances[0];
        paginated = null;
        relay('site-reviews/excerpts/init', root === document ? undefined : root)
        relay('site-reviews/modal/init')
        if (!isPageChange) {
            relay('site-reviews/pagination/init')
        }
    })
    listen('site-reviews/form/initialized', () => relay('site-reviews/forms/init'))
    listen('site-reviews/form/submitted', ({ form, response }) => relay('site-reviews/form/handle', response, form.el))
    listen('site-reviews/modal/closed', ({ event, modal }) => relay('site-reviews/modal/close', modal, event))
    // site-reviews/modal/open fires before the dialog is shown, site-reviews/modal/opened after.
    whenAttached('modal').then(({ hooks }) => {
        hooks.beforeOpen = (modal, event) => relay('site-reviews/modal/open', modal, event);
    })

    command('site-reviews/excerpts/init', 'GLSR.Review.init(root)', (el) => review(Review => {
        Review.excerpts.init(element(el))
        Review.init() // the review modal too
    }))
    command('site-reviews/modal/init', 'GLSR.Review.init(root)', () => review(Review => Review.init()))
    command('site-reviews/pagination/init', 'GLSR.Review.init(root)', () => review(Review => Review.init()))
    command('site-reviews/forms/init', 'GLSR.Form.init(root)', () => window.GLSR.load('form').then(() => window.GLSR.Form.init()))
    Object.values(BLOCKS).forEach(name => command(name, 'GLSR_init(el)', (el) => window.GLSR_init(element(el))))
}
