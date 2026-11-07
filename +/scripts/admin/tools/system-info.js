import { config } from '@/public/core/index.js';
import Notice from '@/admin/shared/notice.js';
import Request from '@/public/modules/request/request.js';

/**
 * The System Info tab loads its report when it is first shown, and enables its download button.
 */
export default class SystemInfo {
    static init (root = document) {
        const view = root.querySelector('#system-info');
        if (!view) return;
        let loaded = false;
        document.addEventListener('site-reviews/tab', async ({ detail }) => {
            if ('system-info' !== detail.id || loaded) return;
            loaded = true;
            const textarea = view.querySelector('textarea');
            const { data, status, success } = await Request.send({ route: 'system-info' });
            if (success) {
                textarea.value = data.system_info;
                view.querySelectorAll('button').forEach(button => (button.disabled = false))
                return;
            }
            loaded = false;
            textarea.value = config.text.systemInfoFailed;
            if (data?.notices) {
                Notice.error(data.notices)
            } else if (500 === status) {
                Notice.error(config.text.systemInfo500)
            } else {
                Notice.error(config.text.systemInfoError.replace('%1$s', status).replace('%2$s', data?.message || ''))
            }
        })
    }
}
