jQuery($ => {
    $('.glsr_type_range-wrapper').each((i, el) => {
        $(el).css({
            alignItems: 'center',
            display: 'flex',
            gap: '1em',
            maxWidth: '25rem',
        });
        let input = $(el).find('input.wpb_vc_param_value').css({
            flex: '1',
        });
        let infobox = $(el).find('input.wpb_vc_param_infobox').css({
            width: '5em',
            padding: '8px 6px',
            textAlign: 'center',
        });
        input.on('input', (ev) => infobox.val(input.val()))
        infobox.on('input', (ev) => input.val(infobox.val()))
    })
});
