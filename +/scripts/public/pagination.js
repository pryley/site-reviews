/** global: GLSR */
;(function () {
    'use strict';

    var GLSR_Pagination = function (paginationEl, reviewsEl) { // HTMLElement, HTMLElement
        this.paginationEl = paginationEl;
        this.reviewsEl = reviewsEl;
        this.initEvents_();
    };

    GLSR_Pagination.prototype = {
        config: {
            hideClass: 'glsr-hide',
            linkSelector: 'a.page-numbers',
            scrollTime: 468,
        },

        /** @return DOMElement|null */
        dataEl_: function () {
            var dataEl = document.getElementById(this.paginationEl.dataset.id);
            if (dataEl) {
                return dataEl
            }
            return this.reviewsEl;
        },

        /** @return void */
        handleResponse_: function (location, response, success) { // string, string
            if (!success) {
                window.location = location;
                return;
            }
            this.paginationEl.innerHTML = response.pagination;
            this.reviewsEl.innerHTML = response.reviews;
            this.scrollToTop_(this.reviewsEl);
            this.paginationEl.classList.remove(this.config.hideClass);
            this.reviewsEl.classList.remove(this.config.hideClass);
            this.initEvents_();
            if (GLSR.urlparameter) {
                window.history.pushState(null, '', location);
            }
            new GLSR.Excerpts(this.reviewsEl);
        },

        /** @return void */
        initEvents_: function () {
            var links = this.paginationEl.querySelectorAll(this.config.linkSelector);
            for (var i = 0; i < links.length; i++) {
                links[i].addEventListener('click', this.onClick_.bind(this));
            }
        },

        /** @return void */
        onClick_: function (ev) { // MouseEvent
            var dataEl = this.dataEl_();
            if (!dataEl) {
                console.log('pagination config not found.');
                return;
            }
            var data = {};
            for (var key of Object.keys(dataEl.dataset)) {
                data[GLSR.nameprefix + '[atts][' + key + ']'] = dataEl.dataset[key];
            }
            data[GLSR.nameprefix + '[_action]'] = 'fetch-paged-reviews';
            data[GLSR.nameprefix + '[page]'] = ev.currentTarget.dataset.page || '';
            data[GLSR.nameprefix + '[url]'] = ev.currentTarget.href || '';
            this.paginationEl.classList.add(this.config.hideClass);
            this.reviewsEl.classList.add(this.config.hideClass);
            ev.preventDefault();
            (new GLSR.Ajax()).post(data, this.handleResponse_.bind(this, ev.currentTarget.href));
        },

        /** @return void */
        scrollToTop_: function (el, offset) { // HTMLElement, int
            offset = offset || 16; // 1rem
            var fixedElement;
            for (var i = 0; i < GLSR.ajaxpagination.length; i++) {
                fixedElement = document.querySelector(GLSR.ajaxpagination[i]);
                if (!fixedElement || window.getComputedStyle(fixedElement).getPropertyValue('position') !== 'fixed') continue;
                offset = offset + fixedElement.clientHeight;
            }
            var clientBounds = el.getBoundingClientRect();
            var offsetTop = clientBounds.top - offset;
            if (offsetTop > 0) return; // if top is in view, don't scroll!
            this.scrollToTopStep_({
                endY: offsetTop,
                offset: window.pageYOffset,
                startTime: window.performance.now(),
                startY: el.scrollTop,
            });
        },

        /** @return void */
        scrollToTopStep_: function (context) { // object
            var elapsed = (window.performance.now() - context.startTime) / this.config.scrollTime;
            elapsed = elapsed > 1 ? 1 : elapsed;
            var easedValue = 0.5 * (1 - Math.cos(Math.PI * elapsed));
            var currentY = context.startY + (context.endY - context.startY) * easedValue;
            window.scroll(0, context.offset + currentY); // @todo what is this for again?
            if (currentY !== context.endY) {
                window.requestAnimationFrame(this.scrollToTopStep_.bind(this, context));
            }
        },
    };

    GLSR.Pagination = function () {
        this.navs = [];
        var pagination = document.querySelectorAll('.glsr-ajax-pagination');
        if (!pagination.length) return;
        pagination.forEach(function (paginationEl) {
            var reviewsEl = document.querySelector('[data-reviews][data-id=' + paginationEl.dataset.id);
            if (reviewsEl) {
                this.navs.push(new GLSR_Pagination(paginationEl, reviewsEl));
            }
        }.bind(this));
    };
})();
