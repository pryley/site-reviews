/** global: GLSR, grecaptcha, MutationObserver */

class Captcha {
    constructor (Form) {
        this.Form = Form;
        this.containerEl = Form.form.querySelector('.glsr-captcha-holder');
        this.captchaEl = this._buildContainer();
        this.id = -1;
        this.instance = null;
    }

    execute () {
        if ('recaptcha_v3' === GLSR.captcha.type) {
            try {
                grecaptcha.execute(GLSR.captcha.sitekey, { action: 'submit_review' }).then(token => {
                    this.Form.submitForm(token);
                });
            } catch (error) {
                this.Form.submitForm('sitekey_invalid');
            }
        } else if (~['hcaptcha', 'recaptcha_v2_invisible'].indexOf(GLSR.captcha.type)) {
            if (1 === +this.captchaEl.dataset.error) {
                // likely a site key error
                this.Form.submitForm('sitekey_invalid');
            } else {
                grecaptcha.execute(this.id);
            }
        } else if ('friendlycaptcha' === GLSR.captcha.type) {
            setTimeout(() => {
                if (1 === +this.captchaEl.dataset.token || '.ERROR' === this.Form.form['frc-captcha-solution'].value) {
                    this.Form.submitForm();
                } else {
                    this.execute()
                }
            }, 200)
        }
    }

    render () {
        this.Form.form.onsubmit = null; // just in case!
        this.reset()
        if (!this.captchaEl) return; // don't render recaptcha v3
        setTimeout(() => {
            if (-1 !== this.id || null !== this.instance) return;
            // grecaptcha is used for both recaptcha and hcaptcha
            let undefinedRecaptcha = (typeof grecaptcha === 'undefined' || typeof grecaptcha.render === 'undefined');
            let undefinedFrcaptcha = (typeof friendlyChallenge === 'undefined' || typeof friendlyChallenge.WidgetInstance === 'undefined');
            if (undefinedRecaptcha && undefinedFrcaptcha) {
                this.render() // try again...
            } else {
                try {
                    this._renderFrcaptcha()
                    this._renderRecaptcha()
                } catch (error) {
                    console.error(error)
                }
            }
        }, 200)
    }

    reset () {
        if (-1 !== this.id) {
            grecaptcha.reset(this.id)
        }
        if (this.instance) {
            this.captchaEl.dataset.token = 0;
            this.instance.reset() // reset friendlycaptcha
        }
        if (this.captchaEl) {
            this.captchaEl.dataset.error = 0; // reset hcaptcha/recaptcha error
        }
        this.is_submitting = false;
        if (this.Form.form['g-recaptcha-response']) {
            this.Form.form['g-recaptcha-response'].value = '';
        }
    }

    _buildContainer () {
        if (!this.containerEl) {
            return false;
        }
        if (this.instance) {
            this.instance.destroy() // remove friendlycaptcha
        }
        Array.from(this.containerEl.getElementsByClassName(GLSR.captcha.class)).forEach(el => el.remove());
        const el = document.createElement('div');
        el.className = GLSR.captcha.class;
        this.containerEl.appendChild(el);
        return el;
    }

    _renderFrcaptcha () {
        if ('friendlycaptcha' !== GLSR.captcha.type) return;
        this.captchaEl.dataset.sitekey = GLSR.captcha.sitekey;
        this.instance = new friendlyChallenge.WidgetInstance(this.captchaEl, {
            doneCallback: (token) => {
                this.captchaEl.dataset.token = 1;
            },
            errorCallback: (error) => {
                // site key is probably invalid
                this.captchaEl.dataset.token = 1;
            },
        });
    }

    _renderRecaptcha () {
        if (!~['hcaptcha', 'recaptcha_v2_invisible'].indexOf(GLSR.captcha.type)) return;
        try {
            this.id = grecaptcha.render(this.captchaEl, {
                badge: GLSR.captcha.badge,
                callback: (token) => this.Form.submitForm(token),
                'error-callback': () => {
                    this.captchaEl.dataset.error = 1;
                },
                'expired-callback': () => this.reset(),
                isolated: true,
                sitekey: GLSR.captcha.sitekey,
                size: GLSR.captcha.size,
            });
        } catch (error) {
            this.captchaEl.dataset.error = 1;
            console.error(error);
        }
    }
}

export default Captcha;
