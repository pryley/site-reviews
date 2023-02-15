class Import {
    constructor () {
        jQuery('form').on('click', '[data-ajax-import]', this.onImport.bind(this));
        this.reset()
    }

    data (overrides) {
        const body = new FormData(this.$el.closest('form').get(0));
        for (let key in overrides) {
            body.set(key, overrides[key]);
        }
        body.set('action', GLSR.action);
        return {
            body,
            credentials: 'same-origin',
            method: 'POST',
        }
    }

    import (page) {
        const options = this.data({
            [GLSR.nameprefix + '[page]']: page,
            [GLSR.nameprefix + '[stage]']: 'import',
        });
        return fetch(wp.ajax.settings.url, options)
            .then(response => response.json())
            .then(response => {
                this.processed += response.data.processed;
            })
            .catch(error => {
                console.error(error)
            })
    }

    isBusy () {
        this.$el.addClass('is-busy');
        this.$el.prop('disabled', true);
    }

    isIdle () {
        this.$el.removeClass('is-busy');
        this.$el.prop('disabled', false);
    }

    onImport (ev) {
        ev.preventDefault()
        this.$el = jQuery(ev.currentTarget);
        this.isBusy()
        const options = this.data({
            [GLSR.nameprefix + '[stage]']: 'prepare',
        });
        fetch(wp.ajax.settings.url, options)
            .then(response => response.json())
            .then(response => {
                const data = response.data;
                const promises = [];
                this.total = data.total;
                for (let page = 1; page <= data.pages; page++) {
                    promises.push(this.import(page)); // fetch returns a Promise
                }
                Promise.all(promises).then(() => {
                    this.isIdle()
                    GLSR.notices.success(data.notice.replace('%d', this.processed));
                    this.reset()
                });
            })
            .catch(error => {
                console.error(error)
                GLSR.notices.error(error);
            })
            .finally(() => {
                jQuery('html, body').animate({ scrollTop: 0 }, 500);
            });
    }

    reset () {
        this.$el = null;
        this.processed = 0;
        this.total = 0;
    }
}

export default Import;
