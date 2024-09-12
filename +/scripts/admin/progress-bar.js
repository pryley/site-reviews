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
    const bar1 = dom(selectors.bar);
    const bar2 = dom(selectors.bar);
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
          .append(bar1, bar2, status)
          .promise()
          .done(() => {
            cancel.prop('disabled', false).fadeIn('fast', () => cancel.on('click', onCancel))
            resize()
            run(cb)
          })
        $(document).on(events.doc, resize)
        $(window).on(events.win, resize)
        percent(0, 1)
        percent(0, 2)
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

    const percent = (value = 0, barNum = 1) => {
        if (1 === barNum) {
            bar1.css('max-width', Math.min(100, Math.max(0, value)) + '%')
        }
        if (2 === barNum) {
            bar2.css('max-width', Math.min(100, Math.max(0, value)) + '%')
        }
        resize()
    };

    const resize = () => {
        bar1.width(el.outerWidth())
        bar2.width(el.outerWidth())
    };

    const run = (cb) => {
        if (typeof cb === 'function') {
            cb()
        }
    };

    const text = (value = '') => {
        bar1.attr('data-text', value)
        bar2.attr('data-text', value)
        status.text(value)
        resize()
    };

    return {
        dom: { bar1, bar2, status },
        destroy,
        init,
        percent,
        resize,
        text,
    };
}
