import { config } from '@/public/core/index.js';
import BusyButton from '@/admin/shared/busy-button.js';
import { buttonArgs, formBody } from '@/admin/shared/request.js';
import Notice from '@/admin/shared/notice.js';
import pLimit from 'p-limit';
import ProgressBar from '@/admin/tools/progress-bar.js';
import Request from '@/public/modules/request/request.js';

/**
 * An import (data-ajax-import, from the source in the button's data-args): stage 1 uploads and
 * counts, stage 2 imports the reviews a page at a time, stage 3 their attachments, stage 4 cleans
 * up and reports.
 */
export default class Import {
    static init (root = document) {
        root.querySelectorAll('form button[data-ajax-import]').forEach(button => new Import(button))
    }

    constructor (button) {
        this.button = button;
        this.form = button.closest('form');
        this.args = buttonArgs(button);
        this.controller = null;
        this.progressbar = null;
        button.addEventListener('click', this.onImport.bind(this))
        this.form.addEventListener('glsr-cancel-import', () => this.controller?.abort())
    }

    async import (perPage = 50) {
        const stage1 = await this.send({ stage: 1 });
        if (!stage1.success) {
            return stage1.data || {};
        }
        const { total } = stage1.data;
        if (total) {
            this.progressbar.init()
        }
        const stage2 = await this.process(2, total, Number(perPage) || 50); // 50 reviews per request by default
        const stage3 = await this.process(3, stage2.attachments, 1); // 1 attachment per request
        const stage4 = await this.send({
            duplicates: stage2.duplicates,
            errors: stage1.data.errors || [],
            failed: stage2.failed,
            imported: stage2.imported,
            skipped: stage2.skipped + (stage1.data.skipped ?? 0),
            stage: 4,
        });
        if (total) {
            this.progressbar.destroy()
        }
        return stage4.data || {};
    }

    async onImport (ev) {
        ev.preventDefault()
        const state = BusyButton(this.button, { disable: false });
        state.loading()
        this.progressbar = ProgressBar(this.button);
        const data = await this.import(this.button.dataset.per_page);
        window.setTimeout(() => {
            if (data?.notices) {
                Notice.add(data.notices)
            } else if (data?.message) {
                Notice.error(data.message) // a refusal or a failure answers with a WP_Error body
            }
            this.form.reset()
            state.loaded()
            this.controller = null;
            this.progressbar = null;
        }, 100)
    }

    async process (stage, total, perPage) {
        let current = 0;
        let processed = 0;
        this.controller = new AbortController();
        const limit = pLimit(1 === perPage ? 2 : 4);
        const pages = Math.ceil(total / perPage);
        const results = await Promise.allSettled([...Array(pages)].map((unused, index) => limit(async () => {
            const page = index + 1;
            const response = await this.send({ page, per_page: perPage, stage, total });
            if (this.controller?.signal.aborted) {
                return response; // the bar says it is cancelling
            }
            processed += (response.data?.imported ?? 0) + (response.data?.skipped ?? 0);
            this.progressbar.percent(Math.round((++current / pages) * 100), stage - 1)
            // %d or %1$d is the count so far, %2$d the total
            this.progressbar.text((response.data?.message || '').replace(/%(1\$)?d/, processed).replace('%2$d', total))
            return response;
        })));
        this.progressbar.percent(100, stage - 1)
        this.controller = null;
        return this.results(results);
    }

    results (settled) {
        const totals = { attachments: 0, duplicates: 0, failed: 0, imported: 0, skipped: 0 };
        settled.forEach(result => {
            if ('fulfilled' !== result.status || !result.value.success) return;
            Object.keys(totals).forEach(key => (totals[key] += result.value.data[key] ?? 0))
        })
        return totals;
    }

    async send (values) {
        const body = formBody(this.form, this.args);
        if (1 !== values.stage) {
            body.delete('import-files') // the file goes with stage 1 only
        }
        Object.entries(values).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                body.delete(key)
                value.forEach(item => body.append(`${key}[]`, item))
            } else if (value === Object(value)) { // the warnings of stage 1 are keyed
                Object.entries(value).forEach(([subkey, item]) => body.set(`${key}[${subkey}]`, item))
            } else {
                body.set(key, value)
            }
        })
        try {
            return await Request.send({ body, route: 'import', signal: this.controller?.signal });
        } catch (error) {
            console.error(error)
            Notice.error(config.text.importError)
            return { data: {}, status: 0, success: false };
        }
    }
}
