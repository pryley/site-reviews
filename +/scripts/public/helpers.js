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
