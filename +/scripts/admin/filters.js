/** global: GLSR, jQuery */

import Ajax from '@/admin/ajax.js';

const Filters = function () {
    this.request = null;
    jQuery('.enable-filter-tog', '#adv-settings').on('click', this.onClick_.bind(this));
};

Filters.prototype = {
    enabled_: function() {
        return jQuery('.enable-filter-tog:checked').map((i, el) => el.value).get();
    },

    onClick_: function (ev) { // MouseEvent
        const el = jQuery(ev.currentTarget);
        const action = el.prop('checked') ? 'removeClass' : 'addClass';
        jQuery('#glsr-filter-by-' + el.val())[action]('hidden'); // @compat with other WP filters
        jQuery('#glsr-filter-by-' + el.val())[action]('is-hidden');
        this.saveState_();
    },

    saveState_: _.debounce(function () {
        if (this.request) {
            this.request.abort();
        }
        const data = {
            _ajax_request: true,
        };
        data[GLSR.nameprefix] = {
            _action: 'toggle-filters',
            _nonce: GLSR.nonce['toggle-filters'],
            enabled: this.enabled_(),
        };
        this.request = wp.ajax.post(GLSR.action, data);
        this.request.always(function () {
            this.request = null
        }.bind(this));
    }, 500),
};

export default Filters;
