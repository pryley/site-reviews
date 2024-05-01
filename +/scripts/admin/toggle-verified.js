import Ajax from '@/admin/ajax.js';

class ToggleVerified {
    constructor () {
        jQuery('td.column-is_verified i.verify-review').on('click', this.toggle)
    }

    toggle (ev) {
        ev.preventDefault()
        const $el = jQuery(this);
        const data = {
            _action: 'toggle-verified',
            verified: -1,
            post_id: $el.data('id'),
        };
        $el.addClass('spinner is-active').removeClass('dashicons-sticky');
        (new Ajax(data)).post(response => {
            $el[1 === response.value ? 'addClass' : 'removeClass']('verified')
            $el.removeClass('spinner is-active').addClass('dashicons-sticky')
        })
    }
}

export default ToggleVerified;
