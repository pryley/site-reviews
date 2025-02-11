/** global: GLSR, jQuery, StarRating, wp */

import Ajax from '@/admin/ajax.js';
import autosize from 'autosize';
import ColorPicker from '@/admin/color-picker.js';
import Event from '@/public/event.js';
import Filter from '@/admin/filter.js';
import Filters from '@/admin/filters.js';
import Flyoutmenu from '@/admin/flyoutmenu.js';
import Forms from '@/admin/forms.js';
import Import from '@/admin/import.js';
import Metabox from '@/admin/metabox.js';
import Notices from '@/admin/notices.js';
import Pointers from '@/admin/pointers.js';
import Prism from 'prismjs';
import PublishAction from '@/admin/publish-action.js';
import Search from '@/admin/search.js';
import Sections from '@/admin/sections.js';
import Shortcode from '@/admin/shortcode.js';
import StarRating from '@/public/starrating.js';
import Status from '@/admin/status.js';
import Tabs from '@/admin/tabs.js';
import TextareaResize from '@/admin/textarea-resize.js';
import TogglePinned from '@/admin/toggle-pinned.js';
import ToggleVerified from '@/admin/toggle-verified.js';
import Tools from '@/admin/tools.js';
import tippy, { followCursor } from 'tippy.js';

GLSR.ajax = Ajax;
GLSR.autosize = autosize;
GLSR.keys = {
    ALT: 18,
    DOWN: 40,
    ENTER: 13,
    ESC: 27,
    SPACE: 32,
    TAB: 9,
    UP: 38,
};

GLSR.Event = Event;
GLSR.stars = StarRating();
GLSR.Tippy = { tippy, plugins: { followCursor }}

Prism.languages.shortcode = {
    tag: {
        lookbehind: true,
        pattern: /^(\[)[^\s]+/i,
    },
    string: {
        greedy: true,
        pattern: /"(?:\\[\s\S]|[^\\"])*"/,
    },
    variable: {
        pattern: /([a-z_]+(?==))/i,
    },
    punctuation: {
        pattern: /[=\[\]]/i,
    },
}

function site_reviews_footer_notice () {
    if (jQuery('.glsr-notice-footer').length) {
        jQuery('#wpbody-content').addClass('has-footer-notice');
    }
}

jQuery(function ($) {
    Prism.highlightAll();
    GLSR.notices = new Notices();
    GLSR.shortcode = new Shortcode('.glsr-mce');
    GLSR.stars.init('.glsr-field-rating select', { clearable: true });

    GLSR.Tippy.tippy('.glsr-tooltip', {appendTo: () => document.body});
    $('.glsr-tooltip').each((i, el) => {
        const content = el.dataset.tippyContent;
        const syntax = el.dataset.syntax;
        if (el._tippy && content && syntax && Prism.languages[syntax]) {
            el._tippy.setContent('<pre class="language-' + syntax + '"><code>' + Prism.highlight(content, Prism.languages[syntax], syntax) + '</code></pre>')
        }
    });

    $('.glsr-nav-tab').on('click:tab', (ev, id, $view) => {
        if ('system-info' !== id || 1 === $view.data('isLoaded')) return;
        const request = wp.ajax.post(GLSR.action, {
            [GLSR.nameprefix]: {
                _action: id,
                _nonce: GLSR.nonce[id],
            },
        }).done(response => {
            $view.data('isLoaded', 1)
            $view.find('textarea').val(response.data);
            $view.find('button').removeAttr('disabled');
        }).fail((response, textStatus, errorThrown) => {
            $view.find('textarea').val(GLSR.text.system_info_failed);
            if (response?.notices) {
                GLSR.notices.error(response.notices);
                return;
            }
            const error = (500 === response.status) ? GLSR.text.system_info_500 : wp.i18n.sprintf(GLSR.text.system_info_error, response.status, response.responseText);
            GLSR.notices.error(error);
            console.error({ response, textStatus, errorThrown });
        })
    })

    ColorPicker();
    new Filters();
    $('.glsr-filter').each((index, filterEl) => {
        new Filter(filterEl);
    })
    new Flyoutmenu();
    new Forms('form.glsr-form');
    new Import();
    new Metabox();
    new Pointers();
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
    new Sections(); // this comes before Tabs
    new Tabs();
    new TextareaResize();
    new TogglePinned();
    new ToggleVerified();
    new Tools();

    var trackValue = function () {
        this.dataset.glsrTrack = this.value;
    };
    $('select[data-glsr-track]').each(trackValue);
    $('select[data-glsr-track]').on('change', trackValue);

    $('.glsr-card.postbox:not(.open)').addClass('closed')
        .find('.glsr-accordion-trigger').attr('aria-expanded', false)
        .closest('.glsr-nav-view').addClass('collapsed');

    if ($('.glsr-support-step').not(':checked').length < 1) {
        $('.glsr-card-result').removeClass('hidden');
    }

    $('.glsr-support-step').on('change', function () {
        var action = $('.glsr-support-step').not(':checked').length > 0 ? 'add' : 'remove';
        $('.glsr-card-result')[action + 'Class']('hidden');
    });

    $('.glsr-card.postbox .glsr-card-heading').on('click', function () {
        var parent = $(this).parent();
        var view = parent.closest('.glsr-nav-view');
        var action = parent.hasClass('closed') ? 'remove' : 'add';
        parent[action + 'Class']('closed').find('.glsr-accordion-trigger').attr('aria-expanded', action !== 'add');
        action = view.find('.glsr-card.postbox').not('.closed').length > 0 ? 'remove' : 'add';
        view[action + 'Class']('collapsed');
    });

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

    const $bulkActionNotice = $('#glsr-notices .bulk-action-notice').on('click', 'button.button-link', function() {
        $(this)
            .toggleClass('bulk-action-errors-collapsed')
            .attr('aria-expanded', !$(this).hasClass('bulk-action-errors-collapsed'));
        $bulkActionNotice.find('.bulk-action-errors').toggleClass('hidden');
    });

    site_reviews_footer_notice();

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

    $('.glsr-screen-meta-toggle').on('click', function () {
        let panel = $('#' + $(this).attr('aria-controls'));
        if (!panel.length) return;
        if (panel.is(':visible')) {
            screenMeta.close(panel, $(this));
            $('.glsr-screen-meta-toggle').removeClass('screen-meta-active').attr('aria-expanded', false);
        } else {
            screenMeta.open(panel, $(this));
        }
    });

    $('.shortcode-example').on('copy', ev => {
        const selection = document.getSelection();
        ev.originalEvent.clipboardData.setData('text/plain', selection.toString());
        ev.preventDefault();
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

    $('.glsr-setting-field .wp-pwd button').each((index, el) => {
        const $btn = $(el);
        $btn.on('click', () => {
            const $icon = $btn.find('.dashicons');
            const label = $icon.hasClass('dashicons-visibility') ? $btn.data('show') : $btn.data('hide');
            $btn.attr('aria-label', label)
            $icon.toggleClass('dashicons-hidden').toggleClass('dashicons-visibility')
        })
    })
});

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

const setTextDirection = (type) => {
    [].forEach.call(document.querySelectorAll(`[data-type="site-reviews/${type}"] .glsr`), el => {
        const direction = 'glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction');
        el.classList.add(direction);
    })
}

Event.on('site-reviews/form', (response, attributes) => {
    if (!_.isEmpty(response) && !response.error) {
        setTextDirection('form')
        GLSR.stars.destroy();
        GLSR.stars.init('.glsr-field-rating select', { clearable: true });
    }
});
Event.on('site-reviews/review', (response, attributes) => {
    if (!_.isEmpty(response) && !response.error) {
        setTextDirection('review')
    }
});
Event.on('site-reviews/reviews', (response, attributes) => {
    if (!_.isEmpty(response) && !response.error) {
        setTextDirection('reviews')
    }
});
Event.on('site-reviews/summary', (response, attributes) => {
    if (!_.isEmpty(response) && !response.error) {
        setTextDirection('summary')
    }
});

