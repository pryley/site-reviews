/** global: FormData, GLSR, HTMLFormElement, StarRating */
/* jshint -W014 */
/* jshint -W030 */
/* jshint -W093 */

// Inspired by https://github.com/sha256/Pristine/

import { addRemoveClass, classListSelector } from './helpers.js';

const countGroupedElements = inputEl => {
    let selector = 'input[name="' + inputEl.getAttribute('name') + '"]:checked';
    return inputEl.validation.form.querySelectorAll(selector).length;
};

const validators = {
    email: {
        fn: function fn(val) {
            return !val || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
        },
    },
    max: {
        fn: function fn(val, limit) {
            return !val || (this.type === 'checkbox'
                ? countGroupedElements(this) <= parseInt(limit)
                : parseFloat(val) <= parseFloat(limit)
           );
        },
    },
    maxlength: {
        fn: function fn(val, length) {
            return !val || val.length <= parseInt(length);
        },
    },
    min: {
        fn: function fn(val, limit) {
            return !val || (this.type === 'checkbox'
                ? countGroupedElements(this) >= parseInt(limit)
                : parseFloat(val) >= parseFloat(limit)
           );
        },
    },
    minlength: {
        fn: function fn(val, length) {
            return !val || val.length >= parseInt(length);
        },
    },
    number: {
        fn: function fn(val) {
            return !val || !isNaN(parseFloat(val));
        },
        priority: 2,
    },
    pattern: {
        fn: function fn(val, pattern) {
            let m = pattern.match(new RegExp('^/(.*?)/([gimy]*)$'));
            return !val || (new RegExp(m[1], m[2])).test(val);
        },
    },
    required: {
        fn: function fn(val) {
            return this.type === 'radio' || this.type === 'checkbox'
                ? countGroupedElements(this)
                : val !== undefined && val !== '';
        },
        priority: 99,
        halt: true,
    },
    tel: {
        fn: function fn(val) {
            let digits = val.replace(/[^0-9]/g, '').length;
            let hasValidLength = 4 <= digits && 15 >= digits;
            return !val || (hasValidLength && (new RegExp("^[+]?[\\d\\s()-]*$")).test(val));
        },
    },
    url: {
        fn: function fn(val) {
            let protocol = '(https?)://';
            let domain = '([\\p{L}\\p{N}\\p{S}\\-_.])+(.?([\\p{L}\\p{N}]|xn--[\\p{L}\\p{N}\\-]+)+.?)';
            let port = '(:[0-9]+)?';
            let path = '(?:/(?:[\\p{L}\\p{N}\\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*';
            let query = '(?:\\?(?:[\\p{L}\\p{N}\\-._~!$&\'\\[\\]()*+,;=:@/?]|%[0-9A-Fa-f]{2})*)?';
            let fragment = '(?:#(?:[\\p{L}\\p{N}\\-._~!$&\'()*+,;=:@/?]|%[0-9A-Fa-f]{2})*)?';
            return !val || (new RegExp('^'+protocol+domain+port+path+query+fragment+'$', 'iu')).test(val);
        }
    },
};

const Validation = function (formEl) { // HTMLElement
    this.config = GLSR.validationconfig;
    this.fields = [];
    this.form = formEl;
    this.form.setAttribute('novalidate', '');
    this.strings = GLSR.validationstrings;
    this.validateEvent = this.onChange_.bind(this);
};

Validation.prototype = {
    ALLOWED_ATTRIBUTES_: ['required', 'max', 'maxlength', 'min', 'minlength', 'pattern'],
    SELECTOR_: 'input:not([type^=hidden]):not([type^=submit]), select, textarea, [data-glsr-validate]',

    destroy: function () {
        this.reset_();
        while (this.fields.length) {
            var field = this.fields.shift();
            this.removeEvent_(field.input);
            delete field.input.validation;
        }
    },

    init: function () {
        [].forEach.call(this.form.querySelectorAll(this.SELECTOR_), field => {
            if (this.fields.find(item => item.input.name === field.name && !field.name.endsWith('[]'))) return;
            let fieldEl = field.closest(classListSelector(this.config.field));
            if ('none' !== fieldEl.style.display) {
                this.fields.push(this.initField_(field));
            }
        });
    },

    addEvent_: function (input) {
        input.addEventListener(this.getEventName_(input), this.validateEvent);
    },

    addValidators_: function (attributes, fns, params) {
        [].forEach.call(attributes, function (attr) {
            let name = attr.name.replace('data-', ''); // using data-* attributes we can simulate the requirement without the HTML5 restriction
            if (~this.ALLOWED_ATTRIBUTES_.indexOf(name)) {
                this.addValidatorToField_(fns, params, name, attr.value);
            }
            else if (attr.name === 'type') {
                this.addValidatorToField_(fns, params, attr.value);
            }
        }.bind(this));
    },

    addValidatorToField_: function (fns, params, name, value) {
        if (!validators[name]) return;
        validators[name].name = name;
        fns.push(validators[name]);
        if (value) {
            var valueParams = (name === 'pattern' ? [value]: value.split(','));
            valueParams.unshift(null); // placeholder for input value
            params[name] = valueParams;
        }
    },

    onChange_: function (ev) {
        this.validate_(ev.currentTarget)
    },

    removeEvent_: function (input) {
        input.removeEventListener(this.getEventName_(input), this.validateEvent);
    },

    reset_: function () {
        for (var i in this.fields) { // remove input error classes
            if (!this.fields.hasOwnProperty(i)) continue;
            this.fields[i].errorElements = null;
            let field = this.fields[i].input.closest(classListSelector(this.config.field));
            addRemoveClass(this.fields[i].input, this.config.input_error, false);
            addRemoveClass(this.fields[i].input, this.config.input_valid, false);
            addRemoveClass(field, this.config.field_error, false);
            addRemoveClass(field, this.config.field_valid, false);
        }
    },

    getEventName_: function (input) {
        return ~['radio', 'checkbox'].indexOf(input.getAttribute('type')) || input.nodeName === 'SELECT'
            ? 'change'
            : 'input';
    },

    initField_: function (inputEl) {
        var params = {};
        var rules = [];
        if (null !== inputEl.offsetParent) { // is inputEl visible?
            this.addValidators_(inputEl.attributes, rules, params);
            this.sortValidators_(rules);
            this.addEvent_(inputEl);
        }
        return inputEl.validation = {
            form: this.form,
            input: inputEl,
            params: params,
            validate: () => this.validate_(inputEl),
            validators: rules,
        };
    },

    toggleError_: function (field, isShowingError) {
        let fieldEl = field.input.closest(classListSelector(this.config.field));
        addRemoveClass(field.input, this.config.input_error, isShowingError);
        addRemoveClass(field.input, this.config.input_valid, !isShowingError);
        if (fieldEl) { // field Element
            addRemoveClass(fieldEl, this.config.field_error, isShowingError);
            addRemoveClass(fieldEl, this.config.field_valid, !isShowingError);
            let errorEl = fieldEl.querySelector(classListSelector(this.config.field_message));
            errorEl.innerHTML = (isShowingError ? field.errors.join('<br>') : ''); // because <br> is used in Field.php
            errorEl.style.display = (!isShowingError ? 'none' : '');
        }
    },

    setErrors_: function (inputEl, errors) {
        if (inputEl.hasOwnProperty('validation')) {
            this.initField_(inputEl);
        }
        inputEl.validation.errors = errors;
    },

    sortValidators_: function (fns) {
        fns.sort((a, b) => (b.priority || 1) - (a.priority || 1));
    },

    validate_: function (input) {
        var isValid = true;
        var fields = this.fields;
        if (input instanceof HTMLElement) {
            fields = [input.validation];
        }
        for (var i in fields) {
            if (!fields.hasOwnProperty(i)) continue;
            var field = fields[i];
            if (this.validateField_(field)) {
                this.toggleError_(field, false); // remove error
            }
            else {
                isValid = false;
                this.toggleError_(field, true); // add error
            }
        }
        return isValid;
    },

    validateField_: function (field) {
        var errors = [];
        var isValid = true;
        for (var i in field.validators) {
            if (!field.validators.hasOwnProperty(i)) continue;
            var validator = field.validators[i];
            var params = field.params[validator.name]
                ? field.params[validator.name]
                : [];
            params[0] = field.input.value;
            if (!validator.fn.apply(field.input, params)) {
                isValid = false;
                var error = this.strings[validator.name];
                errors.push(error.replace(/(\%s)/g, params[1]));
                if (validator.halt === true) break;
            }
        }
        field.errors = errors;
        return isValid;
    },
};

export default Validation;
