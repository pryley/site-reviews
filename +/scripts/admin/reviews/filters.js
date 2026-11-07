import { debounce } from '@/public/core/index.js';
import Request from '@/public/modules/request/request.js';

/**
 * The Filters checkboxes of the Screen Options panel: each shows or hides its dropdown, and the
 * choice is saved as the user's preference through WordPress's users route.
 */
export default class Filters {
    static init (root = document) {
        const checkboxes = [...root.querySelectorAll('#adv-settings .enable-filter-tog')];
        if (checkboxes.length) {
            new Filters(checkboxes)
        }
    }

    constructor (checkboxes) {
        this.checkboxes = checkboxes;
        this.controller = null;
        this.save = debounce(this.save.bind(this), 500);
        checkboxes.forEach(checkbox => checkbox.addEventListener('click', this.onClick.bind(this)))
    }

    enabled () {
        return this.checkboxes.filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
    }

    onClick (ev) {
        const dropdown = document.getElementById(`glsr-filter-by-${ev.currentTarget.value}`);
        dropdown?.classList.toggle('hidden', !ev.currentTarget.checked) // @compat with other WP filters
        dropdown?.classList.toggle('is-hidden', !ev.currentTarget.checked)
        this.save()
    }

    save () {
        this.controller?.abort()
        this.controller = new AbortController();
        const key = this.checkboxes[0].name.replace(/\[\]$/, ''); // the meta key from the checkboxes' name
        Request.send({
            body: { meta: { [key]: this.enabled() } },
            method: 'PUT',
            path: '/wp/v2/users/me',
            signal: this.controller.signal,
        })
    }
}
