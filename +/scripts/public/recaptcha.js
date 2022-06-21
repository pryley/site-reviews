/** global: GLSR, grecaptcha, MutationObserver */

class Recaptcha {
    constructor (Form) {
        this.Form = Form;
        this.counter = 0;
        this.id = -1;
        this.is_submitting = false;
        this.parentEl = this.Form.form.querySelector('.glsr-recaptcha-holder');
        this.recaptchaEl = this._buildContainer();
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
        this.reset()
        this._renderWait()
    }

    reset () {
        if (this.id !== -1) {
            grecaptcha.reset(this.id)
        }
        this.counter = 0;
        this.is_submitting = false;
    }

    _buildContainer () {
        if (!this.parentEl) {
            return false;
        }
        Array.from(this.parentEl.getElementsByClassName("g-recaptcha")).forEach(el => el.remove());
        const el = document.createElement('div');
        el.classList.add('g-recaptcha');
        this.parentEl.appendChild(el);
        return el;
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
            if (-1 !== this.id) return;
            if (typeof grecaptcha === 'undefined' || typeof grecaptcha.render === 'undefined') {
                return this._renderWait()
            }
            this._renderChallenge()
        }, 250)
    }

    _renderChallenge () {
        try {
            this.id = grecaptcha.render(this.recaptchaEl, {
                badge: this.parentEl.dataset.badge,
                callback: this._submitForm.bind(this.Form, this.counter),
                'expired-callback': this.reset.bind(this),
                isolated: true,
                sitekey: this.parentEl.dataset.sitekey,
                size: this.parentEl.dataset.size,
            }, true);
        } catch (error) {
            console.error(error);
        }
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
