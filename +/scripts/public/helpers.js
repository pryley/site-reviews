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

export function debounce (fn, wait = 200) {
    let timerId;
    let lastArgs;
    let lastCallTime;
    let lastThis;
    let result;
    const startTimer = (pendingFunc) => {
        cancelAnimationFrame(timerId)
        return requestAnimationFrame(pendingFunc)
    }
    const timerExpired = () => {
        const timeSinceLastCall = performance.now() - lastCallTime;
        if (timeSinceLastCall >= wait || timeSinceLastCall < 0) {
            result = fn.apply(lastThis, lastArgs);
            lastArgs = lastThis = void 0;
            return result
        }
        timerId = startTimer(timerExpired);
    }
    return (...args) => {
        lastArgs = args;
        lastCallTime = performance.now();
        lastThis = this;
        if (timerId === void 0) {
            timerId = startTimer(timerExpired);
        }
        return result
    }
}
