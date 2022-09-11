<?php defined('ABSPATH') || exit; ?>

<div id="glsr-dp-overlay" tabindex="0" role="dialog" aria-label="{{ data.l10n.overlayLabel }}"></div>
<script id="tmpl-glsr-deativate" type="text/template">
    <div class="glsr-dp-backdrop"></div>
    <div class="glsr-dp-wrap" role="document">
        <div class="glsr-dp-header">
            <h3>{{{ data.l10n.dialogTitle.replace('%s', data.name) }}}</h3>
            <button class="close dashicons dashicons-no">
                <span class="screen-reader-text">{{{ data.l10n.closeDialog }}}</span>
            </button>
        </div>
        <div class="glsr-dp-content">
            <p>{{{ data.l10n.dialogText }}}</p>
            <# if (data.reasons) { #>
                <ul class="glsr-dp-reasons">
                    <# _.each(data.reasons, function (reason) { #>
                        <li>
                            <label class="glsr-dp-reason" data-placeholder="{{ reason.placeholder }}" tabindex="0">
                                <input type="radio" name="reason" value="{{ reason.id }}">
                                <span class="glsr-dp-icon">{{{ reason.icon }}}</span>
                                <span class="glsr-dp-reason">{{{ reason.text }}}</span>
                            </label>
                        </li>
                    <# }); #>
                </ul>
                <div class="glsr-dp-details" style="display:none;">
                    <textarea name="details" placeholder="" rows="3"></textarea>
                </div>
            <# } #>
            <# if (data.insight) { #>
                <p>
                    <button type="button" class="button-link expand-info" aria-controls="glsr-dp-info" aria-expanded="false">{{{ data.l10n.clickHere }}}</button> {{{ data.l10n.dialogTextExtra.replace('%s', '') }}}
                </p>
                <div id="glsr-dp-info" class="glsr-dp-info" hidden>
                    <table class="widefat striped" role="presentation">
                        <tbody>
                            <# _.each(data.insight, function (value, label) { #>
                                <tr>
                                    <td>{{{ label }}}</td>
                                    <td>{{{ value }}}</td>
                                </tr>
                            <# }); #>
                        </tbody>
                    </table>
                </div>
            <# } #>
        </div>
        <div class="glsr-dp-footer">
            <a href="{{ data.action }}" class="components-button deactivate">{{{ data.l10n.buttonDeactivate }}}</a>
            <button class="components-button is-primary submit">{{{ data.l10n.buttonSubmit }}}</button>
        </div>
    </div>
</script>
