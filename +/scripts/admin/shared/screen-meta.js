/** global: jQuery, screenMeta */

/**
 * The Screen Options toggle of our page header, and the Close Panel button in the panel, which
 * open and close WordPress's panel through its screenMeta API.
 */
export default class ScreenMeta {
    static init (root = document) {
        root.querySelectorAll('.glsr-screen-meta-toggle').forEach(toggle => toggle.addEventListener('click', ScreenMeta.onToggle))
    }

    static onToggle (ev) {
        if ('undefined' === typeof screenMeta) return;
        const toggle = ev.currentTarget;
        const panel = document.getElementById(toggle.getAttribute('aria-controls'));
        if (!panel) return;
        if (panel.offsetParent) { // visible
            screenMeta.close(jQuery(panel), jQuery(toggle))
            document.querySelectorAll('.glsr-screen-meta-toggle').forEach(el => {
                el.classList.remove('screen-meta-active')
                el.setAttribute('aria-expanded', 'false')
            })
        } else {
            screenMeta.open(jQuery(panel), jQuery(toggle))
        }
    }
}
