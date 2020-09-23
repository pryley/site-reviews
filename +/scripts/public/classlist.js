/** @return void */
export const classListAddRemove = (el, classValue, bool) => { // HTMLElement, string, bool
    classValue.split(' ').forEach(value => {
        el.classList[bool ? 'add' : 'remove'](value);
    });
};
/** @return string */
export const classListSelector = (classValue) => { // string
    return '.' + classValue.trim().split(' ').join('.');
};
