document.addEventListener('DOMContentLoaded', () => {
    const glsr_vc_editor = (ev) => {
        let base = ev?.settings?.base+'';
        if (base.startsWith('site_review')) {
            let iframe = window.vc.$frame.get(0);
            let win = iframe.contentWindow || iframe;
            win.GLSR.Event.trigger('site-reviews/init')
            ev.view.$el.find('.glsr :input,.glsr a').attr('tabindex', -1).css('pointerEvents','none')
        }
    }
    window.vc.events.on('shortcodeView:ready', glsr_vc_editor)
    window.vc.events.on('shortcodeView:updated', glsr_vc_editor)
});
