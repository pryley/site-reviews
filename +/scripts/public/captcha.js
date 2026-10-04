import config from '@/public/config.js';
import dom from '@/public/dom.js';

class Captcha {
    constructor (Form) {
        this.Form = Form;
        this.captcha = {
            friendlycaptcha: 'friendlyChallenge',
            friendlycaptcha_v2: 'frcaptcha',
            hcaptcha: 'hcaptcha',
            procaptcha: 'procaptcha',
            recaptcha_v2_invisible: 'grecaptcha',
            recaptcha_v3: 'grecaptcha',
            turnstile: 'turnstile',
        }[config.captcha.type];
        this.captchaEl = false;
        this.containerEl = this.Form.el.querySelector('.glsr-captcha-holder');
        this.loaded = false;
        this.token = null;
        this.widget = -1;
        this.fixCompatibility()
    }

    execute () {
        if (!this.captchaEl || !this.isWidgetLoaded()) {
            this.Form.submit();
        } else {
            try {
                this['execute_' + config.captcha.type]();
            } catch (error) {
                console.error(error);
                this.Form.submit();
            }
        }
    }

    execute_friendlycaptcha (timeout) {
        if (1 === +this.captchaEl.dataset.error) {
            this._submitFormWithToken('sitekey_invalid')
        } else if (this.token) {
            this.Form.submit();
        } else {
            this._retry_execute((t) => this.execute_friendlycaptcha(t), timeout)
        }
    }

    execute_friendlycaptcha_v2 () {
        this.execute_friendlycaptcha()
    }

    execute_hcaptcha () {
        if (1 === +this.captchaEl.dataset.error) {
            this._submitFormWithToken('sitekey_invalid')
        } else if (this.token) {
            this._submitFormWithToken(this.token);
        } else {
            window[this.captcha].execute(this.widget, {
                action: 'submit_review',
                async: true,
            }).then(({ response }) => {
                this._submitFormWithToken(response);
            }).catch(err => {
                console.error(err);
            });
        }
    }

    execute_procaptcha () {
        if (1 === +this.captchaEl.dataset.error) {
            this._submitFormWithToken('sitekey_invalid')
        } else {
            this.Form.submit();
        }
    }

    execute_recaptcha_v2_invisible () {
        this.execute_recaptcha_v3()
    }

    execute_recaptcha_v3 () {
        if (1 === +this.captchaEl.dataset.error) {
            this._submitFormWithToken('sitekey_invalid')
        } else {
            window[this.captcha].execute(this.widget, { action: 'submit_review' });
        }
    }

    execute_turnstile (timeout) {
        // The return value type of getResponse is undocumented
        // @see: https://github.com/cloudflare/cloudflare-docs/issues/6070
        let token = window[this.captcha].getResponse(this.widget);
        if (1 === +this.captchaEl.dataset.error || this.token || 'undefined' === typeof token) {
            this.Form.submit();
        } else {
            this._retry_execute((t) => this.execute_turnstile(t), timeout)
        }
    }

    fixCompatibility () {
        // This checks to see if the hCaptcha plugin is being used on the page
        if ('hcaptcha' === config.captcha.type && 'undefined' !== typeof window.hCaptchaOnLoad) {
            document.body.click() // @hack immediately load the hcaptcha script on the page
        }
    }

    isLoaded (src) {
        for (let i = 0; i < document.scripts.length; i++) {
            if (src.split('?')[0] === document.scripts[i].src.split('?')[0]) {
                return true;
            }
        }
        return false;
    }

    isWidgetLoaded () {
        return this.widget !== -1 && this.widget != null; // != checks for null and undefined
    }

    load (src, type) {
        if ('undefined' === typeof src || this.isLoaded(src)) {
            return Promise.resolve();
        }
        const key = src.split('?')[0];
        if (Captcha._loading[key]) {
            return Captcha._loading[key];
        }
        Captcha._loading[key] = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.onload = resolve;
            script.onerror = reject;
            script.src = src;
            script.type = 'module' === type ? 'module' : 'text/javascript';
            if ('module' !== type && 'undefined' !== typeof config.captcha.urls['module']) {
                script.setAttribute('nomodule', '')
            }
            script.setAttribute('async', '')
            script.setAttribute('defer', '')
            document.head.append(script)
        });
        return Captcha._loading[key];
    }

    render (timeout) {
        this.Form.el.onsubmit = null; // remove any inline onsubmit handler that may interfere with captcha
        if (!this.containerEl || this.isWidgetLoaded()) return;
        if ('undefined' === typeof window[this.captcha]) {
            if (!this.loaded) {
                this.load(config.captcha.urls['module'], 'module')
                    .then(() => {
                        this.load(config.captcha.urls['nomodule'], 'nomodule') // don't wait for nomodule scripts
                    })
                    .then(() => this.loaded = true)
                    .then(() => this._retry_render((t) => this.render(t), timeout))
                    .catch(err => console.error(err))
            } else {
                this._retry_render((t) => this.render(t), timeout)
            }
        } else {
            this.reset()
            this._buildContainer()
            try {
                this['render_' + config.captcha.type]();
            } catch (error) {
                this.captchaEl.dataset.error = 1;
                console.error(error)
            }
        }
    }

    render_friendlycaptcha () {
        this.widget = new window[this.captcha].WidgetInstance(this.captchaEl, {
            doneCallback: (token) => (this.token = token),
            errorCallback: (error) => {
                console.error(error)
                this.captchaEl.dataset.error = 1;
            },
        });
    }

    render_friendlycaptcha_v2 () {
        // data-attributes on this.captchaEl do not work when the widget is manually created
        this.widget = window[this.captcha].createWidget({
            element: this.captchaEl,
            sitekey: config.captcha.sitekey,
            startMode: 'focus',
            theme: config.captcha.theme,
        });
        this.captchaEl.addEventListener('frc:widget.complete', event => {
            this.token = event?.detail?.response;
        });
        this.captchaEl.addEventListener('frc:widget.error', event => {
            console.error(event)
            this.captchaEl.dataset.error = 1;
        });
    }

    render_hcaptcha (timeout) {
        if ('undefined' === typeof window[this.captcha]?.render) {
            this._retry_render((t) => this.render_hcaptcha(t), timeout)
            return;
        }
        this.widget = window[this.captcha].render(this.captchaEl, {
            'callback': (token) => (this.token = token),
            'chalexpired-callback': () => this.reset(),
            'close-callback': () => this.Form.button.loaded(),
            'error-callback': () => (this.captchaEl.dataset.error = 1),
            'expired-callback': () => this.reset(),
        });
    }

    render_procaptcha () {
        this.widget = window[this.captcha].render(this.captchaEl, {
            'callback': (token) => (this.token = token),
            'captchaType': config.captcha.captchaType,
            'language': config.captcha.language,
            'siteKey': config.captcha.sitekey, // data-attributes are not working with the render fn
            'theme': config.captcha.theme, // data-attributes are not working with the render fn
            'chalexpired-callback': () => this.reset(),
            'close-callback': () => this.Form.button.loaded(),
            'error-callback': () => (this.captchaEl.dataset.error = 1),
            'expired-callback': () => this.reset(),
            // 'failed-callback': () => this.reset(),
            // 'open-callback': () => false,
        }) || 1; // because procaptcha doesn't set a widget id.
    }

    render_recaptcha_v2_invisible () {
        this.render_recaptcha_v3()
    }

    render_recaptcha_v3 (timeout) {
        if ('undefined' === typeof window[this.captcha]?.render) {
            this._retry_render((t) => this.render_recaptcha_v3(t), timeout)
            return;
        }
        this.widget = window[this.captcha].render(this.captchaEl, {
            'callback': (token) => this._submitFormWithToken(token),
            'error-callback': () => (this.captchaEl.dataset.error = 1),
            'expired-callback': () => this.reset(),
            'isolated': true,
        });
    }

    render_turnstile () {
        this.widget = window[this.captcha].render(this.captchaEl, {
            'action': 'submit_review',
            'callback': (token) => (this.token = token),
            'error-callback': () => (this.captchaEl.dataset.error = 1), // site key is probably invalid
            'expired-callback': () => this.reset(),
            'language': config.captcha.language,
            'sitekey': config.captcha.sitekey, // data-attributes are not working with the render fn
            'theme': config.captcha.theme, // data-attributes are not working with the render fn
        });
    }

    reset () {
        this.token = null;
        if (this.captchaEl) {
            this.captchaEl.dataset.error = 0;
        }
        if (this.isWidgetLoaded()) {
            if (['friendlycaptcha', 'friendlycaptcha_v2'].includes(config.captcha.type)) {
                this.widget.reset()
            } else {
                window[this.captcha].reset(this.widget)
            }
        }
    }

    _buildContainer () {
        if (['friendlycaptcha', 'friendlycaptcha_v2'].includes(config.captcha.type) && this.isWidgetLoaded()) {
            this.widget.destroy()
        }
        Array.from(this.containerEl.getElementsByClassName(config.captcha.class)).forEach(el => el.remove());
        this.captchaEl = dom('div', {
            'class': config.captcha.class,
            'data-badge': config.captcha.badge,
            'data-captcha-type': config.captcha.captchaType,
            'data-lang': config.captcha.language,
            'data-isolated': true,
            'data-sitekey': config.captcha.sitekey,
            'data-size': config.captcha.size,
            'data-theme': config.captcha.theme,
            'data-type': config.captcha.type,
        });
        this.containerEl.appendChild(this.captchaEl);
    }

    _retry_execute (callback, timeout) {
        if ('undefined' === typeof timeout) {
            timeout = 10000;
        }
        if (timeout <= 0) {
            console.warn('Site Reviews: captcha execute timed out');
            if (this.captchaEl) {
                this.captchaEl.dataset.error = 1;
            }
            this.Form.submit();
            return;
        }
        setTimeout(() => callback(timeout - 100), 100);
    }

    _retry_render (callback, timeout) {
        if ('undefined' === typeof timeout) {
            timeout = 10000;
        }
        if (timeout <= 0) {
            console.warn('Site Reviews: captcha render timed out');
            return;
        }
        setTimeout(() => callback(timeout - 100), 100);
    }

    _submitFormWithToken (token) {
        if (this.Form.el[config.captcha.tokenField] && token) {
            this.Form.el[config.captcha.tokenField].value = token;
        }
        this.Form.submit()
    }
}

Captcha._loading = {};

export default Captcha;
