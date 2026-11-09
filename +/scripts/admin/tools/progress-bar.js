/** global: jQuery */

import { config } from '@/public/core/index.js';

const CLASSES = {
    bar: 'glsr-progress__bar',
    el: 'glsr-progress',
    status: 'glsr-progress__status',
};

const div = (className) => {
    const el = document.createElement('div');
    el.className = className;
    return el;
};

/**
 * The progress bar an import draws in place of its button: two bars, one per stage, a status line
 * and a Cancel button, which asks the form to stop (the `glsr-cancel-import` event).
 */
export default (button) => {
    const bars = [div(CLASSES.bar), div(CLASSES.bar)];
    const cancel = document.createElement('button');
    cancel.type = 'button';
    cancel.className = 'glsr-button button button-large';
    cancel.disabled = true;
    cancel.dataset.ajaxCancel = '';
    cancel.textContent = config.text.cancel;
    const status = div(CLASSES.status);
    let label = '';

    const onCancel = (ev) => {
        ev.preventDefault()
        button.closest('form').dispatchEvent(new CustomEvent('glsr-cancel-import'))
        cancel.disabled = true;
        cancel.removeEventListener('click', onCancel)
        text(config.text.cancelling)
    };

    const resize = () => bars.forEach(bar => (bar.style.width = `${button.offsetWidth}px`));

    const percent = (value = 0, barNum = 1) => {
        bars[barNum - 1].style.maxWidth = `${Math.min(100, Math.max(0, value))}%`;
        resize()
    };

    const text = (value = '') => {
        bars.forEach(bar => (bar.dataset.text = value))
        status.textContent = value;
        resize()
    };

    const init = () => {
        label = button.textContent;
        button.after(cancel)
        button.blur()
        button.classList.remove('is-busy')
        button.classList.add(CLASSES.el)
        button.textContent = '';
        button.append(...bars, status)
        cancel.disabled = false;
        cancel.addEventListener('click', onCancel)
        jQuery(document).on('wp-window-resized', resize) // WordPress triggers it through jQuery
        window.addEventListener('hashchange', resize)
        percent(0, 1)
        percent(0, 2)
        text(label)
    };

    const destroy = () => {
        jQuery(document).off('wp-window-resized', resize)
        window.removeEventListener('hashchange', resize)
        cancel.disabled = true;
        cancel.removeEventListener('click', onCancel)
        cancel.remove()
        button.classList.remove(CLASSES.el)
        button.textContent = label;
    };

    return { destroy, init, percent, resize, text };
}
