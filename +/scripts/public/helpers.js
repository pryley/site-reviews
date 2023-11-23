/** @return void */
export const addRemoveClass = (el, classValue, bool) => { // HTMLElement, string, bool
    if (el) {
        classValue.split(' ').forEach(value => {
            el.classList[bool ? 'add' : 'remove'](value);
        });
    }
};

/** @return string */
export const classListSelector = (classValue) => { // string
    return '.' + classValue.trim().split(' ').join('.');
};

export const debounce = (fn, wait = 200) => {
    let timeoutId = null
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(fn, wait, ...args);
    }
}

/** @return object */
export const extend = () => { // ...object
    var args = [].slice.call(arguments);
    var result = args[0];
    var extenders = args.slice(1);
    Object.keys(extenders).forEach(function (i) {
        for (var key in extenders[i]) {
            if (!extenders[i].hasOwnProperty(key)) continue;
            result[key] = extenders[i][key];
        }
    });
    return result;
};

/** @return bool */
export const isEmpty = obj => [Object, Array].includes((obj || {}).constructor) && !Object.entries((obj || {})).length;
