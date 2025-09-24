import { maybeRegisterSalvattoreGrid } from './maybe-register-salvattore-grid';
import { salvattoreInit } from './salvattore-init';

(() => {
    // Add diviModuleBlogGridInit into window object.
    if (! ('diviModuleBlogGridInit' in window)) {
        Object.defineProperty(window, 'diviModuleBlogGridInit', {
            value: maybeRegisterSalvattoreGrid,
            writable: false,
        });
    }

    // Add diviModuleBlogSalvattoreInit into window object.
    if (! ('diviModuleBlogSalvattoreInit' in window)) {
        Object.defineProperty(window, 'diviModuleBlogSalvattoreInit', {
            value: salvattoreInit,
            writable: false,
        });
    }
})();

export { maybeRegisterSalvattoreGrid, salvattoreInit };
