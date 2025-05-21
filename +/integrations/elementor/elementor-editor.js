window.addEventListener('elementor/init', () => {
    const select2 = elementor.modules.controls.Select2.extend({
        isLoaded: false,
        addControlSpinner() {
            this.ui.select.prop('disabled', true);
            this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner">&nbsp;<i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>');
        },
        getControlValueByName(controlName) {
            const name = this.model.get('group_prefix') + controlName;
            return this.container.settings.get(name);
        },
        getInitialValues() {
            let ids = this.getControlValue();
            elementorCommon.ajax.loadObjects({
                action: 'glsr_elementor_ajax_query_selected',
                before: () => this.addControlSpinner(),
                data: this.getQueryData(),
                ids: _.isArray(ids) ? ids : [ids],
                success: (response) => {
                    this.isLoaded = true;
                    this.model.set('options', response)
                    setTimeout(() => this.render(), 1) // 1ms timeout fixes the 0px placeholder width
                }
            })
        },
        getQueryData() {
            return {
                include: this.getControlValueByName(this.model.get('include')),
                option: this.model.get('name'),
                shortcode: this.container.settings.get('widgetType'),
                unique_id: this.cid + this.model.get('name'),
            }
        },
        getSelect2DefaultOptions() {
            return jQuery.extend(elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(this, arguments), {
                ajax: {
                    transport: (params, success, error) => elementorCommon.ajax.addRequest('glsr_elementor_ajax_query', {
                        data: { search: params.data.q, ...this.getQueryData() },
                        error,
                        success,
                    }),
                    cache: true,
                },
                minimumInputLength: 1,
            })
        },
        onReady() {
            if (!this.isLoaded) {
                this.getInitialValues();
            }
        },
    });
    elementor.addControlView('select2_ajax', select2)
    jQuery(document).on('change', '.elementor-control-type-multi_switcher .elementor-switch-input', function() {
        const $checkbox = jQuery(this);
        const $control = $checkbox.closest('.elementor-control-type-multi_switcher')
        const $hiddenInput = $control.find('input[data-setting]');
        const option = $checkbox.val();
        const values = $hiddenInput.val() ? $hiddenInput.val().split(',') : [];
        const updatedValues = $checkbox.is(':checked')
            ? [...new Set([...values, option])]
            : values.filter(item => item !== option);
        $hiddenInput.val(updatedValues.join(',')).trigger('input');
    })
})

// jQuery(window).on('elementor/panel/init', () => {
//     elementor.hooks.addFilter('editor/style/styleText', (css, context) => {
//         const attributes = context?.container?.settings?.attributes || {};
//         const widgetType = attributes?.widgetType;
//         if (!~['site_review','site_reviews','site_reviews_form','site_reviews_summary'].indexOf(widgetType)) {
//             return css;
//         }
//         const ratingColor = attributes?.__globals__?.rating_color || attributes?.rating_color;
//         if ('' === ratingColor) {
//             return css;
//         }
//         const id = context?.container?.id;
//         const selector = `.elementor-${elementor.config.document.id} .elementor-element-${id} .glsr:not([data-theme])`;
//         if (!!~['site_review','site_reviews'].indexOf(widgetType)) {
//             css = css
//                 + `${selector} .glsr-review .glsr-star-empty { mask-image: var(--glsr-star-empty); mask-size: 100%; }`
//                 + `${selector} .glsr-review .glsr-star-full { mask-image: var(--glsr-star-full); mask-size: 100%; }`
//                 + `${selector} .glsr-review .glsr-star-half { mask-image: var(--glsr-star-half); mask-size: 100%; }`;
//             return css;
//         }
//         if (!!~['site_reviews_form'].indexOf(widgetType)) {
//             GLSR_init()
//             css = css
//                 + `${selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span { mask-image: var(--glsr-star-empty); mask-size: 100%; }`
//                 + `${selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span:is(.gl-active,.gl-selected) { mask-image: var(--glsr-star-full); }`
//                 + `${selector} .glsr-field-is-invalid .glsr-star-rating--stars > span.gl-active { mask-image: var(--glsr-star-error); mask-size: 100%; }`;
//             return css;
//         }
//         if (!!~['site_reviews_summary'].indexOf(widgetType)) {
//             css = css
//                 + `${selector} .glsr-star-empty { mask-image: var(--glsr-star-empty); mask-size: 100%; }`
//                 + `${selector} .glsr-star-full { mask-image: var(--glsr-star-full); mask-size: 100%; }`
//                 + `${selector} .glsr-star-half { mask-image: var(--glsr-star-half); mask-size: 100%; }`;
//             return css;
//         }
//         return css;
//     })
// })
