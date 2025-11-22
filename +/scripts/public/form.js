/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */

import Button from '@/public/button.js';
import Captcha from '@/public/captcha.js';
import Conditions from '@/public/conditions.js';
import Session from '@/public/session.js';
import StarRating from '@/public/starrating.js';
import Validation from '@/public/validation.js';
import { addRemoveClass, classListSelector } from '@/public/helpers.js';

class Form {
    constructor (formEl, buttonEl) {
        this.button = Button(buttonEl);
        this.config = GLSR.validation_config;
        this.events = {
            reset: this._onReset.bind(this),
            submit: this._onSubmit.bind(this),
        };
        this.form = formEl;
        this.isActive = false;
        this.stars = StarRating();
        this.strings = GLSR.validation_strings;
        this.captcha = new Captcha(this);
        this.conditions = new Conditions(this);
        this.validation = new Validation(formEl);
        this.reviewsEl = document.getElementById(formEl.closest('.glsr')?.dataset?.reviews_id);
        this.session = new Session(formEl);
        this.summaryEl = document.getElementById(formEl.closest('.glsr')?.dataset?.summary_id);
    }

    destroy () {
        this._destroyForm()
        this.stars.destroy()
        this.captcha.reset()
        this.isActive = false;
    }

    init () {
        if (this.isActive) return;
        this._initForm()
        this.stars.init(this.form.querySelectorAll('.glsr-field-rating select'), GLSR.stars_config);
        this.captcha.render()
        this.isActive = true;
    }

    submitForm () {
        this.button.loading()
        GLSR.ajax.post(this._data(), this._handleResponse.bind(this))
    }

    _data () {
        const data = new FormData(this.form);
        const externals = {
            _reviews_atts: this.reviewsEl,
            _summary_atts: this.summaryEl,
        }
        if (this.reviewsEl) {
            data.append([`${GLSR.nameprefix}[_pagination_atts][page]`], 1);
            data.append([`${GLSR.nameprefix}[_pagination_atts][url]`], location.href);
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
                    data.append(`${GLSR.nameprefix}[${attrKey}][${key}]`, value);
                }
            } catch(e) {
                console.error(e)
            }
        }
        return data;
    }

    _destroyForm () {
        this.form.removeEventListener('reset', this.events.reset)
        this.form.removeEventListener('submit', this.events.submit)
        this._resetErrors()
        this.conditions.destroy()
        this.session.destroy()
        this.validation.destroy()
    }

    _handleResponse (response, success) {
        const wasSuccessful = true === success && undefined !== response;
        this.captcha.reset()
        if (wasSuccessful) {
            this.form.reset()
            this.session.clear()
        }
        this._showFieldErrors(response?.errors)
        this._showResults(response?.message, wasSuccessful)
        this.button.loaded()
        GLSR.Event.trigger('site-reviews/form/handle', response, this.form)
        if (wasSuccessful) {
            if (response.redirect && '' !== response.redirect) {
                window.location = response.redirect;
                return;
            }
            if (this.reviewsEl && response.reviews) {
                this.reviewsEl.innerHTML = response.reviews;
                if (GLSR.url_parameter) {
                    let url = new URL(location.href);
                    url.searchParams.delete(GLSR.url_parameter);
                    window.history.replaceState({}, '', url.toString());
                }
            }
            if (this.summaryEl && response.summary) {
                this.summaryEl.innerHTML = response.summary;
            }
            GLSR.Event.trigger('site-reviews/init')
        }
    }

    _initForm () {
        this._destroyForm()
        this.form.addEventListener('reset', this.events.reset)
        this.form.addEventListener('submit', this.events.submit)
        this.conditions.init()
        this.session.init()
        this.validation.init()
    }

    _onReset (ev) {
        this.conditions.destroy()
        this.conditions.init()
    }

    _onSubmit (ev) {
        if (!this.validation.validate()) {
            ev.preventDefault()
            this._showResults(this.strings.errors, false)
            return
        }
        ev.preventDefault()
        this._resetErrors()
        this.button.loading()
        this.captcha.execute()
    }

    _resetErrors () {
        addRemoveClass(this.form, this.config.form_error, false)
        this._showResults('', null)
        this.validation.reset()
    }

    _showFieldErrors (errors) {
        if (!errors) return;
        for (let error in errors) {
            if (!errors.hasOwnProperty(error)) continue;
            const nameSelector = GLSR.nameprefix ? GLSR.nameprefix + '[' + error + ']' : error;
            const inputEl = this.form.querySelector('[name="' + nameSelector + '"]');
            if (inputEl) {
                this.validation.setErrors(inputEl, errors[error])
                this.validation.toggleError(inputEl.validation, 'add')
            }
        }
    }

    _showResults (message, success) {
        if (!message) return;
        const resultsEl = this.form.querySelector(classListSelector(this.config.form_message));
        if (null !== resultsEl) {
            addRemoveClass(this.form, this.config.form_error, false === success)
            addRemoveClass(resultsEl, this.config.form_message_failed, false === success)
            addRemoveClass(resultsEl, this.config.form_message_success, true === success)
            resultsEl.innerHTML = message;
        }
    }
}

export default Form;
