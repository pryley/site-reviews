/** global: GLSR */

/**
 * The event names of Site Reviews before 8.4.0, each triggered beside its replacement.
 */

import deprecated from '@/compat/deprecated.js';

// The shortcode of a block => the event that the block editor triggered for it.
const BLOCKS = {
    site_review: 'block:site-reviews/review',
    site_reviews: 'block:site-reviews/reviews',
    site_reviews_form: 'block:site-reviews/form',
    site_reviews_summary: 'block:site-reviews/summary',
};

const element = (el) => 1 === el?.nodeType ? el : document;

export default ({ initReviewModal, listen, modalHooks }) => {
    const { Event, Form, Review, Summary } = window.GLSR;

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

    const command = (name, replacement, fn) => listen(name, (...args) => {
        if (isRelaying) return;
        deprecated(`Triggering ${name}`, replacement)
        running = name;
        try {
            fn(...args)
        } finally {
            running = null;
        }
    })

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
    modalHooks.beforeOpen = (modal, event) => relay('site-reviews/modal/open', modal, event);

    command('site-reviews/excerpts/init', 'GLSR.Review.init(root)', (el) => {
        Review.excerpts.init(el)
        initReviewModal()
        relay('site-reviews/modal/init')
    })
    command('site-reviews/modal/init', 'GLSR.Review.init(root)', initReviewModal)
    command('site-reviews/pagination/init', 'GLSR.Review.init(root)', () => {
        Review.instances.forEach(instance => instance._initPagination())
    })
    command('site-reviews/forms/init', 'GLSR.Form.init(root)', () => Form.init())
    command(BLOCKS.site_review, 'GLSR_init(el)', (el) => Review.init(element(el)))
    command(BLOCKS.site_reviews, 'GLSR_init(el)', (el) => Review.init(element(el)))
    command(BLOCKS.site_reviews_form, 'GLSR_init(el)', (el) => Form.init(element(el)))
    command(BLOCKS.site_reviews_summary, 'GLSR_init(el)', (el) => Summary.init(element(el)))
}
