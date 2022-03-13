/** global: GLSR */
jQuery($ => {
    const __ = wp.i18n.__;
    const form = document.querySelector('#rollback-plugin');
    const $button = $(form).find('button');
    const $loading = $button.find('span');
    const onError = (response) => {
        GLSR.notices.error(GLSR.text.rollback_error);
        $button.removeClass('is-busy').prop('disabled', 0);
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
        GLSR.notices.error(GLSR.text.rollback_error + ': ' + error);
    }
    const onSuccess = (response) => {
        wp.ajax.send({
            data: response.data,
            error: onRollbackError,
            success: () => (window.location = response.url),
        }).always(response => {
            $button.removeClass('is-busy').prop('disabled', 0);
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
        $loading.attr('data-loading', $loading.data('loading').replace('%s', 'v' + request.version));
        $button.addClass('is-busy').prop('disabled', 1);
        wp.ajax.send({
            data,
            error: onError,
            success: onSuccess,
        })
    })
})
