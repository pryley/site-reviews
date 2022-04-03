/** global: GLSR, grecaptcha, MutationObserver */

class Recaptcha {
    constructor (Form) {
        this.Form = Form;
        this.counter = 0;
        this.id = -1;
        this.is_submitting = false;
        this.recaptchaEl = Form.form.querySelector('.glsr-recaptcha-holder');
        this.observer = new MutationObserver(mutations => {
            const mutation = mutations.pop();
            if (!mutation.target || mutation.target.style.visibility === 'visible') return;
            this.observer.disconnect()
            setTimeout(() => {
                if (this.is_submitting) return;
                this.Form.enableButton();
            }, 250)
        })
    }

    destroy () {
        this.counter = 0;
        this.id = -1;
        this.is_submitting = false;
        if (this.recaptchaEl) {
            this.recaptchaEl.innerHTML = '';
        }
    }

    execute () {
        if (this.id !== -1) {
            this.counter = 0;
            this._observeMutations(this.id)
            grecaptcha.execute(this.id)
            return;
        }
        setTimeout(() => {
            this.counter++;
            this._submitForm.call(this.Form, this.counter)
        }, 1000)
    }

    render () {
        this.Form.form.onsubmit = null;
        this.destroy()
        this._renderWait()
    }

    reset () {
        this.counter = 0;
        this.is_submitting = false;
        if (this.id !== -1) {
            grecaptcha.reset(this.id)
        }
    }

    _observeMutations (id) {
        const client = window.___grecaptcha_cfg.clients[id];
        for (let property in client) {
            if (!client.hasOwnProperty(property)) continue;
            if (Object.prototype.toString.call(client[property]) !== '[object String]') continue;
            var overlayEl = document.querySelector('iframe[name=c-' + client[property] + ']');
            if (overlayEl) {
                this.observer.observe(overlayEl.parentElement.parentElement, {
                    attributeFilter: ['style'],
                    attributes: true,
                })
                break;
            }
        }
    }

    _renderWait () {
        if (!this.recaptchaEl) return;
        setTimeout(() => {
            if (typeof grecaptcha === 'undefined' || typeof grecaptcha.render === 'undefined') {
                return this._renderWait()
            }
            this.id = grecaptcha.render(this.recaptchaEl, {
                callback: this._submitForm.bind(this.Form, this.counter),
                // 'error-callback': this.reset.bind(this), // @todo
                // 'error-callback': The name of your callback function, executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. If you specify a function here, you are responsible for informing the user that they should retry.
                'expired-callback': this.reset.bind(this),
                isolated: true,
            }, true);
        }, 250)
    }

    _submitForm (counter) {
        this.recaptcha.is_submitting = true;
        if (!this.useAjax) {
            this.disableButton()
            this.form.submit()
            return;
        }
        this.submitForm(counter)
    }
}

export default Recaptcha;
