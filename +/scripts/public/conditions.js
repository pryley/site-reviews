import { classListSelector, parseJson } from '@/public/helpers.js';

// @todo how do we deal with multi fields when adding observes and triggers?

const intval = (value) => isNaN(value) ? value.length : +value;

const checks = {
    contains: (condition) => {
        const value = new String(condition.el.value);
        return value.includes(condition.value);
    },
    equals: (condition) => {
        if (isNaN(condition.value)) {
            return condition.el.value === condition.value
        }
        return intval(condition.el.value) === intval(condition.value)
    },
    greater: (condition) => {
        return intval(condition.el.value) > intval(condition.value);
    },
    less: (condition) => {
        return intval(condition.el.value) < intval(condition.value);
    },
    not: (condition) => {
        return !checks.equals(condition)
    },
}

class Conditions {
    constructor (Form) {
        this.config = GLSR.validationconfig;
        this.elements = Array.from(Form.form.elements);
        this.event = this.onChange.bind(this);
        this.Form = Form;
    }

    init () {
        this.eventListeners('add')
        this.elements.forEach(el => (el.conditions = {
            criteria: 'always',
            observes: [],
            triggers: [],
        }));
        this._setConditionObserves()
        this._setConditionTriggers()
    }

    destroy () {
        this.eventListeners('remove')
        this.elements.forEach(el => (delete el.conditions));
    }

    eventListeners (action) {
        this.elements.forEach(el => el[action+'EventListener'](this.eventName(el), this.event))
    }

    eventName (el) {
        const type = el.getAttribute('type') || el.nodeName;
        return !!~['radio', 'checkbox', 'SELECT'].indexOf(type) ? 'change' : 'input';
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
                el.value = '';
                el.dispatchEvent(new Event(this.eventName(el))) // trigger the input/change event
                field.classList.add(this.config.field_hidden)
            }
        })
    }

    test (condition) {
        if (checks.hasOwnProperty(condition.operator)) {
            return checks[condition.operator](condition)
        }
        return true
    }

    _setConditionObserves () {
        this.elements.filter(el => el.dataset.conditions)
            .forEach(el => {
                let [err, data] = parseJson(el.dataset.conditions);
                if (null !== err || !data?.conditions?.length) return;
                data.conditions.forEach(field => {
                    const observedEl = this.elements.filter(el => el.closest(`[data-field="${field.name}"]`)).shift();
                    if (!observedEl) return;
                    // @todo check for existing observedEl...
                    el.conditions.observes.push({ el: observedEl, ...field });
                })
                if (el.conditions.observes.length) {
                    el.conditions.criteria = data.criteria;
                }
            })
    }

    _setConditionTriggers () {
        this.elements.forEach(el => { // rating
            this.elements.filter(el => el.dataset.conditions).forEach(triggeredEl => { // xxx
                triggeredEl.conditions.observes.forEach(field => {
                    if (field.el !== el) return;
                    // @todo check for existing triggerEl...
                    el.conditions.triggers.push(triggeredEl)
                })
            })
        })
    }
}

export default Conditions;
