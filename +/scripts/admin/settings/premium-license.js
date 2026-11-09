/** global: FormData */

import { config } from '@/public/core/index.js';
import BusyButton from '@/admin/shared/busy-button.js';
import Request from '@/public/modules/request/request.js';
import Tooltip from '@/admin/shared/tooltip.js';

/**
 * The License Key row of Settings > General, with the key field's show/hide toggle, and the install
 * link of the premium notice.
 */
export default class PremiumLicense {
    static init () {
        document.addEventListener('click', PremiumLicense.onClick)
        document.addEventListener('keydown', PremiumLicense.onKeydown)
    }

    // mirrors the overlay EDD shows between verifying a key and its connect page
    static connecting (connect) {
        const overlay = document.createElement('div');
        overlay.className = 'glsr-premium-license__connecting';
        const box = document.createElement('p');
        const spinner = document.createElement('span');
        spinner.className = 'spinner is-active';
        box.append(spinner, config.text.premiumConnecting);
        overlay.append(box);
        document.body.append(overlay);
        window.setTimeout(() => PremiumLicense.handOver(connect), 1500)
    }

    static handOver ({ fields, url }) {
        const form = document.createElement('form');
        form.action = url;
        form.method = 'post';
        Object.entries(fields).forEach(([name, value]) => {
            const input = document.createElement('input');
            input.name = name;
            input.type = 'hidden';
            input.value = value;
            form.append(input);
        });
        document.body.append(form);
        form.submit()
    }

    static async install (row, fallbackUrl) {
        try {
            const response = await Request.send({ method: 'POST', path: 'premium/connect' });
            if (response.success) {
                PremiumLicense.handOver(response.data)
                return true; // the page is leaving
            }
            if (row && response.data.html) { // the key is no longer valid
                PremiumLicense.redraw(row, response.data.html)
            } else if (row) {
                PremiumLicense.show(row, response.data)
            } else if (fallbackUrl) {
                window.location.assign(fallbackUrl)
            }
        } catch (error) {
            if (row) {
                PremiumLicense.show(row, { message: error.message })
            } else if (fallbackUrl) {
                window.location.assign(fallbackUrl)
            }
        }
        return false;
    }

    // delegated: the row is redrawn by its routes
    static async onClick (ev) {
        const link = ev.target.closest('a[data-glsr-premium-install]');
        if (link) {
            ev.preventDefault()
            await PremiumLicense.install(null, link.href)
            return;
        }
        const toggle = ev.target.closest('.glsr-setting-field .wp-pwd button');
        if (toggle) {
            PremiumLicense.toggleKey(toggle)
            return;
        }
        const button = ev.target.closest('#glsr-premium-license [data-action]');
        if (!button) return;
        ev.preventDefault()
        PremiumLicense.run(button)
    }

    static onKeydown (ev) {
        if ('Enter' !== ev.key || !ev.target.matches('#glsr-premium-license input')) return;
        ev.preventDefault()
        const verify = ev.target.closest('#glsr-premium-license').querySelector('[data-action="verify"]');
        if (verify) {
            PremiumLicense.run(verify)
        }
    }

    static redraw (row, html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        const replacement = template.content.firstElementChild;
        row.replaceWith(replacement)
        Tooltip.init(replacement)
        document.dispatchEvent(new CustomEvent('site-reviews/premium-license', { detail: replacement }))
        return replacement;
    }

    static async run (button) {
        const action = button.dataset.action;
        const row = button.closest('#glsr-premium-license');
        const state = BusyButton(button);
        state.loading()
        row.querySelector('.glsr-premium-license__notice').hidden = true;
        let request;
        if ('install' === action) {
            if (await PremiumLicense.install(row)) return;
            state.loaded()
            return;
        }
        if ('verify' === action) {
            const body = new FormData();
            body.append('license', row.querySelector('input').value.trim())
            request = { body, method: 'POST', path: 'premium/license' };
        } else {
            request = { method: 'DELETE', params: { delete: 'delete' === action ? 1 : 0 }, path: 'premium/license' };
        }
        try {
            const response = await Request.send(request);
            if (response.success && response.data.html) {
                const redrawn = PremiumLicense.redraw(row, response.data.html);
                if (response.data.connect) {
                    PremiumLicense.connecting(response.data.connect) // the page is leaving
                } else if (response.data.notice) {
                    PremiumLicense.show(redrawn, response.data.notice)
                }
                return;
            }
            if (response.data.html) { // a refusal changes the row's state
                PremiumLicense.redraw(row, response.data.html)
                return;
            }
            PremiumLicense.show(row, response.data)
        } catch (error) {
            PremiumLicense.show(row, { message: error.message })
        }
        state.loaded()
    }

    static show (row, { link, message, type }) {
        const notice = row.querySelector('.glsr-premium-license__notice');
        notice.className = `glsr-premium-license__notice notice notice-${type || 'error'} inline`;
        notice.textContent = '';
        const p = document.createElement('p');
        p.textContent = message || config.text.premiumError;
        if (link?.url) {
            const a = document.createElement('a');
            a.href = link.url;
            a.target = '_blank';
            a.textContent = link.text || link.url;
            p.append(' ', a);
        }
        notice.append(p);
        notice.hidden = false;
    }

    static toggleKey (button) {
        const icon = button.querySelector('.dashicons');
        const label = icon.classList.contains('dashicons-visibility') ? button.dataset.hide : button.dataset.show;
        button.setAttribute('aria-label', label)
        button._tippy?.setContent(label)
        icon.classList.toggle('dashicons-hidden')
        icon.classList.toggle('dashicons-visibility')
    }
}
