/** @return void */
export const classListAddRemove = (el, classValue, bool) => { // HTMLElement, string, bool
    classValue.split(' ').forEach(value => {
        el.classList[bool ? 'add' : 'remove'](value);
    });
};
