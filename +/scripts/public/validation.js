// Inspired by https://github.com/sha256/Pristine/

import { addRemoveClass, classListSelector } from '@/public/helpers.js';

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

const allowedAttributes = [
    'required', 'max', 'maxlength', 'min', 'minlength', 'pattern',
];
const selector = 'input:not([type^=hidden]):not([type^=submit]), select, textarea, [data-glsr-validate]';

class Validation {
    constructor (formEl) {
        this.config = GLSR.validationconfig;
        this.fields = [];
        this.form = formEl;
        this.form.setAttribute('novalidate', '');
        this.strings = GLSR.validationstrings;
        this.validateEvent = this._onChange.bind(this);
    }

    destroy () {
        this.reset();
        while (this.fields.length) {
            const field = this.fields.shift();
            this._removeEvent(field.input)
            delete field.input.validation;
        }
    }

    init () {
        this.form.querySelectorAll(selector).forEach(field => {
            if (this.fields.find(item => item.input.name === field.name && !field.name.endsWith('[]'))) return;
            let fieldEl = field.closest(classListSelector(this.config.field));
            if (fieldEl && 'none' !== fieldEl.style.display) {
                this.fields.push(this._initField(field))
            }
        })
    }

    reset () {
        for (let i in this.fields) { // remove input error classes
            if (!this.fields.hasOwnProperty(i)) continue;
            this.fields[i].errorElements = null;
            let field = this.fields[i].input.closest(classListSelector(this.config.field));
            addRemoveClass(this.fields[i].input, this.config.input_error, false)
            addRemoveClass(this.fields[i].input, this.config.input_valid, false)
            addRemoveClass(field, this.config.field_error, false)
            addRemoveClass(field, this.config.field_valid, false)
        }
    }

    setErrors (inputEl, errors) {
        if (inputEl.hasOwnProperty('validation')) {
            this._initField(inputEl)
        }
        inputEl.validation.errors = errors;
    }

    toggleError (field, isShowingError) {
        let fieldEl = field.input.closest(classListSelector(this.config.field));
        addRemoveClass(field.input, this.config.input_error, isShowingError)
        addRemoveClass(field.input, this.config.input_valid, !isShowingError)
        if (fieldEl) {
            addRemoveClass(fieldEl, this.config.field_error, isShowingError)
            addRemoveClass(fieldEl, this.config.field_valid, !isShowingError)
            let errorEl = fieldEl.querySelector(classListSelector(this.config.field_message));
            if (errorEl) {
                errorEl.innerHTML = (isShowingError ? field.errors.join('<br>') : ''); // because <br> is used in Field.php
                errorEl.style.display = (!isShowingError ? 'none' : '');
            }
        }
    }

    validate (inputEl) {
        let isValid = true;
        let fields = this.fields;
        if (inputEl instanceof HTMLElement) {
            fields = [inputEl.validation];
        }
        for (let i in fields) {
            if (!fields.hasOwnProperty(i)) continue;
            const field = fields[i];
            if (this._validateField(field)) {
                this.toggleError(field, false) // remove error
            }
            else {
                isValid = false;
                this.toggleError(field, true) // add error
            }
        }
        return isValid;
    }

    _addEvent (inputEl) {
        inputEl.addEventListener(this._getEventName(inputEl), this.validateEvent)
    }

    _addValidators (attributes, fns, params) {
        [].forEach.call(attributes, attr => {
            let name = attr.name.replace('data-', ''); // using data-* attributes we can simulate the requirement without the HTML5 restriction
            if (~allowedAttributes.indexOf(name)) {
                this._addValidatorToField(fns, params, name, attr.value)
            }
            else if (attr.name === 'type') {
                this._addValidatorToField(fns, params, attr.value)
            }
        })
    }

    _addValidatorToField (fns, params, name, value) {
        if (!validators[name]) return;
        validators[name].name = name;
        fns.push(validators[name])
        if (value) {
            var valueParams = (name === 'pattern' ? [value] : value.split(','));
            valueParams.unshift(null) // placeholder for input value
            params[name] = valueParams;
        }
    }

    _onChange (ev) {
        this.validate(ev.currentTarget)
    }

    _removeEvent (inputEl) {
        inputEl.removeEventListener(this._getEventName(inputEl), this.validateEvent)
    }

    _getEventName (inputEl) {
        return ~['radio', 'checkbox'].indexOf(inputEl.getAttribute('type')) || inputEl.nodeName === 'SELECT'
            ? 'change'
            : 'input';
    }

    _initField (inputEl) {
        let params = {};
        let validators = [];
        if (null !== inputEl.offsetParent) { // is inputEl visible?
            this._addValidators(inputEl.attributes, validators, params)
            this._sortValidators(validators)
            this._addEvent(inputEl)
        }
        return inputEl.validation = {
            form: this.form,
            input: inputEl,
            params,
            validate: () => this.validate(inputEl),
            validators,
        };
    }

    _sortValidators (fns) {
        fns.sort((a, b) => (b.priority || 1) - (a.priority || 1))
    }

    _validateField (field) {
        let errors = [];
        let isValid = true;
        for (let i in field.validators) {
            if (!field.validators.hasOwnProperty(i)) continue;
            let validator = field.validators[i];
            let params = field.params[validator.name]
                ? field.params[validator.name]
                : [];
            params[0] = field.input.value;
            if (!validator.fn.apply(field.input, params)) {
                isValid = false;
                let error = this.strings[validator.name];
                errors.push(error.replace(/(\%s)/g, params[1]))
                if (validator.halt === true) break;
            }
        }
        field.errors = errors;
        return isValid;
    }
}

export default Validation;
