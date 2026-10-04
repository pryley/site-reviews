import Event from '@/public/event.js';
import feature, { direction, wrappers } from '@/public/feature.js';

class Summary {
    constructor (el) {
        this.el = el;
    }

    destroy () {}

    init () {
        direction(this.el)
    }

    update (html) {
        this.el.innerHTML = html;
        Event.trigger('site-reviews/summary/updated', { summary: this })
    }
}

const { module } = feature(root => wrappers(root, '.glsr-summary-wrap'), el => new Summary(el));

export default module
