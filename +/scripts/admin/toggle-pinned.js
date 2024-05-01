import Ajax from '@/admin/ajax.js';

class TogglePinned {
    constructor () {
        jQuery('td.column-is_pinned i.pin-review').on('click', this.toggle)
    }

    toggle (ev) {
        ev.preventDefault()
        const $el = jQuery(this);
        const data = {
            _action: 'toggle-pinned',
            pinned: -1,
            post_id: $el.data('id'),
        };
        $el.addClass('spinner is-active').removeClass('dashicons-sticky');
        (new Ajax(data)).post(response => {
            $el[1 === response.value ? 'addClass' : 'removeClass']('pinned')
            $el.removeClass('spinner is-active').addClass('dashicons-sticky')
        })
    }
}

export default TogglePinned;
