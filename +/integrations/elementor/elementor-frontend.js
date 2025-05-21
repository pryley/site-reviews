window.addEventListener('elementor/frontend/init', () => {
    elementorFrontend.elements.$window.on('elementor/popup/show', () => GLSR_init());
    elementorFrontend.hooks.addAction('frontend/element_ready/global', ($scope) => {
        if ($scope.attr('data-widget_type').startsWith('site_review')) {
            GLSR_init()
        }
    })
})
