/** global: GLSR, jQuery */

import Ajax from './ajax.js';

const Status = function (selector) {
    var elements = document.querySelectorAll(selector);
    if (!elements.length) return;
    elements.forEach(function (el) {
        el.addEventListener('click', this.onClick_);
    }.bind(this));
};

Status.prototype = {
    /** @return void */
    onClick_: function (ev) { // MouseEvent
        var post_id = ev.currentTarget.href.match(/post=([0-9]+)/);
        var status = ev.currentTarget.href.match(/action=([a-z]+)/);
        if (post_id === null || status === null) return;
        var request = {
            _action: 'toggle-status',
            _nonce: GLSR.nonce['toggle-status'],
            post_id: post_id[1],
            status: status[1],
        };
        (new Ajax(request, ev)).post(function (response) {
            if (!response.class) return;
            var el = jQuery(ev.target);
            el.closest('tr').removeClass('status-pending status-publish').addClass(response.class);
            el.closest('td.column-title').find('strong').html(response.link);
            if (!response.counts) return;
            el.closest('.wrap').find('ul.subsubsub').html(response.counts);
            jQuery('#menu-posts-site-review')
                .find('.awaiting-mod')
                .removeClass()
                .addClass('awaiting-mod count-' + response.pending)
                .find('.unapproved-count')
                .html(response.pending);
        });
    },
};

export default Status;
