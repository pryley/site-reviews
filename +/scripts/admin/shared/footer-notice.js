/**
 * The footer notice: the page content makes room for it.
 */
export default class FooterNotice {
    static init (root = document) {
        if (root.querySelector('.glsr-notice-footer')) {
            document.getElementById('wpbody-content')?.classList.add('has-footer-notice')
        }
    }
}
