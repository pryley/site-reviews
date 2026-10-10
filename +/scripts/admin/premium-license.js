/** global: GLSR */

import Button from '@/admin/button.js';
import config from '@/public/config.js';
import Request from '@/public/request.js';

/**
 * The License Key row of Settings > General, and the install link of the premium notice.
 */
const PremiumLicense = function () {
    document.addEventListener('click', this.onClick_.bind(this));
    document.addEventListener('keydown', this.onKeydown_.bind(this));
};

PremiumLicense.prototype = {
    // mirrors the overlay EDD shows between verifying a key and its connect page
    connecting_: function (connect) {
        const overlay = document.createElement('div');
        overlay.className = 'glsr-premium-license__connecting';
        const box = document.createElement('p');
        const spinner = document.createElement('span');
        spinner.className = 'spinner is-active';
        box.append(spinner, config.text.premiumConnecting);
        overlay.append(box);
        document.body.append(overlay);
        window.setTimeout(() => this.handOver_(connect), 1500);
    },

    handOver_: function ({ fields, url }) {
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
        form.submit();
    },

    install_: async function (row, fallbackUrl) {
        try {
            const response = await Request.send({ method: 'POST', path: 'premium/connect' });
            if (response.success) {
                this.handOver_(response.data);
                return true; // the page is leaving
            }
            if (row && response.data.html) { // the key is no longer valid: the redrawn row says why
                this.redraw_(row, response.data.html);
            } else if (row) {
                this.show_(row, response.data);
            } else if (fallbackUrl) {
                window.location.assign(fallbackUrl);
            }
        } catch (error) {
            if (row) {
                this.show_(row, { message: error.message });
            } else if (fallbackUrl) {
                window.location.assign(fallbackUrl);
            }
        }
        return false;
    },

    onClick_: async function (ev) {
        const link = ev.target.closest('a[data-glsr-premium-install]');
        if (link) {
            ev.preventDefault();
            await this.install_(null, link.href);
            return;
        }
        const button = ev.target.closest('#glsr-premium-license [data-action]');
        if (!button) return;
        ev.preventDefault();
        this.run_(button);
    },

    onKeydown_: function (ev) {
        if ('Enter' !== ev.key || !ev.target.matches('#glsr-premium-license input')) return;
        ev.preventDefault();
        const verify = ev.target.closest('#glsr-premium-license').querySelector('[data-action="verify"]');
        if (verify) {
            this.run_(verify);
        }
    },

    redraw_: function (row, html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        const replacement = template.content.firstElementChild;
        row.replaceWith(replacement);
        document.dispatchEvent(new CustomEvent('site-reviews/premium-license', { detail: replacement }));
        return replacement;
    },

    // a redrawn row needs no loaded(): the button is gone with it
    run_: async function (button) {
        const action = button.dataset.action;
        const row = button.closest('#glsr-premium-license');
        const state = Button(jQuery(button));
        state.loading();
        row.querySelector('.glsr-premium-license__notice').hidden = true;
        let request;
        if ('install' === action) {
            if (await this.install_(row)) return;
            state.loaded();
            return;
        }
        if ('verify' === action) {
            const body = new FormData();
            body.append('license', row.querySelector('input').value.trim());
            request = { body, method: 'POST', path: 'premium/license' };
        } else {
            request = { method: 'DELETE', params: { delete: 'delete' === action ? 1 : 0 }, path: 'premium/license' };
        }
        try {
            const response = await Request.send(request);
            if (response.success && response.data.html) {
                const redrawn = this.redraw_(row, response.data.html);
                if (response.data.connect) {
                    this.connecting_(response.data.connect); // the page is leaving
                } else if (response.data.notice) {
                    this.show_(redrawn, response.data.notice);
                }
                return;
            }
            if (response.data.html) { // a refusal that changed the row's state: the redrawn row says why
                this.redraw_(row, response.data.html);
                return;
            }
            this.show_(row, response.data);
        } catch (error) {
            this.show_(row, { message: error.message });
        }
        state.loaded();
    },

    show_: function (row, { link, message, type }) {
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
    },
};

export default PremiumLicense;
