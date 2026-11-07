/** global: GLSR, jQuery, wp */

/**
 * The screen modules not yet rewritten on the admin standard, run as the old entry ran them.
 */
import { config } from '@/public/core/index.js';
import ColorPicker from '@/admin/color-picker.js';
import Filter from '@/admin/filter.js';
import Filters from '@/admin/filters.js';
import Forms from '@/admin/forms.js';
import Import from '@/admin/import.js';
import Metabox from '@/admin/metabox.js';
import PremiumLicense from '@/admin/premium-license.js';
import PublishAction from '@/admin/publish-action.js';
import Search from '@/admin/search.js';
import Status from '@/admin/status.js';
import TogglePinned from '@/admin/toggle-pinned.js';
import ToggleVerified from '@/admin/toggle-verified.js';
import Tools from '@/admin/tools.js';
import tippy from 'tippy.js';

export default () => {
    if ('function' !== typeof jQuery) return;
    const $ = jQuery;

    $('.glsr-setting-field button.is-reset-button').click(ev => {
        const btn = $(ev.currentTarget);
        const textarea = btn.prev('textarea.autosized');
        textarea.val(btn.data('default'))
    })

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

    ColorPicker();
    new Filters();
    $('.glsr-filter').each((index, filterEl) => {
        new Filter(filterEl);
    })
    new Forms('form.glsr-form');
    new Import();
    new Metabox();
    new PublishAction();
    new Search('#glsr-search-posts', {
        action: 'search-posts',
        onInit: function () {
            this.el.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
        },
        onResultClick: function (ev) {
            var result = $(ev.currentTarget);
            var template = wp.template('glsr-assigned-posts');
            var entry = {
                id: result.data('id'),
                name: 'post_ids[]',
                url: result.data('url'),
                title: result.text(),
            };
            if (template) {
                var entryEl = $(template(entry));
                entryEl.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
                this.el.find('.glsr-selected-entries').append(entryEl);
                this.reset_();
            }
            this.options.searchEl.focus();
        },
    });
    new Search('#glsr-search-translations', {
        action: 'search-strings',
        onInit: function () {
            this.makeSortable_();
        },
        onResultClick: function (ev) {
            var result = $(ev.currentTarget);
            var entry = result.data('entry');
            var template = wp.template('glsr-string-' + (entry.p1 ? 'plural' : 'single'));
            entry.index = this.options.entriesEl.children().length;
            entry.prefix = this.options.resultsEl.data('prefix');
            if (template) {
                this.options.entriesEl.append(template(entry));
                this.options.exclude.push({ id: entry.id });
                this.options.results = this.options.results.filter(function (i, el) {
                    return el !== result.get(0);
                });
            }
            this.setVisibility_();
        },
    });
    new Search('#glsr-search-users', {
        action: 'search-users',
        onInit: function () {
            this.el.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
        },
        onResultClick: function (ev) {
            var result = $(ev.currentTarget);
            var template = wp.template('glsr-assigned-users');
            var entry = {
                id: result.data('id'),
                name: 'user_ids[]',
                url: result.data('url'),
                title: result.text(),
            };
            if (template) {
                var entryEl = $(template(entry));
                entryEl.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
                this.el.find('.glsr-selected-entries').append(entryEl);
                this.reset_();
            }
            this.options.searchEl.focus();
        },
    });
    new Status('a.glsr-toggle-status');
    new PremiumLicense();
    new TogglePinned();
    new ToggleVerified();
    new Tools();

    var trackValue = function () {
        this.dataset.glsrTrack = this.value;
    };
    $('select[data-glsr-track]').each(trackValue);
    $('select[data-glsr-track]').on('change', trackValue);

    if ('undefined' !== typeof inlineEditTax && $('.taxonomy-site-review-category').length) {
        let wp_inlineEditTax = inlineEditTax.edit;
        inlineEditTax.edit = function (id) {
            wp_inlineEditTax.apply(this, arguments);
            if ('object' === typeof(id)) {
                id = this.getId(id);
            }
            let row = $(this.what+id);
            let val = $('td.term_priority', row).text();
            $('.wp-list-table :input[name="term_priority"]').val(val);
        }
    }
    if ('undefined' !== typeof inlineEditPost) {
        $('.post-type-site-review #the-list').on('click', '.editinline', function() {
            var row = $(this).closest('tr');
            $(':input[data-name="post_content"]').val('');
            $(':input[name="_response"]').val('');
            setTimeout(function () {
                $(':input[data-name="post_content"]').val(row.find('._post_content').text());
                $(':input[name="_response"]').val(row.find('._response').text());
            }, 50);
        });
        // allow Enter keypress in the response textarea
        setTimeout(function () {
            $('td', '.post-type-site-review #inline-edit').off('keydown');
        }, 50);
    }

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

    const addTextAtCursorPosition = (textarea, cursorPosition, text) => {
        const front = (textarea.value).substring(0, cursorPosition);
        const back = (textarea.value).substring(textarea.selectionEnd, textarea.value.length);
        const value = front + text + back;
        textarea.focus();
        if (!document.execCommand('selectAll', false, null) || !document.execCommand('insertText', false, value)) {
            textarea.value = value; // fallback to this which does not support undo
        }
    };

    const updateCursorPosition = (cursorPosition, text, textarea) => {
        cursorPosition = cursorPosition + text.length;
        textarea.selectionStart = cursorPosition;
        textarea.selectionEnd = cursorPosition;
        textarea.focus();
    };

    $('.glsr-template-editor input').on('click', ev => {
        ev.preventDefault();
        const textarea = $(ev.target).closest('.glsr-template-editor').find('textarea')[0];
        const cursorPosition = textarea.selectionStart;
        const scrollPos = textarea.scrollTop;
        const text = '{'+ev.target.dataset.tag+'}';
        addTextAtCursorPosition(textarea, cursorPosition, text);
        updateCursorPosition(cursorPosition, text, textarea);
        textarea.scrollTop = scrollPos;
    })

    // Delegated: the License Key row is redrawn by its routes.
    $(document).on('click', '.glsr-setting-field .wp-pwd button', (ev) => {
        const $btn = $(ev.currentTarget);
        const $icon = $btn.find('.dashicons');
        const label = $icon.hasClass('dashicons-visibility') ? $btn.data('hide') : $btn.data('show');
        $btn.attr('aria-label', label)
        tippy($btn.get(0)).setContent(label);
        $icon.toggleClass('dashicons-hidden').toggleClass('dashicons-visibility')
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
