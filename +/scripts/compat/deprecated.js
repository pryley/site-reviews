/** global: GLSR */

import { rename } from '@/compat/keys.js';

const noticed = new Set();

let report = () => {};

const deprecated = (name, replacement) => {
    if (noticed.has(name)) return;
    noticed.add(name)
    report('deprecated', { name, replacement })
}

/**
 * Assigning to the key replaces the getter with the value, as it did before 8.4.0.
 * A getter without a setter would throw in strict mode.
 */
export const alias = (target, key, name, replacement, get, set) => {
    Object.defineProperty(target, key, {
        configurable: true,
        enumerable: false,
        get () {
            deprecated(name, replacement)
            return get.call(this)
        },
        set: set || function (value) {
            Object.defineProperty(this, key, { configurable: true, enumerable: true, value, writable: true })
        },
    })
}

export const flip = (names) => names && Object.fromEntries(Object.entries(names).map(([key, name]) => [name, key]));

export const read = (config, path) => path.split('.').reduce((value, key) => value?.[key], config);

export const reportTo = (fn) => {
    report = fn;
}

/**
 * `keys` holds the names that the members of a value had before 8.4.0.
 */
export const aliasPaths = (paths, keys, view = {}) => {
    const { config } = window.GLSR;
    Object.entries(paths).forEach(([key, path]) => {
        alias(window.GLSR, key, `GLSR.${key}`, `GLSR.config.${path}`, () => {
            const value = rename(read(config, path), flip(keys[key]));
            return view[key] ? view[key](value) : value;
        })
    })
}

export default deprecated
