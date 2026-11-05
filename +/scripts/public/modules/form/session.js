/**
 * Inspired by https://jpederson.com/Squirrel.js/
 */

const storeKey = (el) => 'checkbox' === el.type ? (el.name + el.value) : el.name;

const event = (ev) => sessionStorage.setItem(storeKey(ev.target), ev.target.value);

const forget = (el) => el.removeEventListener('change', event);

const listen = (el) => el.addEventListener('change', event);

const restore = (el) => {
    const value = sessionStorage.getItem(storeKey(el));
    if (null === value) return;
    if (['checkbox','radio'].includes(el.type)) {
        el.checked = (el.value === value);
    } else {
        el.value = value;
    }
    el.dispatchEvent(new Event('change'))
};

class Session {
    constructor (formEl) {
        this.fields = [];
        if (!formEl.classList.contains('glsr-persist-data')) return
        [...formEl.elements].forEach(el => {
            if (el.disabled || el.readonly
                || ['file','hidden','password','submit'].includes(el.type)
                || !['input','select','textarea'].includes(el.localName)) {
                return;
            }
            this.fields.push(el)
        })
    }

    clear () {
        if (this.fields.length) {
            sessionStorage.clear()
        }
    }

    destroy () {
        this.fields.forEach(el => forget(el))
    }

    init () {
        this.fields.forEach(el => {
            restore(el)
            listen(el)
        })
    }
}

export default Session;
