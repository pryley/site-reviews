/** global: GLSR */

import config from '@/public/config.js';
import Button from '@/admin/button.js';
import Serializer from '@/admin/serializer.js';

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
            action: config.request.ajax.action,
            _ajax_request: true,
        };
        if (this.form) {
            var formdata = new Serializer(this.form);
            if (formdata[config.nameprefix]) {
                this.request = formdata[config.nameprefix];
            }
        }
        this.buildNonce_(el);
        data[config.nameprefix] = this.request;
        return data;
    },

    buildNonce_: function (el) { // HTMLElement|null
        if (this.request._nonce) return;
        if (config.nonce[this.request._action]) {
            this.request._nonce = config.nonce[this.request._action];
            return;
        }
        if (!el) return;
        this.request._nonce = el.closest('form').find('#_wpnonce').val();
    },

    doPost_: function (callback, el) {
        if (el) {
            Button(el).loading()
        }
        // wp.ajax.post(config.request.ajax.action, this.buildData_(el)).done(response => {
        jQuery.post(ajaxurl, this.buildData_(el)).done(response => {
            if (typeof callback === 'function') {
                callback(response.data, response.success);
            }
        }).always(response => {
            if (response?.data?.notices) {
                GLSR.Notice.add(response.data.notices); // triggers scroll
            } else if (!response.success) {
                GLSR.Notice.error('Unknown error.'); // triggers scroll
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
