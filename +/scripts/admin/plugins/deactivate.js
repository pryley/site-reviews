/** global: _glsr_deactivate */

/**
 * The deactivation dialog of the Plugins screen: a <dialog> drawn from the page's <template> for
 * the plugin whose Deactivate link was clicked, asking why, with the feedback posted to
 * plugin/feedback before the browser deactivates.
 */
import Request from '@/public/modules/request/request.js';

const HELP = {
    info: ['feature-missing'], // the reasons each help notice answers
    warning: ['confused', 'looking-for-different', 'not-working'],
};

class Deactivate {
    static init (root = document) {
        const list = root.querySelector('#the-list');
        const mount = root.querySelector('#glsr-dp-overlay');
        const template = root.querySelector('#glsr-deactivate-dialog');
        if (list && mount && template && 'object' === typeof _glsr_deactivate) {
            new Deactivate(list, mount, template, _glsr_deactivate)
        }
    }

    constructor (list, mount, template, data) {
        this.data = data;
        this.dialog = null;
        this.mount = mount;
        this.target = null;
        this.template = template;
        list.addEventListener('click', this.onDeactivate.bind(this))
    }

    close () {
        if (!this.dialog) return;
        document.body.classList.remove('modal-open')
        this.dialog.close()
        this.dialog.remove()
        this.dialog = null;
        this.target?.focus()
    }

    // the dialog for one plugin, from the template
    draw (plugin, href) {
        const dialog = this.template.content.firstElementChild.cloneNode(true);
        const title = dialog.querySelector('h3');
        title.textContent = title.dataset.title.replace('%s', plugin.name);
        dialog.querySelector('a.deactivate').href = href;
        const reasons = dialog.querySelector('.glsr-dp-reasons');
        const reason = reasons.querySelector('template');
        this.data.reasons.forEach(({ icon, id, placeholder, text }) => {
            const item = reason.content.firstElementChild.cloneNode(true);
            const label = item.querySelector('label');
            label.dataset.placeholder = placeholder;
            label.querySelector('input').value = id;
            label.querySelector('.glsr-dp-icon').innerHTML = icon;
            label.querySelector('.glsr-dp-reason').textContent = text;
            reasons.append(item)
        })
        reason.remove()
        const rows = dialog.querySelector('.glsr-dp-info tbody');
        Object.entries(this.data.insight).forEach(([label, value]) => {
            const row = document.createElement('tr');
            [label, value].forEach(text => {
                const cell = document.createElement('td');
                cell.textContent = text;
                row.append(cell)
            })
            rows.append(row)
        })
        dialog.addEventListener('cancel', ev => { // the Escape key
            ev.preventDefault()
            this.close()
        })
        dialog.addEventListener('click', ev => this.onClick(ev, plugin, href))
        return dialog;
    }

    onClick (ev, plugin, href) {
        const { target } = ev;
        if (target === this.dialog || target.closest('button.close')) { // the backdrop, or the close button
            this.close()
        } else if (target.closest('.expand-info')) {
            const info = this.dialog.querySelector('#glsr-dp-info');
            info.hidden = !info.hidden;
            target.closest('.expand-info').setAttribute('aria-expanded', String(!info.hidden))
        } else if (target.closest('label.glsr-dp-reason')) {
            this.select(target.closest('label.glsr-dp-reason'))
        } else if (target.closest('button.submit')) {
            ev.preventDefault()
            this.submit(target.closest('button.submit'), plugin, href)
        }
    }

    onDeactivate (ev) {
        const link = ev.target.closest('a[data-deactivate]');
        if (!link) return;
        const plugin = this.data.plugins.find(({ slug }) => slug === link.dataset.deactivate);
        if (!plugin) return;
        ev.preventDefault()
        this.target = link;
        this.dialog = this.draw(plugin, link.href);
        this.mount.replaceChildren(this.dialog)
        document.body.classList.add('modal-open')
        this.dialog.showModal()
    }

    select (label) {
        const { placeholder } = label.dataset;
        const value = label.querySelector('input').value;
        const details = this.dialog.querySelector('.glsr-dp-details');
        const help = this.dialog.querySelector('.glsr-dp-help');
        this.dialog.querySelectorAll('label.glsr-dp-reason').forEach(el => el.classList.toggle('is-selected', el === label))
        details.querySelector('textarea').placeholder = placeholder;
        details.hidden = '' === placeholder;
        const info = HELP.info.includes(value);
        const warning = HELP.warning.includes(value);
        help.hidden = !info && !warning;
        help.querySelectorAll('.is-info').forEach(el => (el.hidden = !info))
        help.querySelectorAll('.is-warning').forEach(el => (el.hidden = !warning))
    }

    async submit (button, plugin, href) {
        const reason = this.dialog.querySelector('input[name="reason"]:checked')?.value || '';
        const details = this.dialog.querySelector('textarea[name="details"]')?.value || '';
        button.classList.add('is-busy')
        button.disabled = true;
        button.textContent = button.dataset.processing;
        await Request.send({
            body: { details, reason, slug: plugin.slug, version: plugin.version },
            method: 'POST',
            path: 'plugin/feedback',
        })
        window.location.href = href;
    }
}

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', () => Deactivate.init(), { once: true })
} else {
    Deactivate.init()
}
