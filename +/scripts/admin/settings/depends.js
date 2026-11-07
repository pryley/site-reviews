/**
 * A setting that depends on others (its data-depends attribute names them and their values) shows
 * while they hold those values and hides otherwise.
 */
const normalize = (value) => {
    if (['true', 'on', 'yes', '1'].includes(value)) return true;
    if (['false', 'off', 'no', '0'].includes(value)) return false;
    return value;
};

export default class Depends {
    static init (root = document) {
        root.querySelectorAll('form.glsr-form').forEach(form => {
            if (form.querySelector('[data-depends]')) {
                new Depends(form)
            }
        })
    }

    constructor (form) {
        this.form = form;
        this.dependents = [...form.querySelectorAll('[data-depends]')];
        [...form.elements].forEach(control => {
            if (['INPUT', 'SELECT'].includes(control.nodeName)) {
                control.addEventListener('change', this.onChange.bind(this))
            }
        })
    }

    dependencies (el) {
        try {
            return JSON.parse(el.dataset.depends || '[]');
        } catch (error) {
            console.error(error)
            return [];
        }
    }

    // whether the control holds one of the values
    holds (name, values) {
        const wanted = [].concat(values).map(normalize);
        return [...this.form.querySelectorAll(`[name="${name}"]`)].some(control => {
            const has = wanted.includes(normalize(control.value));
            return ['checkbox', 'radio'].includes(control.type) ? control.checked && has : has;
        });
    }

    onChange (ev) {
        this.dependents.forEach(el => {
            const dependencies = this.dependencies(el);
            if (!dependencies.some(dependency => dependency.name === ev.currentTarget.name)) return;
            const shown = dependencies.every(dependency => this.holds(dependency.name, dependency.value));
            el.closest('.glsr-setting-field')?.classList.toggle('hidden', !shown)
        })
    }
}
