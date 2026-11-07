import Request from '@/public/modules/request/request.js';

// the column's icon => its part of the review and the class the flag shows as
const FLAGS = {
    'td.column-is_pinned i.pin-review': { className: 'pinned', part: 'pinned' },
    'td.column-is_verified i.verify-review': { className: 'verified', part: 'verified' },
};

/**
 * The pin and verify icons of the review list which set the review's flag to the other value.
 */
export default class ToggleFlag {
    static init (root = document) {
        Object.entries(FLAGS).forEach(([selector, flag]) => {
            root.querySelectorAll(selector).forEach(icon => icon.addEventListener('click', ev => ToggleFlag.toggle(ev, flag)))
        })
    }

    static async toggle (ev, { className, part }) {
        ev.preventDefault()
        const icon = ev.currentTarget;
        const value = !icon.classList.contains(className);
        icon.classList.add('spinner', 'is-active')
        icon.classList.remove('dashicons-sticky')
        const { data, success } = await Request.send({
            body: { value },
            method: 'PUT',
            path: `reviews/${icon.dataset.id}/${part}`,
        });
        icon.classList.toggle(className, success ? 1 === data.value : !value)
        icon.classList.remove('spinner', 'is-active')
        icon.classList.add('dashicons-sticky')
    }
}
