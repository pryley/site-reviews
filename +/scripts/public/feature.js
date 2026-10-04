/**
 * The contract that Review, Form and Summary share.
 *
 * @param {Function} elements Returns the feature's elements inside a root
 * @param {Function} create   Returns the instance of an element: { el, init(), destroy() }
 */
export default (elements, create) => {
    let instances = [];

    const find = (el) => instances.find(instance => instance.el === el) || null;

    // with no root: the instances whose element has left the document
    const destroy = (root) => {
        instances = instances.filter(instance => {
            const isGone = root ? root.contains(instance.el) : !instance.el.isConnected;
            if (isGone) {
                instance.destroy()
            }
            return !isGone;
        })
    }

    const init = (root = document) => {
        destroy()
        return [...new Set(elements(root))].map(el => {
            let instance = find(el);
            if (instance) {
                instance.destroy()
            } else {
                instance = create(el);
                instances.push(instance)
            }
            instance.init()
            return instance;
        });
    }

    const retain = (kept) => {
        instances = instances.filter(instance => {
            const isKept = kept.includes(instance);
            if (!isKept) {
                instance.destroy()
            }
            return isKept;
        })
    }

    const module = {
        destroy,
        find,
        init,
        get instances () {
            return Object.freeze([...instances]);
        },
    };

    return { module, retain };
}

// The root can be the wrapper itself, or an element inside it.
export const wrappers = (root, selector) => {
    const matches = [...root.querySelectorAll(selector)];
    if (root.matches?.(selector) || (!matches.length && root.closest?.(selector))) {
        matches.push(root.matches(selector) ? root : root.closest(selector))
    }
    return matches.map(el => el.closest('.glsr') || el);
}

export const direction = (el) => {
    el.classList.add('glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction'))
}
