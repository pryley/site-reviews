/** global: GLSR, jQuery */

import Ajax from './ajax.js';

const Pinned = function () {
    this.el =jQuery('#pinned-status-select');
    if (this.el) {
        this.cancel =jQuery('a.cancel-pinned-status');
        this.cancel.on('click', this.onClickCancel_.bind(this));
        this.edit =jQuery('a.edit-pinned-status');
        this.edit.on('click', this.onClickEdit_.bind(this));
        this.save =jQuery('a.save-pinned-status');
        this.save.on('click', this.onClickSave_.bind(this));
    }
   jQuery('td.column-is_pinned i.pin-review').on('click', this.onClickToggle_.bind(this));
};

Pinned.prototype = {
    /** @return void */
    restoreEditLink_: function () {
        this.el.slideUp('fast');
        this.edit.show().focus();
    },

    /** @return void */
    onClickCancel_: function (ev) { // MouseEvent
        ev.preventDefault();
        this.restoreEditLink_();
        this.el.find('select').val(jQuery('#hidden-pinned-status').val() === '0' ? 1 : 0);
    },

    /** @return void */
    onClickEdit_: function (ev) { // MouseEvent
        ev.preventDefault();
        if (!this.el.is(':hidden')) return;
        this.el.slideDown('fast', function () {
            this.el.find('select').focus();
        }.bind(this));
        this.edit.hide();
    },

    /** @return void */
    onClickSave_: function (ev) { // MouseEvent
        ev.preventDefault();
        this.restoreEditLink_();
        this.target = ev.currentTarget;
        var request = {
            _action: 'toggle-pinned',
            id:jQuery('#post_ID').val(),
            pinned:jQuery('#pinned-status').val(),
        };
        (new Ajax(request)).post(this.save_.bind(this));
    },

    /** @return void */
    onClickToggle_: function (ev) { // MouseEvent
        ev.preventDefault();
        this.target = ev.currentTarget;
        var request = {
            _action: 'toggle-pinned',
            id: ev.currentTarget.getAttribute('data-id'),
            pinned:-1,
        };
       jQuery(this.target).addClass('spinner is-active').removeClass('dashicons-sticky');
        (new Ajax(request)).post(this.togglePinned_.bind(this));
    },

    /** @return void */
    save_: function (response) {
       jQuery('#pinned-status').val(!response.pinned|0);
       jQuery('#hidden-pinned-status').val(response.pinned|0);
       jQuery('#pinned-status-text').text(response.pinned ? this.target.dataset.yes : this.target.dataset.no);
        GLSR.notices.add(response.notices);
    },

    /** @return void */
    togglePinned_: function (response) {
        this.target.classList[response.pinned ? 'add' : 'remove']('pinned');
       jQuery(this.target).removeClass('spinner is-active').addClass('dashicons-sticky');
    },
};

export default Pinned;
