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
        GLSR.notices.error(response.error ?? GLSR.text.rollback_error);
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
        GLSR.notices.error((response.error ?? GLSR.text.rollback_error) + ': ' + error);
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
    $(form).on('submit', (ev) => {
        ev.preventDefault();
        let data = { _ajax_request: true, action: GLSR.action };
        let request = {
            _action: form.action.value,
            _nonce: form._wpnonce.value,
            version: form.version.value,
        };
        data[GLSR.nameprefix] = request;
        loading();
        wp.ajax.send({
            data,
            error: onError,
            success: onSuccess,
        })
    })
})
