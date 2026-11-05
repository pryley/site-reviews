/**
 * @site-reviews/modal: the native <dialog> modal, and GLSR.Modal.
 */
import { attach, ensureStyles } from '@/public/core/index.js';
import Modal, { Instance, hooks } from './modal.js';

window.GLSR.Modal = Modal;

// no root names the modal's stylesheet: the modal is built client-side
attach('modal', () => ensureStyles(['modal']), { Instance, hooks })

export { Instance, hooks }

export default Modal
