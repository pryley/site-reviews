import dom from '@/public/dom.js';

class Captcha {
    constructor (Form) {
        this.Form = Form;
        this.captcha = {
            friendlycaptcha: 'friendlyChallenge',
            hcaptcha: 'hcaptcha',
            procaptcha: 'procaptcha',
            recaptcha_v2_invisible: 'grecaptcha',
            recaptcha_v3: 'grecaptcha',
            turnstile: 'turnstile',
        }[GLSR.captcha.type];
        this.captchaEl = false;
        this.containerEl = this.Form.form.querySelector('.glsr-captcha-holder');
        this.loaded = false;
        this.token = null;
        this.widget = -1;
        this.fixCompatibility()
    }

    execute () {
        if (!this.captchaEl || !this.isWidgetLoaded()) {
            this.Form.submitForm();
        } else {
            try {
                this['execute_' + GLSR.captcha.type]();
            } catch (error) {
                console.error(error);
                this.Form.submitForm();
            }
        }
    }

    execute_friendlycaptcha (timeout) {
        if (1 === +this.captchaEl.dataset.error) {
            this._submitFormWithToken('sitekey_invalid')
        } else if (this.token) {
            this.Form.submitForm();
        } else {
            this._retry_execute((t) => this.execute_friendlycaptcha(t), timeout)
        }
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
            this.Form.submitForm();
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
            this.Form.submitForm();
        } else {
            this._retry_execute((t) => this.execute_turnstile(t), timeout)
        }
    }

    fixCompatibility () {
        // This checks to see if the hCaptcha plugin is being used on the page
        if ('hcaptcha' === GLSR.captcha.type && 'undefined' !== typeof window.hCaptchaOnLoad) {
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
            if ('module' !== type && 'undefined' !== typeof GLSR.captcha.urls['module']) {
                script.setAttribute('nomodule', '')
            }
            script.setAttribute('async', '')
            script.setAttribute('defer', '')
            document.head.append(script)
        });
        return Captcha._loading[key];
    }

    render (timeout) {
        this.Form.form.onsubmit = null; // remove any inline onsubmit handler that may interfere with captcha
        if (!this.containerEl || this.isWidgetLoaded()) return;
        if ('undefined' === typeof window[this.captcha]) {
            if (!this.loaded) {
                this.load(GLSR.captcha.urls['module'], 'module')
                    .then(() => {
                        this.load(GLSR.captcha.urls['nomodule'], 'nomodule') // don't wait for nomodule scripts
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
                this['render_' + GLSR.captcha.type]();
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
            'captchaType': GLSR.captcha.captcha_type,
            'language': GLSR.captcha.language,
            'siteKey': GLSR.captcha.sitekey, // data-attributes are not working with the render fn
            'theme': GLSR.captcha.theme, // data-attributes are not working with the render fn
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
            'language': GLSR.captcha.language,
            'sitekey': GLSR.captcha.sitekey, // data-attributes are not working with the render fn
            'theme': GLSR.captcha.theme, // data-attributes are not working with the render fn
        });
    }

    reset () {
        this.token = null;
        if (this.captchaEl) {
            this.captchaEl.dataset.error = 0;
        }
        if (this.isWidgetLoaded()) {
            if ('friendlycaptcha' === GLSR.captcha.type) {
                this.widget.reset()
            } else {
                window[this.captcha].reset(this.widget)
            }
        }
    }

    _buildContainer () {
        if ('friendlycaptcha' === GLSR.captcha.type && this.isWidgetLoaded()) {
            this.widget.destroy()
        }
        Array.from(this.containerEl.getElementsByClassName(GLSR.captcha.class)).forEach(el => el.remove());
        this.captchaEl = dom('div', {
            'class': GLSR.captcha.class,
            'data-badge': GLSR.captcha.badge,
            'data-captcha-type': GLSR.captcha.captcha_type,
            'data-lang': GLSR.captcha.language,
            'data-isolated': true,
            'data-sitekey': GLSR.captcha.sitekey,
            'data-size': GLSR.captcha.size,
            'data-theme': GLSR.captcha.theme,
            'data-type': GLSR.captcha.type,
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
            this.Form.submitForm();
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
        if (this.Form.form[GLSR.captcha.token_field] && token) {
            this.Form.form[GLSR.captcha.token_field].value = token;
        }
        this.Form.submitForm()
    }
}

Captcha._loading = {};

export default Captcha;
