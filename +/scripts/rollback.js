import config from '@/public/config.js';

jQuery($ => {
    const __ = wp.i18n.__;
    const form = document.querySelector('#rollback-plugin');
    const $btn = $(form).find('button');
    const loaded = () => {
        if ('true' === $btn.attr('aria-busy')) {
            $btn.text($btn.data('text'))
               .data('text', '')
               .attr('aria-busy', false)
               .prop('disabled', false)
               .removeClass('is-busy');
        }
    }
    const loading = () => {
        if (['false', void(0)].includes($btn.attr('aria-busy'))) {
            let text = ($btn.data('loading') || $btn.data('text')).replace('%s', 'v' + form.version.value);
            $btn.addClass('is-busy')
               .prop('disabled', true)
               .attr('aria-busy', true)
               .data('text', $btn.text())
               .text(text);
        }
    }
    const onError = (response) => {
        loaded();
        GLSR.Notice.error(response.error ?? config.text.rollbackError);
        console.error(response);
    }
    const onRollbackError = (response) => {
        let error;
        if (_.isObject(response) && !_.isFunction(response.always)) {
            error = response.errorMessage;
        } else if (_.isString(response) && '-1' === response) {
            error = __('An error has occurred. Please reload the page and try again.');
        } else if (_.isString(response)) {
            error = response;
        } else if ('undefined' !== typeof response.readyState && 0 === response.readyState) {
            error = __('Connection lost or the server is busy. Please try again later.');
        } else if (_.isString(response.responseText) && '' !== response.responseText) {
            error = response.responseText;
        } else if (_.isString( response.statusText)) {
            error = response.statusText;
        }
        error = error.replace( /<[\/a-z][^<>]*>/gi, '' );
        GLSR.Notice.error((response.error ?? config.text.rollbackError) + ': ' + error);
    }
    const onSuccess = (response) => {
        wp.ajax.send({
            data: response.data,
            error: onRollbackError,
            success: () => (window.location = response.url),
        }).always(response => {
            loaded();
            if ('undefined' !== typeof response.debug) {
                _.map(response.debug, message => console.info(message));
            }
        })
    }
    const dialog = document.querySelector('#rollback-dialog');
    const close = (focusEl) => {
        $(document).off('keydown.glsr-rollback');
        dialog.classList.add('hidden');
        $(document.body).removeClass('modal-open');
        $('#wpwrap').removeAttr('aria-hidden');
        focusEl.focus();
    }
    const onKeydown = (ev) => {
        if ('Escape' === ev.key) {
            close($btn[0]);
        } else if ('Tab' === ev.key) {
            // Safari does not move the focus to a button with the Tab key.
            const buttons = [...dialog.querySelectorAll('button')];
            const index = buttons.indexOf(ev.target);
            const next = ev.shiftKey ? (index < 1 ? buttons.length : index) - 1 : (index + 1) % buttons.length;
            buttons[next].focus();
            ev.preventDefault();
        }
    }
    const open = () => {
        const option = form.version.selectedOptions[0];
        const names = JSON.parse(option.dataset.stops || '[]');
        const warning = dialog.querySelector('[data-warning]');
        dialog.querySelectorAll('[data-version]').forEach(el => (el.textContent = form.version.value));
        warning.querySelector('ul').replaceChildren(...names.map(name => {
            const item = document.createElement('li');
            item.textContent = name;
            return item;
        }));
        warning.classList.toggle('hidden', 0 === names.length);
        warning.querySelector('[data-premium]').classList.toggle('hidden', !option.dataset.premium);
        $('#wpwrap').attr('aria-hidden', 'true');
        // #wpwrap is hidden from screen readers while the dialog is open; the dialog must be outside it.
        $(document.body).addClass('modal-open').append(dialog);
        dialog.classList.remove('hidden');
        dialog.querySelector('[data-cancel]').focus();
        $(document).on('keydown.glsr-rollback', onKeydown);
    }
    const rollback = () => {
        let data = { _ajax_request: true, action: config.request.ajax.action };
        let request = {
            _action: form.action.value,
            _nonce: form._wpnonce.value,
            version: form.version.value,
        };
        data[config.nameprefix] = request;
        loading();
        wp.ajax.send({
            data,
            error: onError,
            success: onSuccess,
        })
    }
    $(dialog).on('click', '[data-cancel], .notification-dialog-background', () => close($btn[0]));
    $(dialog).on('click', '[data-confirm]', () => {
        // The busy state disables the form button, which then cannot hold the focus.
        close(form.version);
        rollback();
    });
    $(form).on('submit', (ev) => {
        ev.preventDefault();
        open();
    })
})
