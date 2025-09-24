/**
 * Salvattore is dependent on CSS to determine the number of columns
 * so if the script gets executed before the CSS is ready it will fail.
 *
 * We can use this function to register the grid on window load when we
 * can assume the CSS is ready.
 *
 * @returns {void}
 */

export const maybeRegisterSalvattoreGrid = (): void => {
    const blogGrid = document.querySelectorAll('.et_pb_blog_grid');

    if (0 === blogGrid.length) {
        return;
    }

    blogGrid.forEach(grid => {
        const salvattoreContent = grid.querySelectorAll('.et_pb_salvattore_content');
        const interval          = setInterval(() => {
            salvattoreContent.forEach((thisEl: HTMLElement) => {
                const contentValue = getComputedStyle(thisEl, ':before').content;

                // If the 'content' value is NOT 'none', CSS is ready so we can clear.
                if ('none' !== contentValue) {
                    clearInterval(interval);
                }

                // If .column exists, the grid has already been registered so we can "return", as in "continue" in the loop.
                if (thisEl.querySelectorAll('.column').length) {
                    return;
                }

                // If 'content' value is 'none', CSS is not ready so we can "return", as in "continue" in the loop.
                if ('none' === contentValue) {
                    return;
                }

                if (thisEl.querySelectorAll('div').length && ! thisEl.querySelectorAll('div').item(0).classList.length) {
                    // If the next element is a div, without a class, attempt to recreate the columns.
                    window?.divi?.scriptLibrary?.scriptLibrarySalvattore?.recreateColumns(thisEl);
                } else {
                    // Otherwise, register the grid.
                    window?.divi?.scriptLibrary?.scriptLibrarySalvattore?.registerGrid(thisEl);
                }
            });
        }, 100);
    });
};
