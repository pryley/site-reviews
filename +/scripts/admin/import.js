import Button from '@/admin/button.js';
import ProgressBar from '@/admin/progress-bar.js';
import pLimit from 'p-limit';

class Import {
    constructor () {
        this.reset()
        jQuery('form')
            .has('button[data-ajax-import]')
            .on('click', 'button[data-ajax-import]', this.onImport.bind(this))
            .on('glsr-cancel-import', this.abort.bind(this))
    }

    async fetch (data = {}) {
        const options = {
            body: this.formdata(data),
            credentials: 'same-origin',
            method: 'POST',
        }
        if (this.abortController) {
            options.signal = this.abortController.signal;
        }
        const response = await fetch(wp.ajax.settings.url, options);
        return response.json()
    }

    async import () {
        console.info('run import');
        const stage1 = await this.fetch({ stage: 1 });
        console.info('stage 1 complete', stage1);
        this.data = stage1.data;
        if (!stage1.success) {
            return this.data
        }
        if (this.data.total) {
            this.progressbar.init()
        }
        const stage2 = await this.process(2, this.data.total, 50); // import 50 reviews per request
        console.info('stage 2 complete', stage2);
        const stage3 = await this.process(3, stage2.attachments, 1); // import 1 attachment per request
        console.info('stage 3 complete', stage3);
        const stage4 = await this.fetch({
            errors: this.data.errors || [],
            imported: stage2.imported,
            skipped: stage2.skipped,
            stage: 4,
        });
        console.info('stage 4 complete', stage4);
        if (this.data.total) {
            this.progressbar.destroy()
        }
        return stage4.data
    }

    abort () {
        if (this.abortController) {
            this.abortController.abort();
        }
    }

    formdata (data = {}) {
        let fd = new FormData(this.$form.get(0));
        if (1 !== data.stage) {
            fd.delete('import-files') // don't upload files on later stages
        }
        for (let key in data) {
            fd.set(`${GLSR.nameprefix}[${key}]`, data[key]);
        }
        fd.set('action', GLSR.action);
        return fd;
    }

    isBusy () {
        Button(this.$el, false).loading()
    }

    isIdle () {
        Button(this.$el, false).loaded()
    }

    onImport (ev) {
        ev.preventDefault()
        this.$el = jQuery(ev.currentTarget);
        this.$form = this.$el.closest('form');
        this.isBusy()
        this.progressbar = ProgressBar(this.$el);
        this.import().then(data => {
            setTimeout(() => {
                if (data?.notices) {
                    GLSR.notices.add(data.notices)
                }
                this.$form.get(0).reset()
                this.isIdle()
                this.reset()
                console.info('all done')
            }, 100)
        });
    }

    process (stage, total, per_page) {
        let current = 0;
        let processed = 0;
        this.abortController = new AbortController();
        const limit = pLimit((1 === per_page ? 2 : 4)); // the number of concurrent requests allowed
        const pages = Math.ceil(total / per_page);
        const promises = [...Array(pages)]
            .map((_, index) => ++index)
            .map(page => limit(async () => {
                const response = await this.fetch({ page, per_page, stage, total });
                const percent = Math.round((++current / pages) * 100);
                processed += (response.data.imported + response.data.skipped);
                this.progressbar.percent(percent)
                this.progressbar.text(wp.i18n.sprintf(response.data.message, processed, total))
                return response
            }));
        return (async () => {
            const data = await Promise.allSettled(promises);
            this.abortController = null;
            return this.results(data)
        })()
    }

    reset () {
        this.$el = null;
        this.$form = null;
        this.abortController = null;
        this.aborted = false;
        this.data = {};
        this.progressbar = null;
    }

    results (data) {
        const acc = {
            attachments: 0,
            imported: 0,
            skipped: 0,
        };
        return data.reduce((acc, r) => {
            for (let k in acc) {
                if ('fulfilled' === r.status && r.value.success) {
                    acc[k] += (r.value.data[k] ?? 0);
                }
            }
            return acc
        }, acc);
    }
}

export default Import;
