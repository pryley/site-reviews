/**
 * @site-reviews/form: the review forms with their validation, conditions and star ratings, and GLSR.Form.
 */
import { attach } from '@/public/core/index.js';
import Form, { Instance, retain } from './form.js';

window.GLSR.Form = Form;

attach('form', (root) => {
    Form.init(root)
})

export { Instance, retain }

export default Form
