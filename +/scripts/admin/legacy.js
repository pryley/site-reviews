/** global: GLSR, jQuery, wp */

/**
 * The screen modules not yet rewritten on the admin standard, run as the old entry ran them.
 */
import { config } from '@/public/core/index.js';
import Import from '@/admin/import.js';
import Tools from '@/admin/tools.js';

export default () => {
    if ('function' !== typeof jQuery) return;
    const $ = jQuery;

    $('.glsr-nav-tab').on('site-reviews/tab', (ev) => {
        const { id, view } = ev.originalEvent.detail;
        const $view = $(view);
        if ('system-info' !== id || 1 === $view.data('isLoaded')) return;
        const request = wp.ajax.post(config.request.ajax.action, {
            [config.nameprefix]: {
                _action: id,
                _nonce: config.nonce[id],
            },
        }).done(response => {
            $view.data('isLoaded', 1)
            $view.find('textarea').val(response.data);
            $view.find('button').removeAttr('disabled');
        }).fail((response, textStatus, errorThrown) => {
            $view.find('textarea').val(config.text.systemInfoFailed);
            if (response?.notices) {
                GLSR.Notice.error(response.notices);
                return;
            }
            const error = (500 === response.status) ? config.text.systemInfo500 : wp.i18n.sprintf(config.text.systemInfoError, response.status, response.responseText);
            GLSR.Notice.error(error);
            console.error({ response, textStatus, errorThrown });
        })
    })

    new Import();
    new Tools();

    $('.glsr-videos__playlist a').on('click', function () {
        const playlist = $(this).closest('.glsr-videos__playlist');
        const style = window.getComputedStyle(playlist[0]);
        const transform = style.transform || style.webkitTransform || style.mozTransform;
        if (!~['none','matrix(1, 0, 0, 1, 0, 0)'].indexOf(transform)) {
            // do nothing if the playlist is collapsed, this allows touch devices to expand the playlist without triggering the link
            return false;
        }
        playlist.find('a').removeClass('is-active')
        $(this).addClass('is-active')
        loadYouTube($(this));
        return false; // preventDefault and stopPropagation
    });

    $('.glsr-youtube-button').on('click', function () {
        loadYouTube($(this));
    });

    $('#wpbody').on('copy', ev => {
        const $el = $(ev.originalEvent.target);
        if ($el.closest('.shortcode-example,.tippy-content').length) {
            const selection = document.getSelection();
            ev.originalEvent.clipboardData.setData('text/plain', selection.toString());
            ev.preventDefault();
        }
    })

};

const loadYouTube = function (link) {
    let id = link.data('id');
    let iframe = jQuery(document.createElement('iframe'));
    iframe.attr('frameborder', '0');
    iframe.attr('allowfullscreen', '');
    iframe.attr('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
    if (id.length > 12) {
        iframe.attr('src', 'https://www.youtube-nocookie.com/embed/videoseries?list='+ id +'&rel=0&showinfo=0&autoplay=1&modestbranding=1');
    } else {
        iframe.attr('src', 'https://www.youtube-nocookie.com/embed/'+ id +'?rel=0&showinfo=0&autoplay=1&modestbranding=1');
    }
    let ytFrame = link.closest('.glsr-videos').find('.glsr-youtube');
    ytFrame.find('iframe').remove()
    ytFrame.prepend(iframe);
}
