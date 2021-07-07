/** global: GLSR, jQuery, StarRating, wp */

import Ajax from './admin/ajax.js';
import autosize from 'autosize';
import ColorPicker from './admin/color-picker.js';
import Filters from './admin/filters.js';
import Forms from './admin/forms.js';
import Metabox from './admin/metabox.js';
import Notices from './admin/notices.js';
import Pinned from './admin/pinned.js';
import Pointers from './admin/pointers.js';
import Prism from 'prismjs';
import Search from './admin/search.js';
import Sections from './admin/sections.js';
import Shortcode from './admin/shortcode.js';
import StarRating from 'star-rating.js';
import Status from './admin/status.js';
import Sync from './admin/sync.js';
import Tabs from './admin/tabs.js';
import TextareaResize from './admin/textarea-resize.js';
import Tools from './admin/tools.js';
import tippy, {followCursor} from 'tippy.js';

GLSR.autosize = autosize;
GLSR.keys = {
    ALT: 18,
    DOWN: 40,
    ENTER: 13,
    ESC: 27,
    SPACE: 32,
    UP: 38,
};

GLSR.Tippy = { tippy, plugins: { followCursor }}

function discover_site_reviews () {
    jQuery('.post-type-site-review.edit-php .page-title-action').after('<a href="' + GLSR.addonsurl + '" class="page-title-action" style="color:#DC3232;">' + GLSR.text.discover + '</a>');
}
function rate_site_reviews () {
    let rateUrl = 'https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post';
    let rateMsg = GLSR.text.rate;
    let url1 = '<strong>Site Reviews</strong> <a href="' + rateUrl + '" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>';
    let url2 = '<a href="' + rateUrl + '" target="_blank">wordpress.org</a>';
    jQuery('#footer-left', '.post-type-site-review, .post-type-site-review-form, .post-type-site-review-theme, .dashboard_page_site-reviews-welcome').html(rateMsg.replace('%s', url1).replace('%s', url2));
}

jQuery(function ($) {
    Prism.highlightAll();
    GLSR.notices = new Notices();
    GLSR.shortcode = new Shortcode('.glsr-mce');
    GLSR.stars = new StarRating('select.glsr-star-rating', { tooltip: false });
    GLSR.Tippy.tippy('.glsr-tooltip', {appendTo: () => document.body});
    ColorPicker();
    new Filters();
    new Forms('form.glsr-form');
    new Metabox();
    new Pinned();
    new Pointers();
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
    new Search('#glsr-search-translations', {
        action: 'search-translations',
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
    new Status('a.glsr-toggle-status');
    new Sections(); // this comes before Tabs
    new Tabs();
    new TextareaResize();
    new Tools();
    new Sync();

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

    $('.post-type-site-review #the-list').on('click', '.editinline', function() {
        var row = $(this).closest('tr');
        $(':input[data-name="post_content"]').val('');
        $(':input[name="_response"]').val('');
        setTimeout(function () {
            $(':input[data-name="post_content"]').val(row.find('._post_content').text());
            $(':input[name="_response"]').val(row.find('._response').text());
        }, 50);
    });

    discover_site_reviews();
    rate_site_reviews();
});
