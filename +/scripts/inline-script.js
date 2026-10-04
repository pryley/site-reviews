/** global: GLSR_CONFIG, GLSR_DEPRECATED, GLSR_VERSION */

/**
 * PHP replaces the three GLSR_ names in the built file (Modules\Assets\InlineScript).
 */

const isObject = (value) => value && 'object' === typeof value && !Array.isArray(value);

// The block editor prints both inline scripts on one page; the first config may already be frozen.
const merge = (first, second) => {
    if (!first) {
        return second;
    }
    const merged = Object.assign({}, first);
    for (const key in second) {
        merged[key] = isObject(first[key]) && isObject(second[key])
            ? merge(first[key], second[key])
            : second[key];
    }
    return merged;
}

// GLSR.Event.on() queues until the script runs, which replays the queue.
const queue = (q) => ({ on: (...args) => { q.push(args) }, q });

((version, config, deprecated) => {
    const GLSR = window.GLSR = window.GLSR || {};
    GLSR.Event = GLSR.Event || queue([]);
    GLSR.version = version;
    GLSR.config = merge(GLSR.config, config);
    Object.assign(GLSR, deprecated)
})(GLSR_VERSION, GLSR_CONFIG, GLSR_DEPRECATED)
