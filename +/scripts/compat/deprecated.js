/** global: GLSR */

const noticed = new Set();

let report = () => {};

const deprecated = (name, replacement) => {
    if (noticed.has(name)) return;
    noticed.add(name)
    report('deprecated', { name, replacement })
}

/**
 * A key that 9.0.0 removed: reading it reports the removal and gives undefined, unless the page
 * set the key itself (an 8.x localize filter still prints what it added).
 */
export const removed = (target, key, name, replacement) => {
    if (key in target) return;
    Object.defineProperty(target, key, {
        configurable: true,
        enumerable: false,
        get () {
            if (!noticed.has(name)) {
                noticed.add(name)
                report('removed', { name, replacement })
            }
            return undefined;
        },
        set (value) {
            Object.defineProperty(this, key, { configurable: true, enumerable: true, value, writable: true })
        },
    })
}

export const reportTo = (fn) => {
    report = fn;
}

export default deprecated
