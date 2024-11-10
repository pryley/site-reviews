jQuery(window).on('elementor/panel/init', () => {
    elementor.hooks.addFilter('editor/style/styleText', (css, context) => {
        const attributes = context?.container?.settings?.attributes || {};
        const widgetType = attributes?.widgetType;
        if (!~['site_review','site_reviews','site_reviews_form','site_reviews_summary'].indexOf(widgetType)) {
            return css;
        }
        const ratingColor = attributes?.__globals__?.rating_color || attributes?.rating_color;
        if ('' === ratingColor) {
            return css;
        }
        const id = context?.container?.id;
        const selector = `.elementor-${elementor.config.document.id} .elementor-element-${id} .glsr:not([data-theme])`;
        if (!!~['site_review','site_reviews'].indexOf(widgetType)) {
            css = css
                + `${selector} .glsr-review .glsr-star-empty { mask-image: var(--glsr-star-empty); mask-size: 100%; }`
                + `${selector} .glsr-review .glsr-star-full { mask-image: var(--glsr-star-full); mask-size: 100%; }`
                + `${selector} .glsr-review .glsr-star-half { mask-image: var(--glsr-star-half); mask-size: 100%; }`;
            return css;
        }
        if (!!~['site_reviews_form'].indexOf(widgetType)) {
            css = css
                + `${selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span { mask-image: var(--glsr-star-empty); mask-size: 100%; }`
                + `${selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span:is(.gl-active,.gl-selected) { mask-image: var(--glsr-star-full); }`
                + `${selector} .glsr-field-is-invalid .glsr-star-rating--stars > span.gl-active { mask-image: var(--glsr-star-error); mask-size: 100%; }`;
            return css;
        }
        if (!!~['site_reviews_summary'].indexOf(widgetType)) {
            css = css
                + `${selector} .glsr-star-empty { mask-image: var(--glsr-star-empty); mask-size: 100%; }`
                + `${selector} .glsr-star-full { mask-image: var(--glsr-star-full); mask-size: 100%; }`
                + `${selector} .glsr-star-half { mask-image: var(--glsr-star-half); mask-size: 100%; }`;
            return css;
        }
        return css;
    });
})
