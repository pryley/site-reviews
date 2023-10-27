/** global: GLSR, jQuery */
/* jshint -W014 */

import Ajax from '@/admin/ajax.js';

const Sync = function () {
    this.button = jQuery('button#sync-reviews');
    this.progressbar = jQuery('.glsr-progress');
    this.service = null;
    jQuery('form.glsr-form-sync').on('click', '#sync-reviews', this.onSync_.bind(this));
    jQuery(document).on('wp-window-resized', this.onWindowResize_);
    jQuery(window).on('hashchange', this.onWindowResize_);
    this.onWindowResize_();
};

Sync.prototype = {
    finishSync_: function (response) {
        jQuery('.service-' + this.service + ' td.column-last_sync').text(response.last_sync);
        jQuery('.service-' + this.service + ' td.column-total_fetched a').text(response.total);
        this.watchSyncStatus_(false);
    },

    onSync_: function (ev) {
        ev.preventDefault();
        this.service = jQuery('[name="'+GLSR.nameprefix+'[service]"]').val();
        if (this.service) {
            this.watchSyncStatus_(true);
            this.syncFetch_();
        }
    },

    onWindowResize_: function () {
        var width = jQuery('.glsr-progress').width();
        if (!width) return;
        jQuery('.glsr-progress span').width(width);
    },

    syncFetch_: function () {
        var data = {
            _action: 'sync-reviews',
            service: this.service,
            stage: 'fetch',
        };
        (new Ajax(data)).post(this.syncProgress_.bind(this));
    },

    syncProgress_: function (response) {
        var data = {
            _action: 'sync-reviews',
            job_id: response.job_id,
            service: this.service,
            stage: 'progress',
        };
        var callback = !response.finished
            ? this.syncProgress_.bind(this)
            : this.syncReviews_.bind(this, response);
        this.updateMessage_(response.message);
        this.updateProgress_(response.percent);
        setTimeout(function () {
            (new Ajax(data)).post(callback);
        }, 1500);
    },

    syncReviews_: function (response) {
        var page = 0;
        try {
            page = response.meta.pagination.current_page;
        } catch (e) {}
        var data = {
            _action: 'sync-reviews',
            page: page + 1,
            service: this.service,
            stage: 'reviews',
        };
        this.updateMessage_(response.message);
        if (response.percent_synced && response.percent_synced >= 100) {
            this.finishSync_(response);
            return;
        }
        (new Ajax(data)).post(this.syncReviews_.bind(this));
    },

    updateMessage_: function (text) {
        jQuery('.glsr-progress-status', this.progressbar).text(text);
    },

    updateProgress_: function (percent) {
        percent = (percent || 0) + '%';
        jQuery('.glsr-progress-bar', this.progressbar).outerWidth(percent);
    },

    watchSyncStatus_: function (run) {
        if (run === true) {
            this.updateMessage_(this.progressbar.data('active-text'));
            this.updateProgress_();
            this.button.prop('disabled', true);
            window.requestAnimationFrame(function () {
                this.progressbar.addClass('active');
            }.bind(this));
        }
        if (run === false) {
            this.service = null;
            this.button.prop('disabled', false);
            this.progressbar.removeClass('active');
            return;
        }
        window.requestAnimationFrame(this.watchSyncStatus_.bind(this));
    },
};

export default Sync;
