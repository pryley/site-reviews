document.addEventListener('DOMContentLoaded', () => {
    const glsr_ux_builder = (ev) => {
        if (ev.tag.startsWith('site_review')) {
            let iframe = ev.$scope.$ctrl.targets.$iframe().get(0);
            let win = iframe.contentWindow || iframe;
            win.GLSR_init && win.GLSR_init();
            ev.$element.find('.glsr :input,.glsr a').attr('tabindex', -1).css('pointerEvents','none')
        }
    }
    UxBuilder.on('shortcode-attached', glsr_ux_builder)
});
