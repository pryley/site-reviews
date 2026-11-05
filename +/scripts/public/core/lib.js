/** global: GLSR */

const create = () => {
    const lib = {};
    Object.defineProperty(lib, 'register', {
        value: (name, value) => {
            if (!(name in lib)) {
                Object.defineProperty(lib, name, { enumerable: true, value })
            }
        },
    })
    return lib;
}

// The public and the admin script share the entries when both are on a page.
export default window.GLSR?.lib || create()
