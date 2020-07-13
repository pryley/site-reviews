/** global: GLSR */
;(function () {
    'use strict';

    GLSR.Excerpts = function (el) { // HTMLElement
        this.init_(el || document);
    };

    GLSR.Excerpts.prototype = {
        config: {
            hiddenClass: 'glsr-hidden',
            hiddenTextSelector: '.glsr-hidden-text',
            readMoreClass: 'glsr-read-more',
            visibleClass: 'glsr-visible',
        },

        /** @return void */
        createLinks_: function (el) { // HTMLElement
            var readMoreSpan = document.createElement('span');
            var readmoreLink = document.createElement('a');
            readmoreLink.setAttribute('href', '#');
            readmoreLink.setAttribute('data-text', el.getAttribute('data-show-less'));
            readmoreLink.innerHTML = el.getAttribute('data-show-more');
            readmoreLink.addEventListener('click', this.onClick_.bind(this));
            readMoreSpan.setAttribute('class', this.config.readMoreClass);
            readMoreSpan.appendChild(readmoreLink);
            el.parentNode.insertBefore(readMoreSpan, el.nextSibling);
        },

        /** @return void */
        onClick_: function (ev) { // MouseEvent
            ev.preventDefault();
            var el = ev.currentTarget;
            var hiddenNode = el.parentNode.previousSibling;
            var text = el.getAttribute('data-text');
            hiddenNode.classList.toggle(this.config.hiddenClass);
            hiddenNode.classList.toggle(this.config.visibleClass);
            el.setAttribute('data-text', el.innerText);
            el.innerText = text;
        },

        init_: function (el) { // HTMLElement
            var excerpts = el.querySelectorAll(this.config.hiddenTextSelector);
            for (var i = 0; i < excerpts.length; i++) {
                this.createLinks_(excerpts[i]);
            }
        },
    };
})();
