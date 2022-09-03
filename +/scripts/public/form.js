/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */

import Button from '@/public/button.js';
import Captcha from '@/public/captcha.js';
import StarRating from '@/public/starrating.js';
import Validation from '@/public/validation.js';
import { addRemoveClass, classListSelector } from '@/public/helpers.js';

class Form {
    constructor (formEl, buttonEl) {
        this.button = Button(buttonEl);
        this.config = GLSR.validationconfig;
        this.events = {
            submit: this._onSubmit.bind(this),
        };
        this.form = formEl;
        this.isActive = false;
        this.stars = StarRating();
        this.strings = GLSR.validationstrings;
        this.captcha = new Captcha(this);
        this.validation = new Validation(formEl);
        this.reviewsEl = document.getElementById(formEl.closest('.glsr')?.dataset?.reviews_id);
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
        this.stars.init(this.form.querySelectorAll('.glsr-field-rating select'), GLSR.starsconfig);
        this.captcha.render()
        this.isActive = true;
    }

    submitForm (token) {
        this.button.loading()
        if (this.form['g-recaptcha-response']) {
            this.form['g-recaptcha-response'].value = token;
        }
        GLSR.ajax.post(this._data(), this._handleResponse.bind(this))
    }

    _data () {
        const data = new FormData(this.form);
        if (this.reviewsEl && this.reviewsEl.classList.contains('glsr')) {
            try {
                const dataset = JSON.parse(JSON.stringify(this.reviewsEl.dataset));
                for (let key of Object.keys(dataset)) {
                    let value;
                    try {
                        value = JSON.parse(dataset[key]);
                    } catch(e) {
                        value = dataset[key];
                    }
                    data.append(`${GLSR.nameprefix}[_reviews_atts][${key}]`, value);
                }
                data.append([`${GLSR.nameprefix}[_pagination_atts][page]`], 1);
                data.append([`${GLSR.nameprefix}[_pagination_atts][url]`], location.href);
            } catch(e) {
                console.error(e)
            }
        }
        return data;
    }

    _destroyForm () {
        this.form.removeEventListener('submit', this.events.submit)
        this._resetErrors()
        this.validation.destroy()
    }

    _handleResponse (response, success) {
        const wasSuccessful = success === true;
        this.captcha.reset()
        if (wasSuccessful) {
            this.form.reset()
        }
        this._showFieldErrors(response.errors)
        this._showResults(response.message, wasSuccessful)
        this.button.loaded()
        GLSR.Event.trigger('site-reviews/form/handle', response, this.form)
        if (wasSuccessful) {
            if ('' !== response.redirect) {
                window.location = response.redirect;
                return;
            }
            if (this.reviewsEl && response.reviews) {
                this.reviewsEl.innerHTML = response.reviews;
                if (GLSR.urlparameter) {
                    let url = new URL(location.href);
                    url.searchParams.delete(GLSR.urlparameter);
                    window.history.replaceState({}, '', url.toString());
                }
                GLSR.Event.trigger('site-reviews/pagination/init')
            }
        }
    }

    _initForm () {
        this._destroyForm()
        this.form.addEventListener('submit', this.events.submit)
        this.validation.init()
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
