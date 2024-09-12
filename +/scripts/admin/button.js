export default (el) => {
    const isImportForm = el.get(0).hasAttribute('data-ajax-import');
    const siblings = el.siblings('button:not([data-ajax-cancel])');
    // const isDisabled = (fallback) => {
    //     const files = el.closest('form.wp-upload-form').find('input[type="file"]');
    //     if (files.length) {
    //         return '' === files.map((i,e) => e.value).get().join('');
    //     }
    //     return fallback;
    // };
    const loaded = () => {
        if ('true' === el.attr('aria-busy')) {
            siblings.prop('disabled', false);
            el.text(el.data('text'))
               .data('text', '')
               .attr('aria-busy', false)
               .removeClass('is-busy');
            if (!isImportForm) {
                el.prop('disabled', false)
            }
        }
    };
    const loading = () => {
        if (!!~['false', void(0)].indexOf(el.attr('aria-busy'))) {
            siblings.prop('disabled', true);
            el.addClass('is-busy')
               .attr('aria-busy', true)
               .data('text', el.text())
               .text(el.data('loading') || el.data('text'))
               .blur();
            if (!isImportForm) {
                el.prop('disabled', true)
            }
        }
    };
    return { el, loading, loaded };
}
