import dom from '@/public/dom.js';

class Captcha {
    constructor (Form) {
        this.Form = Form;
        this.captchaEl = this._buildContainer();
        this.id = -1;
        this.instanceFr = null;
    }

    execute () {
        if (!this.captchaEl) {
            this.Form.submitForm();
            return;
        }
        if ('friendlycaptcha' === GLSR.captcha.type) {
            setTimeout(() => {
                if (1 === +this.captchaEl.dataset.token || '.ERROR' === this.Form.form['frc-captcha-solution'].value) {
                    this.Form.submitForm();
                } else {
                    this.execute()
                }
            }, 200)
        } else {
            if (1 === +this.captchaEl.dataset.error) {
                this._submitFormWithToken('sitekey_invalid')
            } else {
                grecaptcha.execute(this.id, { action: 'submit_review' });
            }
        }
    }

    render () {
        this.Form.form.onsubmit = null; // just in case!
        this.reset()
        if (!this.captchaEl) return;
        setTimeout(() => {
            if (-1 !== this.id || null !== this.instanceFr) return;
            // grecaptcha is used for both recaptcha and hcaptcha
            let undefinedRecaptcha = (typeof grecaptcha === 'undefined' || typeof grecaptcha.render === 'undefined');
            let undefinedFrcaptcha = (typeof friendlyChallenge === 'undefined' || typeof friendlyChallenge.WidgetInstance === 'undefined');
            if (undefinedRecaptcha && undefinedFrcaptcha) {
                this.render() // try again...
            } else {
                try {
                    if ('friendlycaptcha' === GLSR.captcha.type) {
                        this._renderFrcaptcha()
                    } else {
                        this._renderRecaptcha()
                    }
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
        if (this.captchaEl) {
            this.captchaEl.dataset.error = 0; // reset hcaptcha/recaptcha error
        }
        if (this.instanceFr) {
            this.captchaEl.dataset.token = 0;
            this.instanceFr.reset() // reset friendlycaptcha
        }
        this.is_submitting = false;
    }

    _buildContainer () {
        const containerEl = this.Form.form.querySelector('.glsr-captcha-holder');
        if (!containerEl) {
            return false;
        }
        if (this.instanceFr) {
            this.instanceFr.destroy() // remove friendlycaptcha
        }
        Array.from(containerEl.getElementsByClassName(GLSR.captcha.class)).forEach(el => el.remove());
        const el = dom('div', {class: GLSR.captcha.class});
        containerEl.appendChild(el);
        return el;
    }

    _renderFrcaptcha () {
        this.captchaEl.dataset.sitekey = GLSR.captcha.sitekey;
        this.instanceFr = new friendlyChallenge.WidgetInstance(this.captchaEl, {
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
        try {
            this.id = grecaptcha.render(this.captchaEl, {
                badge: GLSR.captcha.badge,
                callback: (token) => this._submitFormWithToken(token),
                'error-callback': () => {
                    this.captchaEl.dataset.error = 1;
                },
                'expired-callback': () => this.reset(),
                isolated: true,
                sitekey: GLSR.captcha.sitekey,
                size: GLSR.captcha.size,
                theme: GLSR.captcha.theme,
            });
        } catch (error) {
            this.captchaEl.dataset.error = 1;
            console.error(error);
        }
    }

    _submitFormWithToken (token) {
        if (this.Form.form['g-recaptcha-response'] && token) {
            this.Form.form['g-recaptcha-response'].value = token;
        }
        this.Form.submitForm()
    }
}

export default Captcha;
