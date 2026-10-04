import config from '@/public/config.js';

const LIMIT = 50;
const pending = [];

let isExpected = true === config.debug?.enabled;
let listener = null;

export const attach = (fn) => {
    listener = fn;
    pending.splice(0).forEach(([type, data]) => report(type, data))
}

// reports are kept only while the debug script is on its way
export const expect = () => {
    isExpected = true;
}

const report = (type, data) => {
    if (listener) {
        try {
            listener(type, data)
        } catch (error) {} // debug mode never changes what the scripts do
    } else if (isExpected && pending.length < LIMIT) {
        pending.push([type, data])
    }
}

export default report
