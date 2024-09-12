const $ = jQuery;
const dom = (cls) => $('<div/>').addClass(cls);
const cls = {
    cancel: 'glsr-button button button-large button-secondary',
}
const events = {
    doc: 'wp-window-resized',
    win: 'hashchange',
};
const selectors = {
    el: 'glsr-progress',
    bar: 'glsr-progress__bar',
    status: 'glsr-progress__status',
};

export default (el) => {
    const bar = dom(selectors.bar);
    const cancel = $('<button type="button" data-ajax-cancel disabled>')
        .addClass(cls.cancel)
        .text(GLSR.text.cancel);
    const status = dom(selectors.status);

    const init = (cb) => {
        const value = el.text();
        cancel.insertAfter(el);
        el.data('value', value)
          .blur()
          .removeClass('is-busy')
          .addClass(selectors.el)
          .text('')
          .append(bar, status)
          .promise()
          .done(() => {
            cancel.prop('disabled', false).fadeIn('fast', () => cancel.on('click', onCancel))
            resize()
            run(cb)
          })
        $(document).on(events.doc, resize)
        $(window).on(events.win, resize)
        percent()
        text(value)
    };

    const destroy = (cb) => {
        $(document).off(events.doc, resize)
        $(window).off(events.win, resize)
        cancel.prop('disabled', true)
        el.text(el.data('value'))
          .removeClass(selectors.el)
          .promise()
          .done(() => {
            cancel.off('click').fadeOut('fast', () => cancel.remove())
            run(cb)
          })
    };

    const onCancel = (ev) => {
        ev.preventDefault()
        el.closest('form').trigger('glsr-cancel-import')
        cancel.prop('disabled', true).off('click')
        text(GLSR.text.cancelling)
    };

    const percent = (value = 0) => {
        bar.css('max-width', Math.min(100, Math.max(0, value)) + '%')
        resize()
    };

    const resize = () => {
        bar.width(el.outerWidth())
    };

    const run = (cb) => {
        if (typeof cb === 'function') {
            cb()
        }
    };

    const text = (value = '') => {
        bar.attr('data-text', value)
        status.text(value)
        resize()
    };

    return {
        dom: { bar, status },
        destroy,
        init,
        percent,
        resize,
        text,
    };
}
