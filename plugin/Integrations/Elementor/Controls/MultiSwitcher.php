<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Controls;

use Elementor\Base_Data_Control;
use GeminiLabs\SiteReviews\Helpers\Str;

class MultiSwitcher extends Base_Data_Control
{
    public function content_template(): void
    {
        $control_uid_input_type = '{{option}}';
        ?>
        <div class="elementor-control-type-switcher elementor-control-type-multi_switcher" style="display: grid; row-gap: 10px;">
            <# if ( data.label ) {#>
                <label class="elementor-control-title">{{{ data.label }}}</label>
            <# } #>
            <input type="hidden" data-setting="{{ data.name }}" value="{{ data.controlValue || '' }}" />
            <# var values = data.controlValue ? data.controlValue.split(',').filter(Boolean) : []; #>
            <# _.each( data.options, function( label, option ) { #>
            <div class="elementor-control-field" style="display: grid; grid-template-columns: 1fr auto;">
                <label for="<?php $this->print_control_uid($control_uid_input_type); ?>" class="elementor-control-title">{{{ label }}}</label>
                <div class="elementor-control-input-wrapper">
                    <label class="elementor-switch elementor-control-unit-2">
                        <input id="<?php $this->print_control_uid($control_uid_input_type); ?>"
                            type="checkbox"
                            class="elementor-switch-input"
                            value="{{ option }}"
                            <# if ( values.includes(option) ) { #>checked<# } #>
                        />
                        <span class="elementor-switch-label" data-on="{{ data.label_on }}" data-off="{{ data.label_off }}"></span>
                        <span class="elementor-switch-handle"></span>
                    </label>
                </div>
            </div>
            <# } ); #>
        </div>
        <# if ( data.description ) { #>
        <div class="elementor-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

    public function get_type(): string
    {
        return 'multi_switcher';
    }

    protected function get_default_settings()
    {
        return [
            'label_off' => esc_html_x('No', 'admin-text', 'site-reviews'),
            'label_on' => esc_html_x('Yes', 'admin-text', 'site-reviews'),
        ];
    }
}
