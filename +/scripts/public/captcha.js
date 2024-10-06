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

    execute_friendlycaptcha () {
        if (1 === +this.captchaEl.dataset.error) {
            this._submitFormWithToken('sitekey_invalid')
        } else if (this.token) {
            this.Form.submitForm();
        } else {
            setTimeout(() => this.execute_friendlycaptcha(), 100)
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
        } else if (this.token) {
            this.Form.submitForm();
        } else {
            setTimeout(() => this.execute_procaptcha(), 100)
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

    execute_turnstile () {
        // @see: https://github.com/cloudflare/cloudflare-docs/issues/6070
        let token = window[this.captcha].getResponse(this.widget);
        if (1 === +this.captchaEl.dataset.error || this.token || 'undefined' === typeof token) {
            this.Form.submitForm();
        } else {
            setTimeout(() => this.execute_turnstile(), 100)
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
        return !~[-1, null, undefined].indexOf(this.widget)
    }

    load (src, type) {
        return new Promise((resolve, reject) => {
            if ('undefined' === typeof src || this.isLoaded(src)) {
                resolve()
            } else {
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
            }
        })
    }

    render () {
        this.Form.form.onsubmit = null; // just in case!
        if (!this.containerEl || this.isWidgetLoaded()) return;
        if ('undefined' === typeof window[this.captcha]) {
            if (!this.loaded) {
                this.load(GLSR.captcha.urls['module'], 'module')
                    .then(() => {
                        this.load(GLSR.captcha.urls['nomodule'], 'nomodule') // don't wait for nomodule scripts
                    })
                    .then(() => this.loaded = true)
                    .then(() => setTimeout(() => this.render(), 100))
                    .catch(err => console.error(err))
            } else {
                setTimeout(() => this.render(), 50);
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

    render_hcaptcha () {
        if ('undefined' === typeof window[this.captcha]?.render) {
            setTimeout(() => this.render_hcaptcha(), 100);
            return;
        }
        this.widget = window[this.captcha].render(this.captchaEl, {
            callback: (token) => (this.token = token),
            'chalexpired-callback': () => this.reset(),
            'close-callback': () => this.Form.button.loaded(),
            'error-callback': () => (this.captchaEl.dataset.error = 1),
            'expired-callback': () => this.reset(),
        });
    }

    render_procaptcha () {
        this.widget = window[this.captcha].render(this.captchaEl, {
            callback: (token) => (this.token = token),
            captchaType: GLSR.captcha.captcha_type,
            language: GLSR.captcha.language,
            siteKey: GLSR.captcha.sitekey, // data-attributes are not working with the render fn
            theme: GLSR.captcha.theme, // data-attributes are not working with the render fn
            'chalexpired-callback': () => this.reset(),
            'close-callback': () => this.Form.button.loaded(),
            'error-callback': () => (this.captchaEl.dataset.error = 1),
            'expired-callback': () => this.reset(),
            // 'open-callback': () => false,
        });
    }

    render_recaptcha_v2_invisible () {
        this.render_recaptcha_v3()
    }

    render_recaptcha_v3 () {
        if ('undefined' === typeof window[this.captcha]?.render) {
            setTimeout(() => this.render_recaptcha_v3(), 100);
            return;
        }
        this.widget = window[this.captcha].render(this.captchaEl, {
            callback: (token) => this._submitFormWithToken(token),
            'error-callback': () => (this.captchaEl.dataset.error = 1),
            'expired-callback': () => this.reset(),
            isolated: true,
        });
    }

    render_turnstile () {
        this.widget = window[this.captcha].render(this.captchaEl, {
            action: 'submit_review',
            callback: (token) => (this.token = token),
            'error-callback': () => (this.captchaEl.dataset.error = 1), // site key is probably invalid
            'expired-callback': () => this.reset(),
            sitekey: GLSR.captcha.sitekey, // data-attributes are not working with the render fn
            theme: GLSR.captcha.theme, // data-attributes are not working with the render fn
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
            } else if ('procaptcha' === GLSR.captcha.type) {
                this.Form.form['procaptcha-response']?.remove()
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
            class: GLSR.captcha.class,
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

    _submitFormWithToken (token) {
        const fields = [
            'frc-captcha-solution',
            'g-recaptcha-response',
            'h-captcha-response',
        ];
        fields.forEach(name => {
            if (this.Form.form[name] && token) {
                this.Form.form[name].value = token;
            }
        })
        this.Form.submitForm()
    }
}

export default Captcha;
