/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */
/* jshint -W014 */
/* jshint -W030 */
/* jshint -W093 */

import { classListAddRemove, classListSelector } from './classlist.js';

let countGroupedElements = inputEl => {
    let selector = 'input[name="' + inputEl.getAttribute('name') + '"]:checked';
    return inputEl.validation.form.querySelectorAll(selector).length;
}

let validators = {
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
    required: {
        fn: function fn(val) {
            return this.type === 'radio' || this.type === 'checkbox'
                ? countGroupedElements(this)
                : val !== undefined && val !== '';
        },
        priority: 99,
        halt: true,
    },
};

const Validation = function (formEl) { // HTMLElement
    this.config = GLSR.validationconfig;
    this.fields = [];
    this.form = formEl;
    this.form.setAttribute('novalidate', '');
    this.strings = GLSR.validationstrings;
    this.init();
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

    /** @return void */
    init: function () {
        [].forEach.call(this.form.querySelectorAll(this.SELECTOR_), function (field) {
            if (!this.fields.find(function (item) {
                return item.input.name === field.name;
            })) {
                this.fields.push(this.initField_(field));
            }
        }.bind(this));
    },

    /** @return void */
    addEvent_: function (input) {
        input.addEventListener(this.getEventName_(input), this.validate_.bind(this, input));
    },

    /** @return void */
    addValidators_: function (attributes, fns, params) {
        [].forEach.call(attributes, function (attr) {
            if (~this.ALLOWED_ATTRIBUTES_.indexOf(attr.name)) {
                this.addValidatorToField_(fns, params, attr.name, attr.value);
            }
            else if (attr.name === 'type') {
                this.addValidatorToField_(fns, params, attr.value);
            }
        }.bind(this));
    },

    /** @return void */
    addValidatorToField_: function (fns, params, name, value) {
        if (!validators[name]) return;
        validators[name].name = name;
        fns.push(validators[name]);
        if (value) {
            var valueParams = value.split(',');
            valueParams.unshift(null); // placeholder for input value
            params[name] = valueParams;
        }
    },

    /** @return void */
    removeEvent_: function (input) {
        input.removeEventListener(this.getEventName_(input), this.validate_.bind(this, input));
    },

    /** @return void */
    reset_: function () {
        for (var i in this.fields) {
            if (!this.fields.hasOwnProperty(i)) continue;
            this.fields[i].errorElements = null;
            classListAddRemove(this.fields[i].input, this.config.input_error_class, false);
            classListAddRemove(this.fields[i].input, this.config.input_valid_class, false);
        }
        [].map.call(this.form.querySelectorAll(classListSelector(this.config.error_tag_class)), function (el) {
            classListAddRemove(el.parentNode, this.config.field_error_class, false);
            el.parentNode.removeChild(el);
        }.bind(this));
    },

    /** @return object */
    extend_: function () { // ...object
        var args = [].slice.call(arguments);
        var result = args[0];
        var extenders = args.slice(1);
        Object.keys(extenders).forEach(function (i) {
            for (var key in extenders[i]) {
                if (!extenders[i].hasOwnProperty(key)) continue;
                result[key] = extenders[i][key];
            }
        });
        return result;
    },

    /** @return array */
    getErrorElements_: function (field) {
        if (field.errorElements) {
            return field.errorElements;
        }
        var errorEl;
        var parentEl = field.input.closest(classListSelector(this.config.field_class));
        if (parentEl) {
            errorEl = parentEl.closest(classListSelector(this.config.error_tag_class));
            if (errorEl === null) {
                errorEl = document.createElement(this.config.error_tag);
                errorEl.className = this.config.error_tag_class;
                parentEl.appendChild(errorEl);
            }
        }
        return field.errorElements = [parentEl, errorEl];
    },

    /** @return string */
    getEventName_: function (input) {
        return ~['radio', 'checkbox'].indexOf(input.getAttribute('type')) || input.nodeName === 'SELECT'
            ? 'change'
            : 'input';
    },

    /** @return object */
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
            validators: rules,
        };
    },

    /** @return void */
    toggleError_: function (field, action) {
        var errorEls = this.getErrorElements_(field);
        var isShowingError = action === 'add';
        classListAddRemove(field.input, this.config.input_error_class, isShowingError);
        classListAddRemove(field.input, this.config.input_valid_class, !isShowingError);
        if (errorEls[0]) {
            classListAddRemove(errorEls[0], this.config.field_error_class, isShowingError);
        }
        if (errorEls[1]) {
            errorEls[1].innerHTML = (isShowingError ? field.errors.join('<br>') : '');
            errorEls[1].style.display = (!isShowingError ? 'none' : '');
        }
    },

    /** @return void */
    setErrors_: function (inputEl, errors) {
        if (inputEl.hasOwnProperty('validation')) {
            this.initField_(inputEl);
        }
        inputEl.validation.errors = errors;
    },

    /** @return void */
    sortValidators_: function (fns) {
        fns.sort(function (a, b) {
            return (b.priority || 1) - (a.priority || 1);
        });
    },

    /** @return bool */
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
                this.toggleError_(field, 'remove');
            }
            else {
                isValid = false;
                this.toggleError_(field, 'add');
            }
        }
        return isValid;
    },

    /** @return bool */
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
