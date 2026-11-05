/**
 * @site-reviews/modal: the native <dialog> modal, and GLSR.Modal.
 */
import { attach } from '@/public/core/index.js';
import Modal, { Instance, hooks } from './modal.js';

window.GLSR.Modal = Modal;

attach('modal', () => {}, { Instance, hooks })

export { Instance, hooks }

export default Modal
