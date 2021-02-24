/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */

import Recaptcha from './recaptcha.js';
import StarRating from 'star-rating.js';
import Validation from './validation.js';
import { addRemoveClass, classListSelector } from './helpers.js';

const SingleForm = function (formEl, buttonEl) { // HTMLElement, HTMLElement
    this.button = buttonEl;
    this.config = GLSR.validationconfig;
    this.events = {
        submit: this.onSubmit_.bind(this),
    };
    this.form = formEl;
    this.isActive = false;
    this.recaptcha = new Recaptcha(this);
    this.stars = null;
    this.strings = GLSR.validationstrings;
    this.useAjax = this.isAjaxEnabled_();
    this.validation = new Validation(formEl);
};

SingleForm.prototype = {
    /** @return void */
    destroy: function () {
        this.destroyForm();
        this.destroyRecaptcha();
        this.destroyStarRatings();
        this.isActive = false;
    },

    /** @return void */
    destroyForm: function () {
        this.form.removeEventListener('submit', this.events.submit);
        this.resetErrors_();
    },

    /** @return void */
    destroyRecaptcha: function () {
        this.recaptcha.destroy_();
    },

    /** @return void */
    destroyStarRatings: function () {
        if (this.stars) {
            this.stars.destroy();
        }
    },

    /** @return void */
    init: function () {
        if (this.isActive) return;
        this.initForm();
        this.initStarRatings();
        this.initRecaptcha();
        this.isActive = true;
    },

    /** @return void */
    initForm: function () {
        this.destroyForm();
        this.form.addEventListener('submit', this.events.submit);
    },

    /** @return void */
    initRecaptcha: function () {
        this.recaptcha.render_();
    },

    /** @return void */
    initStarRatings: function () {
        if (null !== this.stars) {
            this.stars.rebuild();
        } else {
            this.stars = new StarRating(this.form.querySelectorAll('.glsr-field-rating select'), GLSR.stars);
        }
    },

    /** @return void */
    disableButton_: function () {
        this.button.setAttribute('disabled', '');
    },

    /** @return void */
    enableButton_: function () {
        this.button.removeAttribute('disabled');
    },

    /** @return void */
    handleResponse_: function (response, success) { // object
        var wasSuccessful = success === true;
        if (response.recaptcha === 'unset') {
            this.recaptcha.execute_();
            return;
        }
        if (response.recaptcha === 'reset') {
            this.recaptcha.reset_();
        }
        if (wasSuccessful) {
            this.recaptcha.reset_();
            this.form.reset();
        }
        this.showFieldErrors_(response.errors);
        this.showResults_(response.message, wasSuccessful);
        this.enableButton_();
        GLSR.Event.trigger('site-reviews/form/handle', response, this.form);
        response.form = this.form; // @compat
        document.dispatchEvent(new CustomEvent('site-reviews/after/submission', { detail: response })); // @compat
        if (wasSuccessful && response.redirect !== '') {
            window.location = response.redirect;
        }
    },

    /** @return bool */
    isAjaxEnabled_: function () {
        var isUploadSupported = true;
        [].forEach.call(this.form.elements, function (el) {
            if (el.type !== 'file') return;
            isUploadSupported = GLSR.ajax.isFileSupported() && GLSR.ajax.isUploadSupported();
        });
        return isUploadSupported && !this.form.classList.contains('no-ajax');
    },

    /** @return void */
    onSubmit_: function (ev) { // HTMLEvent
        if (!this.validation.validate_()) {
            ev.preventDefault();
            this.showResults_(this.strings.errors, false);
            return;
        }
        this.resetErrors_();
        if (!this.form['g-recaptcha-response'] || this.form['g-recaptcha-response'].value !== '') {
            if (!this.useAjax) return;
        }
        ev.preventDefault();
        this.submitForm_();
    },

    /** @return void */
    resetErrors_: function () {
        addRemoveClass(this.form, this.config.form_error, false);
        this.showResults_('', null);
        this.validation.reset_();
    },

    /** @return void - displays field errors from the server response */
    showFieldErrors_: function (errors) { // object
        if (!errors) return;
        for (var error in errors) {
            if (!errors.hasOwnProperty(error)) continue;
            var nameSelector = GLSR.nameprefix ? GLSR.nameprefix + '[' + error + ']' : error;
            var inputEl = this.form.querySelector('[name="' + nameSelector + '"]');
            if (inputEl) {
                this.validation.setErrors_(inputEl, errors[error]);
                this.validation.toggleError_(inputEl.validation, 'add');
            }
        }
    },

    /** @return void */
    showResults_: function (message, success) { // object, bool
        var resultsEl = this.form.querySelector(classListSelector(this.config.form_message));
        if (resultsEl !== null) {
            addRemoveClass(this.form, this.config.form_error, false === success);
            addRemoveClass(resultsEl, this.config.form_message_failed, false === success);
            addRemoveClass(resultsEl, this.config.form_message_success, true === success);
            resultsEl.innerHTML = message;
        }
    },

    /** @return void */
    submitForm_: function (counter) { // int|null
        if (!GLSR.ajax.isFormDataSupported()) {
            this.showResults_(this.strings.unsupported, false);
            return;
        }
        this.disableButton_();
        this.form[GLSR.nameprefix + '[_counter]'].value = counter || 0;
        GLSR.ajax.post(this.form, this.handleResponse_.bind(this));
    },
};

const Forms = function () {
    while (GLSR.forms.length) {
        var form = GLSR.forms.shift();
        form.destroy();
    }
    var form, forms, submitButton;
    forms = document.querySelectorAll('form.glsr-review-form');
    for (var i = 0; i < forms.length; i++) {
        submitButton = forms[i].querySelector('[type=submit]');
        if (!submitButton) continue;
        form = new SingleForm(forms[i], submitButton);
        form.init();
        GLSR.forms.push(form);
    }
};

export default Forms;
