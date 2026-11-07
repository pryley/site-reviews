/** global: GLSR */

const freeze = (value) => {
    if (value && 'object' === typeof value && !Object.isFrozen(value)) {
        Object.values(value).forEach(freeze)
        Object.freeze(value)
    }
    return value;
}

window.GLSR = window.GLSR || {};
window.GLSR.config = freeze(window.GLSR.config || {});

export default window.GLSR.config
