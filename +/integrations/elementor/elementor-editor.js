window.addEventListener('elementor/init', () => {
    const select2 = elementor.modules.controls.Select2.extend({
        isLoaded: false,
        addControlSpinner() {
            this.ui.select.prop('disabled', true);
            this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner">&nbsp;<i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>');
        },
        getControlValueByName(controlName) {
            const name = (this.model.get('group_prefix') ?? '') + controlName;
            return this.container.settings.get(name) ?? '';
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
            const data = {
                include: this.getControlValueByName(this.model.get('include')),
                option: this.model.get('name'),
                shortcode: this.container.settings.get('widgetType'),
                unique_id: this.cid + this.model.get('name'),
            }
            const depends_on = this.model.get('depends_on');
            if (depends_on) {
                data[depends_on] = this.getControlValueByName(depends_on);
            }
            return data;
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
window.addEventListener('elementor/panel/init', () => {
    // Elementor does not provide a way to add widget classes in the editor
    // when a widget setting changes, so this will have to do.
    elementor.hooks.addFilter('editor/style/styleText', (css, context) => {
        const $el = context?.$el;
        const attributes = context?.container?.settings?.attributes || {};
        const shortcode = attributes?.widgetType || '';
        if (!$el || attributes?.theme || !shortcode.startsWith('site_review')) {
            return css;
        }
        const hasClass = attributes?.style_rating_color || attributes?.__globals__?.style_rating_color;
        $el.find('.glsr').parent()[hasClass ? 'addClass' : 'removeClass']('has-custom-color');
        return css;
    })
})
