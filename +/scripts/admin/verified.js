/** global: GLSR, jQuery */

import Ajax from '@/admin/ajax.js';

const Verified = function () {
    this.el =jQuery('#verified-status-select');
    if (this.el) {
        this.cancel =jQuery('a.cancel-verified-status');
        this.cancel.on('click', this.onClickCancel_.bind(this));
        this.edit =jQuery('a.edit-verified-status');
        this.edit.on('click', this.onClickEdit_.bind(this));
        this.save =jQuery('a.save-verified-status');
        this.save.on('click', this.onClickSave_.bind(this));
    }
   jQuery('td.column-is_verified i.verify-review').on('click', this.onClickToggle_.bind(this));
};

Verified.prototype = {
    restoreEditLink_: function () {
        this.el.slideUp('fast');
        this.edit.show().focus();
    },

    onClickCancel_: function (ev) { // MouseEvent
        ev.preventDefault();
        this.restoreEditLink_();
        this.el.find('select').val(jQuery('#hidden-verified-status').val() === '0' ? 1 : 0);
    },

    onClickEdit_: function (ev) { // MouseEvent
        ev.preventDefault();
        if (!this.el.is(':hidden')) return;
        this.el.slideDown('fast', function () {
            this.el.find('select').focus();
        }.bind(this));
        this.edit.hide();
    },

    onClickSave_: function (ev) { // MouseEvent
        ev.preventDefault();
        this.restoreEditLink_();
        this.target = ev.currentTarget;
        var request = {
            _action: 'toggle-verified',
            id: jQuery('#post_ID').val(),
            verified: jQuery('#verified-status').val(),
        };
        (new Ajax(request)).post(this.save_.bind(this));
    },

    onClickToggle_: function (ev) { // MouseEvent
        ev.preventDefault();
        this.target = ev.currentTarget;
        var request = {
            _action: 'toggle-verified',
            id: ev.currentTarget.getAttribute('data-id'),
            verified:-1,
        };
       jQuery(this.target).addClass('spinner is-active').removeClass('dashicons-sticky');
        (new Ajax(request)).post(this.toggleVerified_.bind(this));
    },

    save_: function (response) {
       jQuery('#verified-status').val(!response.verified|0);
       jQuery('#hidden-verified-status').val(response.verified|0);
       jQuery('#verified-status-text').text(response.verified ? this.target.dataset.yes : this.target.dataset.no);
        GLSR.notices.add(response.notices);
    },

    toggleVerified_: function (response) {
        this.target.classList[response.verified ? 'add' : 'remove']('verified');
       jQuery(this.target).removeClass('spinner is-active').addClass('dashicons-sticky');
    },
};

export default Verified;
