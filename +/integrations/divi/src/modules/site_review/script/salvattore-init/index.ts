/**
 * Salvattore needs to be initialized on the blog grid. This function
 * will check if the grid has already been initialized and if not,
 * initialize it on VB. This function is used in only VB.
 *
 * @param {HTMLElement} el The element to initialize Salvattore on.
 *
 * @returns {void}
 */
export const salvattoreInit = (el: HTMLElement): void => {
    // If the column element is found and the content changed we need to recreate them.
    if (el.querySelectorAll(':scope >.column').length) {
        window?.divi?.scriptLibrary?.scriptLibrarySalvattore?.recreateColumns(el);
    }

    // If the column element is not found, register the grid.
    if (! el.querySelectorAll(':scope >.column').length) {
        window?.divi?.scriptLibrary?.scriptLibrarySalvattore?.registerGrid(el);
    }
};
