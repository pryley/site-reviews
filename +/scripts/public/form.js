/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */

import Recaptcha from './recaptcha.js';
import StarRating from 'star-rating.js/src';
import Validation from './validation.js';
import { addRemoveClass, classListSelector } from './helpers.js';

class Form {
    constructor (formEl, buttonEl) {
        this.button = buttonEl;
        this.config = GLSR.validationconfig;
        this.events = {
            submit: this._onSubmit.bind(this),
        };
        this.form = formEl;
        this.isActive = false;
        this.recaptcha = new Recaptcha(this);
        this.stars = null;
        this.strings = GLSR.validationstrings;
        this.useAjax = !formEl.classList.contains('no-ajax');
        this.validation = new Validation(formEl);
    }

    destroy () {
        this._destroyForm()
        this._destroyRecaptcha()
        this._destroyStarRatings()
        this.isActive = false;
    }

    disableButton () {
        this.button.setAttribute('aria-busy', 'true')
        this.button.setAttribute('disabled', '')
    }

    enableButton () {
        this.button.setAttribute('aria-busy', 'false')
        this.button.removeAttribute('disabled')
    }

    init () {
        if (this.isActive) return;
        this._initForm()
        this._initStarRatings()
        this._initRecaptcha()
        this.isActive = true;
    }

    submitForm (counter) {
        this.disableButton()
        this.form[GLSR.nameprefix + '[_counter]'].value = counter || 0;
        GLSR.ajax.post(this.form, this._handleResponse.bind(this))
    }

    _destroyForm () {
        this.form.removeEventListener('submit', this.events.submit)
        this._resetErrors()
        this.validation.destroy()
    }

    _destroyRecaptcha () {
        this.recaptcha.reset()
    }

    _destroyStarRatings () {
        if (this.stars) {
            this.stars.destroy()
        }
    }

    _handleResponse (response, success) {
        const wasSuccessful = success === true;
        if ('unset' === response.recaptcha) {
            this.recaptcha.execute()
            return
        }
        if ('reset' === response.recaptcha) {
            this.recaptcha.reset()
        }
        if (wasSuccessful) {
            this.recaptcha.reset()
            this.form.reset()
        }
        this._showFieldErrors(response.errors)
        this._showResults(response.message, wasSuccessful)
        this.enableButton()
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

    _initRecaptcha () {
        this.recaptcha.render()
    }

    _initStarRatings () {
        if (null !== this.stars) {
            this.stars.rebuild()
        } else {
            this.stars = new StarRating(this.form.querySelectorAll('.glsr-field-rating select'), GLSR.stars);
        }
    }

    _onSubmit (ev) {
        if (!this.validation.validate()) {
            ev.preventDefault()
            this._showResults(this.strings.errors, false)
            return
        }
        this._resetErrors()
        if (!this.form['g-recaptcha-response'] || '' !== this.form['g-recaptcha-response'].value) {
            if (!this.useAjax) return;
        }
        ev.preventDefault()
        this.submitForm()
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
