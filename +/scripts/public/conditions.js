import { classListSelector, parseJson } from '@/public/helpers.js';

const checks = {
    contains: (value, conditionVal) => {
        return value.includes(conditionVal)
    },
    equals: (value, conditionVal) => {
        if (Array.isArray(value)) {
            let values = conditionVal.split(/\s*(?:,|$)\s*/);
            return value.sort().toString() === values.sort().toString()
        }
        if (isNumber(conditionVal)) {
            return intval(value) === +conditionVal
        }
        return value === conditionVal
    },
    greater: (value, conditionVal) => {
        return isNumber(conditionVal) ? intval(value) > +conditionVal : false
    },
    less: (value, conditionVal) => {
        return isNumber(conditionVal) ? intval(value) < +conditionVal : false
    },
    not: (value, conditionVal) => {
        return !checks.equals(value, conditionVal)
    },
};

const fieldtype = (el) => String(el.getAttribute('type') || el.nodeName).toLowerCase();

const intval = (value) => isNaN(value) ? value.length : +value;

const isNumber = (value) => !isNaN(parseInt(value));

class Conditions {
    constructor (Form) {
        this.config = GLSR.validationconfig;
        this.elements = Array.from(Form.form.elements);
        this.event = this.onChange.bind(this);
        this.Form = Form;
    }

    init () {
        console.log('conditions:init');
        this.eventListeners('add')
        this.elements.forEach(el => (el.conditions = {
            criteria: 'always',
            observes: [], // verb
            triggers: [], // verb
        }));
        this._setConditionObserves()
        this._setConditionTriggers()
    }

    destroy () {
        console.log('conditions:destroy');
        this.eventListeners('remove')
        this.elements.forEach(el => (delete el.conditions));
    }

    eventListeners (action) {
        this.elements.forEach(el => el[action+'EventListener'](this.eventName(el), this.event))
    }

    eventName (el) {
        return ['radio', 'checkbox', 'select'].includes(fieldtype(el)) ? 'change' : 'input';
    }

    onChange (ev) {
        const triggerEl = ev.currentTarget;
        triggerEl.conditions.triggers.forEach(el => {
            let results = [];
            el.conditions.observes.forEach(condition => results.push(this.test(condition)))
            results = results.filter(v => v); // remove empty results
            const isAll = results.length === el.conditions.observes.length;
            const isAny = results.length && 'any' === el.conditions.criteria;
            const field = el.closest(classListSelector(this.config.field));
            if (isAll || isAny) {
                field.classList.remove(this.config.field_hidden);
                this.Form.validation.initField(el) // add validation to the field
            } else {
                if (el.validation) {
                    this.Form.validation.destroyField(el.validation) // remove validation from the hidden field
                }
                this.resetValue(el);
                field.classList.add(this.config.field_hidden)
            }
        })
    }

    resetValue (el) {
        const type = fieldtype(el);
        if ('select' === type) {
            Array.from(el.options).forEach(o => (o.selected = o.defaultSelected))
        } else if (['checkbox', 'radio'].includes(type)) {
            let elements = this.Form.form.elements[el.name];
            Array.from(elements.length ? elements : [elements]).forEach(e => (e.checked = e.defaultChecked))
        } else {
            el.value = el.defaultValue || '';
        }
        el.dispatchEvent(new Event(this.eventName(el))) // trigger the input/change event
    }

    test (condition) {
        if (checks.hasOwnProperty(condition.operator)) {
            return checks[condition.operator](this.value(condition.el), condition.value)
        }
        return true
    }

    value (el) {
        const name = el.getAttribute('name');
        const type = fieldtype(el);
        const elements = this.Form.form.elements[name];
        if ('radio' === type) {
            return elements.value
        }
        if (!['checkbox', 'select'].includes(type)) {
            return new String(el.value)
        }
        return Array.from(elements.length ? elements : [elements])
            .filter(el => el['checkbox' === type ? 'checked' : 'selected'])
            .map(el => el.value)
    }

    _setConditionObserves () {
        this.elements.filter(el => el.dataset.conditions).forEach(el => {
            let [err, data] = parseJson(el.dataset.conditions);
            if (null !== err || !data?.conditions?.length) return;
            data.conditions.forEach(field => {
                const observedEl = this.elements.filter(el => el.closest(`[data-field="${field.name}"]`)).shift();
                if (!observedEl) return;
                // @todo check for existing observedEl?
                el.conditions.observes.push({ el: observedEl, ...field });
            })
            if (el.conditions.observes.length) {
                el.conditions.criteria = data.criteria;
            }
        })
    }

    _setConditionTriggers () {
        this.elements.forEach(el => {
            this.elements.filter(el => el.dataset.conditions).forEach(triggerEl => {
                triggerEl.conditions.observes.forEach(field => {
                    if (field.el.getAttribute('name') !== el.getAttribute('name')) return;
                    // @todo check for existing triggerEl?
                    el.conditions.triggers.push(triggerEl)
                })
            })
        })
    }
}

export default Conditions;
