document.addEventListener('DOMContentLoaded', () => {
    const glsr_ux_builder = (ev) => {
        if (ev.tag.startsWith('ux_site_review')) {
            let iframe = ev.$scope.$ctrl.targets.$iframe().get(0);
            let win = iframe.contentWindow || iframe;
            win.GLSR.Event.trigger('site-reviews/init')
            ev.$element.find('.glsr :input,.glsr a').attr('tabindex', -1).css('pointerEvents','none')
        }
    }
    UxBuilder.on('shortcode-attached', glsr_ux_builder)
});
