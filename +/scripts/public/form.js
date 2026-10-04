/** global: FormData */

import Button from '@/public/button.js';
import Captcha from '@/public/captcha.js';
import config from '@/public/config.js';
import Conditions from '@/public/conditions.js';
import Event from '@/public/event.js';
import feature, { direction } from '@/public/feature.js';
import report from '@/public/report.js';
import Request from '@/public/request.js';
import Review from '@/public/review.js';
import Session from '@/public/session.js';
import StarRating from '@/public/starrating.js';
import Summary from '@/public/summary.js';
import Validation from '@/public/validation.js';
import { addRemoveClass, classListSelector } from '@/public/helpers.js';

class Form {
    constructor (formEl) {
        this.button = Button(formEl.querySelector('[type=submit]'));
        this.el = formEl;
        this.isActive = false;
        this.captcha = new Captcha(this);
        this.conditions = new Conditions(this);
        this.session = new Session(formEl);
        this.validation = new Validation(formEl);
        this._config = config.validation;
        this._events = {
            reset: this._onReset.bind(this),
            submit: this._onSubmit.bind(this),
        };
        this._stars = StarRating();
    }

    destroy () {
        this._destroyForm()
        this._stars.destroy()
        this.captcha.reset()
        this.isActive = false;
    }

    init () {
        if (this.isActive) return;
        const wrapperEl = this.el.closest('.glsr');
        if (wrapperEl) {
            direction(wrapperEl)
        }
        this._initForm()
        this._stars.init(this.el.querySelectorAll('.glsr-field-rating select'), { ...config.rating });
        this.captcha.render()
        this.isActive = true;
    }

    submit () {
        this.button.loading()
        Request.submit(this._data()).then(result => this._handleResponse(result.data, result.success, result.status))
    }

    _data () {
        const data = new FormData(this.el);
        const externals = {
            _reviews_atts: this._linked('reviews_id'),
            _summary_atts: this._linked('summary_id'),
        }
        if (externals._reviews_atts) {
            data.append([`${config.nameprefix}[_pagination_atts][page]`], 1);
            data.append([`${config.nameprefix}[_pagination_atts][url]`], location.href);
        }
        for (let attrKey in externals) {
            if (!externals[attrKey]) continue;
            try {
                const dataset = JSON.parse(JSON.stringify(externals[attrKey].dataset));
                for (let key of Object.keys(dataset)) {
                    let value;
                    try {
                        value = JSON.parse(dataset[key]);
                    } catch(e) {
                        value = dataset[key];
                    }
                    data.append(`${config.nameprefix}[${attrKey}][${key}]`, value);
                }
            } catch(e) {
                console.error(e)
            }
        }
        return data;
    }

    _destroyForm () {
        this.el.removeEventListener('reset', this._events.reset)
        this.el.removeEventListener('submit', this._events.submit)
        this._resetErrors()
        this.conditions.destroy()
        this.session.destroy()
        this.validation.destroy()
    }

    _handleResponse (response, success, status) {
        const wasSuccessful = true === success && undefined !== response;
        report('form', { response, status, success: wasSuccessful })
        this.captcha.reset()
        if (wasSuccessful) {
            this.el.reset()
            this.session.clear()
        }
        this._showFieldErrors(response?.errors)
        this._showResults(response?.message, wasSuccessful)
        this.button.loaded()
        Event.trigger('site-reviews/form/submitted', { form: this, response, success: wasSuccessful })
        if (wasSuccessful) {
            if (response.redirect && '' !== response.redirect) {
                window.location = response.redirect;
                return;
            }
            this._refresh(response)
        }
    }

    _initForm () {
        this._destroyForm()
        this.el.addEventListener('reset', this._events.reset)
        this.el.addEventListener('submit', this._events.submit)
        this.conditions.init()
        this.session.init()
        this.validation.init()
    }

    _linked (key) {
        return document.getElementById(this.el.closest('.glsr')?.dataset?.[key]);
    }

    _onReset (ev) {
        this.conditions.destroy()
        this.conditions.init()
    }

    _onSubmit (ev) {
        if (!this.validation.validate()) {
            ev.preventDefault()
            this._showResults(this._config.strings.errors, false)
            return
        }
        ev.preventDefault()
        this._resetErrors()
        this.button.loading()
        this.captcha.execute()
    }

    _refresh (response) {
        const reviewsEl = this._linked('reviews_id');
        const summaryEl = this._linked('summary_id');
        if (reviewsEl && response.reviews) {
            reviewsEl.innerHTML = response.reviews;
            if (config.pagination.urlParameter) {
                let url = new URL(location.href);
                url.searchParams.delete(config.pagination.urlParameter);
                window.history.replaceState({}, '', url.toString());
            }
            Review.init(reviewsEl)
        }
        if (summaryEl && response.summary) {
            const summary = Summary.init(summaryEl).find(instance => instance.el === summaryEl);
            if (summary) {
                summary.update(response.summary)
            } else {
                summaryEl.innerHTML = response.summary; // the id is on an element that is not a summary
            }
        }
        this.destroy()
        this.init()
    }

    _resetErrors () {
        addRemoveClass(this.el, this._config.formError, false)
        this._showResults('', null)
        this.validation.reset()
    }

    _showFieldErrors (errors) {
        if (!errors) return;
        for (let error in errors) {
            if (!errors.hasOwnProperty(error)) continue;
            const nameSelector = config.nameprefix ? config.nameprefix + '[' + error + ']' : error;
            const inputEl = this.el.querySelector('[name="' + nameSelector + '"]');
            if (inputEl) {
                this.validation.setErrors(inputEl, errors[error])
                this.validation.toggleError(inputEl.validation, 'add')
            }
        }
    }

    _showResults (message, success) {
        if (!message) return;
        const resultsEl = this.el.querySelector(classListSelector(this._config.formMessage));
        if (null !== resultsEl) {
            addRemoveClass(this.el, this._config.formError, false === success)
            addRemoveClass(resultsEl, this._config.formMessageFailed, false === success)
            addRemoveClass(resultsEl, this._config.formMessageSuccess, true === success)
            resultsEl.innerHTML = message;
        }
    }
}

const elements = (root) => {
    const forms = [...root.querySelectorAll('form.glsr-review-form')];
    if (root.matches?.('form.glsr-review-form')) {
        forms.push(root)
    }
    return forms.filter(formEl => formEl.querySelector('[type=submit]'));
}

const { module, retain } = feature(elements, el => new Form(el));

const init = module.init;

module.init = (root = document) => {
    const instances = init(root);
    Event.trigger('site-reviews/form/initialized', { instances, root })
    return instances;
}

export { Form as Instance, retain }

export default module
