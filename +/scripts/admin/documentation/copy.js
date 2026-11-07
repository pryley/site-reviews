/**
 * Copying a code sample or a tooltip's code gives the text as shown, without its markup.
 */
export default class Copy {
    static init (root = document) {
        const body = root.querySelector('#wpbody');
        if (body) {
            body.addEventListener('copy', Copy.onCopy)
        }
    }

    static onCopy (ev) {
        if (!ev.target.closest?.('.shortcode-example, .tippy-content')) return;
        ev.clipboardData.setData('text/plain', document.getSelection().toString())
        ev.preventDefault()
    }
}
