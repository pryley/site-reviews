/** global: GLSR */

import Button from './button.js';
import Serializer from './serializer.js';

const Ajax = function (request, ev, form) { // object
    this.event = ev || null;
    this.form = form || null;
    this.notice = null;
    this.request = request || {};
};

Ajax.prototype = {
    post: function (callback) { // function|void
        if (this.event) {
            this.postFromEvent_(callback);
        } else {
            this.doPost_(callback);
        }
    },

    buildData_: function (el) { // HTMLElement|null
        var data = {
            action: GLSR.action,
            _ajax_request: true,
        };
        if (this.form) {
            var formdata = new Serializer(this.form);
            if (formdata[GLSR.nameprefix]) {
                this.request = formdata[GLSR.nameprefix];
            }
        }
        this.buildNonce_(el);
        data[GLSR.nameprefix] = this.request;
        return data;
    },

    buildNonce_: function (el) { // HTMLElement|null
        if (this.request._nonce) return;
        if (GLSR.nonce[this.request._action]) {
            this.request._nonce = GLSR.nonce[this.request._action];
            return;
        }
        if (!el) return;
        this.request._nonce = el.closest('form').find('#_wpnonce').val();
    },

    doPost_: function (callback, el) {
        if (el) {
            Button(el).loading()
        }
        // wp.ajax.post(GLSR.action, this.buildData_(el)).done(response => {
        jQuery.post(GLSR.ajaxurl, this.buildData_(el)).done(response => {
            if (typeof callback === 'function') {
                callback(response.data, response.success);
            }
        }).always(response => {
            if (!response.data) {
                GLSR.notices.error('Unknown error.'); // triggers scroll
            }
            else if (response.data.notices) {
                GLSR.notices.add(response.data.notices); // triggers scroll
            }
            if (el) {
                Button(el).loaded()
            }
        });
    },

    postFromEvent_: function (callback) { // Event, function|void
        this.event.preventDefault();
        this.doPost_(callback, jQuery(this.event.currentTarget));
    },
};

export default Ajax;
