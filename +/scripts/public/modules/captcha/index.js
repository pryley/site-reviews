/**
 * @site-reviews/captcha: the captcha of a review form. A form root names it beside `form`
 * (data-glsr-modules="form captcha") when the site has a captcha, and it takes over the form's submit.
 */
import { attach, Event, load } from '@/public/core/index.js';
import Captcha from './captcha.js';

const guard = (form) => {
    if (form.captcha instanceof Captcha) return;
    form.captcha = new Captcha(form);
    if (form.isActive) {
        form.captcha.render()
    }
}

// a form set up before this module loaded, or after it
Event.on('site-reviews/form/initialized', ({ instances }) => instances.forEach(guard))

attach('captcha', (root) => {
    load('form').then(() => window.GLSR.Form.instances.filter(form => root.contains(form.el) || root === form.el).forEach(guard))
})

export default Captcha
