/** global: wp */

import { config } from '@/public/core/index.js';
import BusyButton from '@/admin/shared/busy-button.js';
import Notice from '@/admin/shared/notice.js';
import Request from '@/public/modules/request/request.js';

/**
 * The Rollback Plugin tool: a dialog confirms the version, the route stores it and answers with
 * WordPress's update request, which is posted to admin-ajax as WordPress's updater would.
 */
export default class Rollback {
    static init (root = document) {
        const form = root.querySelector('#rollback-plugin');
        const dialog = root.querySelector('#rollback-dialog');
        if (form && dialog) {
            new Rollback(form, dialog)
        }
    }

    constructor (form, dialog) {
        this.button = form.querySelector('button');
        this.dialog = dialog;
        this.form = form;
        this.version = form.elements.namedItem('version');
        this.onKeydown = this.onKeydown.bind(this);
        form.addEventListener('submit', ev => {
            ev.preventDefault()
            this.open()
        })
        dialog.addEventListener('click', ev => {
            if (ev.target.closest('[data-cancel], .notification-dialog-background')) {
                this.close(this.button)
            } else if (ev.target.closest('[data-confirm]')) {
                this.close(this.version) // the busy button cannot hold the focus
                this.rollback()
            }
        })
    }

    close (focusEl) {
        document.removeEventListener('keydown', this.onKeydown)
        this.dialog.classList.add('hidden')
        document.body.classList.remove('modal-open')
        document.getElementById('wpwrap')?.removeAttribute('aria-hidden')
        focusEl.focus()
    }

    onKeydown (ev) {
        if ('Escape' === ev.key) {
            this.close(this.button)
        } else if ('Tab' === ev.key) { // Safari does not move the focus to a button with the Tab key
            const buttons = [...this.dialog.querySelectorAll('button')];
            const index = buttons.indexOf(ev.target);
            const next = ev.shiftKey ? (index < 1 ? buttons.length : index) - 1 : (index + 1) % buttons.length;
            buttons[next].focus()
            ev.preventDefault()
        }
    }

    open () {
        const option = this.version.selectedOptions[0];
        const names = JSON.parse(option.dataset.stops || '[]');
        const warning = this.dialog.querySelector('[data-warning]');
        this.dialog.querySelectorAll('[data-version]').forEach(el => (el.textContent = this.version.value))
        warning.querySelector('ul').replaceChildren(...names.map(name => {
            const item = document.createElement('li');
            item.textContent = name;
            return item;
        }))
        warning.classList.toggle('hidden', 0 === names.length)
        warning.querySelector('[data-premium]').classList.toggle('hidden', !option.dataset.premium)
        document.getElementById('wpwrap')?.setAttribute('aria-hidden', 'true')
        document.body.classList.add('modal-open')
        document.body.append(this.dialog) // outside #wpwrap, which is hidden from screen readers while the dialog is open
        this.dialog.classList.remove('hidden')
        this.dialog.querySelector('[data-cancel]').focus()
        document.addEventListener('keydown', this.onKeydown)
    }

    async rollback () {
        const version = this.version.value;
        const state = BusyButton(this.button);
        this.loading ??= this.button.dataset.loading || ''; // the %s template, kept for the next attempt
        this.button.dataset.loading = this.loading.replace('%s', `v${version}`);
        state.loading()
        const { data, success } = await Request.send({ body: { version }, route: 'rollback' });
        if (!success) {
            state.loaded()
            Notice.error(data?.message || config.text.rollbackError)
            return;
        }
        try {
            const response = await fetch(config.request.ajax.url, {
                body: new URLSearchParams(data.data),
                credentials: 'same-origin',
                method: 'POST',
            });
            const json = await response.json();
            if (json.success) {
                window.location = data.url;
                return;
            }
            this.showError(json.data?.errorMessage || json.data?.message || response.statusText)
        } catch (error) {
            this.showError(error.message)
        }
        state.loaded()
    }

    showError (error) {
        const message = ('string' === typeof error ? error : '').replace(/<[\/a-z][^<>]*>/gi, '');
        Notice.error(`${config.text.rollbackError}: ${message || wp?.i18n?.__('An error has occurred. Please reload the page and try again.') || ''}`)
    }
}
