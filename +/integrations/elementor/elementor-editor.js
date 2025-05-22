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

jQuery(window).on('elementor/panel/init', () => {
    /**
     * Elementor overwrites all selector values in a color control with the
     * global color variable when a global variable is being used.
     */
    elementor.hooks.addFilter('editor/style/styleText', (css, context) => {
        const attributes = context?.container?.settings?.attributes || {};
        const shortcode = attributes?.widgetType || '';
        if (!shortcode.startsWith('site_review')) {
            return css;
        }
        const color = attributes?.__globals__?.style_rating_color || attributes?.style_rating_color || '';
        if ('' === color) {
            return css;
        }
        const id = context?.container?.id;
        const wrapper = `.elementor-${elementor.config.document.id} .elementor-element-${id} .glsr:not([data-theme])`;
        switch (shortcode) {
            case 'site_review':
            case 'site_reviews':
                css = css
                    + `${wrapper} .glsr-star-empty { background: var(--glsr-review-star-bg); mask-image: var(--glsr-star-empty); mask-size: 100%; }`
                    + `${wrapper} .glsr-star-full { background: var(--glsr-review-star-bg); mask-image: var(--glsr-star-full); mask-size: 100%; }`
                    + `${wrapper} .glsr-star-half { background: var(--glsr-review-star-bg); mask-image: var(--glsr-star-half); mask-size: 100%; }`;
                break;
            case 'site_reviews_form':
                css = css
                    + `${wrapper} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span { background: var(--glsr-form-star-bg); mask-image: var(--glsr-star-empty); mask-size: 100%; }`
                    + `${wrapper} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span:is(.gl-active,.gl-selected) { mask-image: var(--glsr-star-full); }`;
                break;
            case 'site_reviews_summary':
                css = css
                    + `${wrapper} .glsr-star-empty { background: var(--glsr-summary-star-bg); mask-image: var(--glsr-star-empty); mask-size: 100%; }`
                    + `${wrapper} .glsr-star-full { background: var(--glsr-summary-star-bg); mask-image: var(--glsr-star-full); mask-size: 100%; }`
                    + `${wrapper} .glsr-star-half { background: var(--glsr-summary-star-bg); mask-image: var(--glsr-star-half); mask-size: 100%; }`;
                break;
        }
        return css;
    })
})
