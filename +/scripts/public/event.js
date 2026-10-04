import report from '@/public/report.js';

// shared with the other script when both are on a page
const KEY = Symbol.for('site-reviews.events');

const events = window.GLSR?.Event?.[KEY] || {};

export const listeners = (name) => {
    if (name) {
        return Object.freeze((events[name] || []).map(({ fn, context }) => Object.freeze({ fn: fn.once || fn, context })));
    }
    return Object.freeze(Object.fromEntries(Object.entries(events).map(([name, triggers]) => [name, triggers.length])));
}

const off = function (name, fn) {
    const triggers = events[name] || []
    const liveTriggers = [];
    if (fn) {
        [].forEach.call(triggers, event => {
            if (fn !== event.fn && fn !== event.fn.once) {
                liveTriggers.push(event)
            }
        })
    }
    if (liveTriggers.length) {
        events[name] = liveTriggers
    } else {
        delete events[name]
    }
}

// adds a listener without reporting it to debug mode
export const listen = function (name, fn, context) {
    (events[name] || (events[name] = [])).push({ fn, context })
}

const on = function (name, fn, context) {
    report('listen', { name })
    listen(name, fn, context)
}

const once = function (name, fn, context) {
    const listener = function () {
        off(name, listener)
        fn.apply(context, arguments)
    }
    listener.once = fn
    on(name, listener, context)
}

const trigger = function (name) {
    const data = [].slice.call(arguments, 1)
    const triggers = (events[name] || []).slice(); // shallow copy
    [].forEach.call(triggers, event => event.fn.apply(event.context, data))
}

const Event = { off, on, once, trigger }

// The inline script's stub is extended in place: other scripts may hold a reference to it.
export const adopt = (stub) => {
    if (stub?.[KEY]) {
        return stub; // the other script got here first
    }
    const target = stub || Event;
    const queue = target.q || [];
    delete target.q;
    Object.assign(target, Event)
    Object.defineProperty(target, KEY, { value: events })
    queue.forEach(args => on(...args))
    return target;
}

export default Event
