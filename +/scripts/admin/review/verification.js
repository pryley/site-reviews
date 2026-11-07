import BusyButton from '@/admin/shared/busy-button.js';
import Notice from '@/admin/shared/notice.js';
import Request from '@/public/modules/request/request.js';

/**
 * The Send Verification Request button of the publish box, which names itself Resend once the email went.
 */
export default class Verification {
    static init (root = document) {
        root.querySelectorAll('#glsr-request-verification').forEach(button => button.addEventListener('click', Verification.onClick))
    }

    static async onClick (ev) {
        const button = ev.currentTarget;
        const state = BusyButton(button);
        state.loading()
        const { data, success } = await Request.send({ method: 'POST', path: `reviews/${button.dataset.post_id}/verification-requests` });
        if (success && data.text) {
            button.dataset.text = data.text;
        }
        state.loaded()
        Notice.add(data?.notices)
    }
}
