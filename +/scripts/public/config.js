/** global: GLSR */

import { flatConfig } from '@/compat/keys.js';

const freeze = (value) => {
    if (value && 'object' === typeof value && !Object.isFrozen(value)) {
        Object.values(value).forEach(freeze)
        Object.freeze(value)
    }
    return value;
}

window.GLSR = window.GLSR || {};
window.GLSR.config = freeze(window.GLSR.config || flatConfig(window.GLSR));

export default window.GLSR.config
