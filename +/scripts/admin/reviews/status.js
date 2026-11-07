import Request from '@/public/modules/request/request.js';

/**
 * The Approve and Unapprove row actions of the review list: the row, the title, the status links
 * above the table and the menu's pending count are redrawn from the answer.
 */
export default class Status {
    static init (root = document) {
        root.querySelectorAll('a.glsr-toggle-status').forEach(link => link.addEventListener('click', Status.onClick))
    }

    static async onClick (ev) {
        const link = ev.currentTarget;
        const id = link.href.match(/post=([0-9]+)/);
        const action = link.href.match(/action=([a-z]+)/);
        if (!id || !action) return;
        ev.preventDefault()
        const row = link.closest('tr');
        const indicator = row?.querySelector('.locked-indicator');
        indicator?.classList.add('spinner', 'is-active') // in place of the checkbox
        const { data, success } = await Request.send({
            body: { status: 'approve' === action[1] ? 'publish' : 'pending' },
            method: 'PUT',
            path: `reviews/${id[1]}/status`,
        });
        indicator?.classList.remove('spinner', 'is-active')
        if (!success || !data.class) return;
        row.classList.remove('status-pending', 'status-publish')
        row.classList.add(data.class)
        const title = row.querySelector('td.column-title strong');
        if (title) {
            title.innerHTML = data.link;
        }
        if (!data.counts) return;
        const counts = link.closest('.wrap')?.querySelector('ul.subsubsub');
        if (counts) {
            counts.innerHTML = data.counts;
        }
        const bubble = document.querySelector('#menu-posts-site-review .awaiting-mod');
        if (bubble) {
            bubble.className = `awaiting-mod count-${data.pending}`;
            const count = bubble.querySelector('.unapproved-count');
            if (count) {
                count.textContent = data.pending;
            }
        }
    }
}
