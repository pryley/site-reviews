if ('undefined' !== typeof jQuery) {
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.elements.$window.on('elementor/popup/show', GLSR_init);
        elementorFrontend.hooks.addAction('frontend/element_ready/site_review.default', GLSR_init);
        elementorFrontend.hooks.addAction('frontend/element_ready/site_reviews.default', GLSR_init);
        elementorFrontend.hooks.addAction('frontend/element_ready/site_reviews_form.default', GLSR_init);
    })
}
