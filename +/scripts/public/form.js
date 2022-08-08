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
        this.useAjax = !formEl.classList.contains('no-ajax');
        this.captcha = new Captcha(this);
        this.validation = new Validation(formEl);
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
        GLSR.ajax.post(this.form, this._handleResponse.bind(this))
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
        response.form = this.form; // @compat
        document.dispatchEvent(new CustomEvent('site-reviews/after/submission', { detail: response })) // @compat
        if (wasSuccessful && '' !== response.redirect) {
            window.location = response.redirect;
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
