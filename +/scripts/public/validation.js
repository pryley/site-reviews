// Inspired by https://github.com/sha256/Pristine/

import { addRemoveClass, classListSelector } from '@/public/helpers.js';

const allowedAttributes = [
    'required', 'max', 'maxlength', 'min', 'minlength', 'pattern',
];

const countGroupedElements = (inputEl) => {
    const name = inputEl.getAttribute('name');
    return inputEl.validation.form.querySelectorAll(`input[name="${name}"]:checked`).length;
};

const selector = 'input:not([type=hidden]):not([type=submit]), select, textarea, [data-glsr-validate]';

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
            if (!!~['checkbox', 'radio'].indexOf(this.type)) {
                return countGroupedElements(this)
            }
            if (val === undefined || val === null) {
                return false;
            }
            return String(val).replace(/\s/g, '').length > 0;
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

class Validation {
    constructor (formEl) {
        this.config = GLSR.validationconfig;
        this.event = this._onChange.bind(this);
        this.fields = [];
        this.form = formEl;
        this.form.setAttribute('novalidate', '');
        this.strings = GLSR.validationstrings;
    }

    destroy () {
        console.info('validation:destroy');
        while (this.fields.length) {
            this.destroyField(this.fields[0])
        }
    }

    destroyField (field) {
        this.resetField(field)
        this._eventListener('remove', field.input)
        delete field.input.validation;
        this.fields = this.fields.filter(f => f.input !== field.input)
    }

    init () {
        console.info('validation:init');
        this.form.querySelectorAll(selector).forEach(inputEl => this.initField(inputEl))
    }

    initField (inputEl) {
        if (this.fields.find(field => field.input.name === inputEl.name && !inputEl.name.endsWith('[]'))) return; // prevent duplicates
        const fieldEl = inputEl.closest(classListSelector(this.config.field));
        if (!fieldEl?.offsetParent) return; // make sure field is visible
        this.fields.push(this._initField(inputEl))
    }

    reset () {
        this.fields.forEach(field => this.resetField(field))
    }

    resetField (field) {
        let fieldEl = field.input.closest(classListSelector(this.config.field));
        addRemoveClass(field.input, this.config.input_error, false)
        addRemoveClass(field.input, this.config.input_valid, false)
        if (fieldEl) {
            addRemoveClass(fieldEl, this.config.field_error, false)
            addRemoveClass(fieldEl, this.config.field_valid, false)
        }
        field.errors = [];
    }

    setErrors (inputEl, errors) {
        if (!inputEl.hasOwnProperty('validation')) {
            this._initField(inputEl)
        }
        inputEl.validation.errors = errors;
    }

    setInvalid (field) {
        this.toggleError(field, true);
    }

    setValid (field) {
        this.toggleError(field, false);
    }

    toggleError (field, hasError) {
        let fieldEl = field.input.closest(classListSelector(this.config.field));
        addRemoveClass(field.input, this.config.input_error, hasError)
        addRemoveClass(field.input, this.config.input_valid, !hasError)
        if (fieldEl) {
            addRemoveClass(fieldEl, this.config.field_error, hasError)
            addRemoveClass(fieldEl, this.config.field_valid, !hasError)
            // display the form's error message
            let errorEl = fieldEl.querySelector(classListSelector(this.config.field_message));
            if (errorEl) {
                errorEl.innerHTML = (hasError ? field.errors.join('<br>') : ''); // because <br> is used in Field.php
                errorEl.style.display = (!hasError ? 'none' : '');
            }
        }
    }

    validate (inputEl) {
        console.info('validation:validate');
        let isValid = true;
        let fields = this.fields;
        if (inputEl instanceof HTMLElement && inputEl.hasOwnProperty('validation')) {
            fields = [inputEl.validation];
        }
        fields.forEach(field => {
            if (this._validateField(field)) {
                this.toggleError(field, false) // remove error
            } else {
                this.toggleError(field, true) // add error
                isValid = false;
            }
        })
        return isValid;
    }

    _addValidators (attributes, fns, params) {
        [].forEach.call(attributes, attr => {
            let name = attr.name.replace('data-', ''); // using data-* attributes we can simulate the requirement without the HTML5 restriction
            if (!!~allowedAttributes.indexOf(name)) {
                this._addValidatorToField(fns, params, name, attr.value)
            }
            else if (attr.name === 'type') {
                this._addValidatorToField(fns, params, attr.value)
            }
        })
    }

    _addValidatorToField (fns, params, name, value) {
        if (!validators.hasOwnProperty(name)) return
        validators[name].name = name;
        fns.push(validators[name])
        if (value) {
            let valueParams = (name === 'pattern' ? [value] : value.split(','));
            valueParams.unshift(null) // placeholder for input value
            params[name] = valueParams;
        }
    }

    _eventListener (action, el) {
        const type = el.getAttribute('type') || el.nodeName;
        const event = !!~['radio', 'checkbox', 'SELECT'].indexOf(type) ? 'change' : 'input';
        el[action+'EventListener'](event, this.event)
    }

    _onChange (ev) {
        this.validate(ev.currentTarget)
    }

    _initField (inputEl) {
        let params = {};
        let validators = [];
        if (null !== inputEl.offsetParent) { // make sure inputEl is visible
            this._addValidators(inputEl.attributes, validators, params)
            this._sortValidators(validators)
            this._eventListener('add', inputEl)
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
