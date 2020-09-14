/** global: GLSR, jQuery */

const Pointers = function () {
    jQuery.each(GLSR.pointers, function (i, pointer) {
        this.init_(pointer);
    }.bind(this));
};

Pointers.prototype = {
    /** @return void */
    close_: function (pointerId) { // string
        jQuery.post(GLSR.ajaxurl, {
            action: 'dismiss-wp-pointer',
            pointer: pointerId,
        });
    },

    /** @return void */
    init_: function (pointer) { // object
        jQuery(pointer.target).pointer({
            content: pointer.options.content,
            position: pointer.options.position,
            close: this.close_.bind(null, pointer.id),
        })
        .pointer('open')
        .pointer('sendToTop');
        jQuery(document).on('wp-window-resized', function () {
            jQuery(pointer.target).pointer('reposition');
        });
    },
};

export default Pointers;
