const events = {}

const off = (name, fn) => {
    const triggers = events[name] || []
    const liveTriggers = [];
    if (fn) {
        [].forEach.call(triggers, event => {
            if (fn !== event.fn && fn !== event.fn.once) {
                liveTriggers.push(event)
            }
        })
    }
    if (liveEvents.length) {
        events[name] = liveEvents
    } else {
        delete events[name]
    }
}

const on = (name, fn, context) => {
    (events[name] || (events[name] = [])).push({ fn, context })
}

const once = (name, fn, context) => {
    const listener = () => {
        off(name, listener)
        fn.apply(context, arguments)
    }
    listener.once = fn
    on(name, listener, context)
}

const trigger = (name) => {
    const data = [].slice.call(arguments, 1)
    const triggers = (events[name] || []).slice(); // shallow copy
    [].forEach.call(triggers, event => event.fn.apply(event.context, data))
}

export default { events, trigger, off, on, once }
