const fadeConfig = () => ({
  delay: 0,
  direction: 'normal',
  easing: 'cubic-bezier(0.54,1.5,0.38,1.11)', //'linear',
  endDelay: 0,
  fill: 'forwards',
  iterations: 1,
});

const fade = (direction, el, durationInMs = 400, config = fadeConfig()) => {
  return new Promise(resolve => {
    const animation = el.animate([
      { opacity: 'in' === direction ? 0 : 1 },
      { opacity: 'in' === direction ? 1 : 0 },
    ], {duration: durationInMs, ...config});
    animation.onfinish = () => resolve();
  })
};

export const addRemoveClass = (el, classValue, bool) => {
  if (el) {
    classValue.split(' ').forEach(value => el.classList[bool ? 'add' : 'remove'](value))
  }
};

export const classListSelector = (classValue) => {
  return '.' + classValue.trim().split(' ').join('.');
};

export const debounce = (fn, wait = 200) => {
  let timeoutId = null
  return (...args) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(fn, wait, ...args);
  }
};

export const fadeIn = async (el, durationInMs, config) => {
  return fade('in', el, durationInMs, config);
};

export const fadeOut = async (el, durationInMs, config) => {
  return fade('out', el, durationInMs, config);
};

export const extend = () => {
  let args = [].slice.call(arguments);
  let result = args[0];
  let extenders = args.slice(1);
  Object.keys(extenders).forEach(i => {
    for (let key in extenders[i]) {
      if (!extenders[i].hasOwnProperty(key)) continue;
      result[key] = extenders[i][key];
    }
  })
  return result
};

export const isEmpty = (obj) => {
  return [Object, Array].includes((obj || {}).constructor) && !Object.entries((obj || {})).length;
};

export const parseJson = (str) => {
  try {
    return [null, JSON.parse(str)];
  } catch (err) {
    return [err, str];
  }
};

export const selectText = (el) => {
  if (window.getSelection && el.firstChild && el.firstChild.nodeType === Node.TEXT_NODE) {
    const textNode = el.firstChild;
    const text = textNode.textContent;
    const start = text.search(/\S/); // First non-whitespace character
    if (start === -1) return; // All whitespace, no selection needed
    const range = document.createRange();
    const selection = window.getSelection();
    const end = text.search(/\s*$/); // Start of trailing whitespace
    range.setStart(textNode, start);
    range.setEnd(textNode, end);
    selection.removeAllRanges();
    selection.addRange(range);
  }
};

export const throttle = (func, wait = 32) => {
  let timeout = null;
  let lastRan = 0;
  return function (...args) {
    const now = Date.now();
    const elapsed = now - lastRan;
    if (elapsed >= wait) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
      lastRan = now;
      return func.apply(this, args);
    } else if (!timeout) {
      timeout = setTimeout(() => {
        lastRan = Date.now();
        timeout = null;
        func.apply(this, args);
      }, wait - elapsed);
    }
  };
};
