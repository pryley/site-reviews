/** global: GLSR */

/**
 * Changes nothing on the page, sends no request and stores nothing: any visitor may load it.
 */

const PREFIX = '[site-reviews]';

export default ({ attach, defined, listeners }, events = null) => {
    const warned = new Set();

    const info = (message) => console.info(`${PREFIX} ${message}`);

    const warn = (key, message) => {
        if (warned.has(key)) return;
        warned.add(key)
        console.warn(`${PREFIX} ${message}`)
    }

    // Before 8.4.0 a submission that replaced a list or a summary ended with site-reviews/init.
    const warnAfterSubmission = () => {
        const counts = listeners();
        // site-reviews.js has one listener of site-reviews/init, and none of site-reviews/loaded
        Object.entries({ 'site-reviews/init': 1, 'site-reviews/loaded': 0 }).forEach(([name, own]) => {
            if ((counts[name] || 0) > own) {
                warn(`submission:${name}`, `A script listens to ${name}. Since Site Reviews 8.4.0 this event does not fire after a review is submitted; use site-reviews/review/initialized for a refreshed list and site-reviews/summary/updated for a refreshed summary.`)
            }
        })
    }

    const checkName = (name) => {
        const { CURRENT, OLD, REMOVED } = events;
        if (REMOVED[name]) {
            return warn(`event:${name}`, `A script listens to ${name}, which Site Reviews 8.4.0 removed; use ${REMOVED[name]} instead.`);
        }
        if (OLD[name]) {
            const isFired = false !== window.GLSR.config.compat;
            return warn(`event:${name}`, `A script listens to ${name}, which is deprecated; use ${OLD[name]} instead.${isFired ? '' : ' Compat mode is off, so nothing fires it.'}`);
        }
        const [prefix, ...parts] = name.split('/');
        if ('site-reviews' !== prefix || !parts.length || parts.length > 2) return;
        const module = 2 === parts.length ? parts[0] : '';
        if (CURRENT[module] && !CURRENT[module].includes(parts[parts.length - 1])) {
            const names = CURRENT[module].map(what => ['site-reviews', module, what].filter(Boolean).join('/'));
            warn(`event:${name}`, `A script listens to ${name}, which nothing fires. The events of ${module ? `GLSR.${module[0].toUpperCase()}${module.slice(1)}` : 'Site Reviews'} are ${names.join(', ')}.`)
        }
    }

    const reports = {
        deprecated: ({ name, replacement }) => {
            warn(`deprecated:${name}`, `${name} is deprecated; ${replacement ? `use ${replacement} instead` : 'it has no replacement'}.`)
        },
        form: ({ response, status, success }) => {
            info(`form: the submission was answered with ${status}`)
            if (success && (response?.reviews || response?.summary)) {
                warnAfterSubmission()
            }
        },
        listen: ({ name }) => events && checkName(name),
        pagination: ({ id, page, url }) => info(`pagination: page ${page} of #${id || '(no id)'}, ${url}`),
        request: ({ method, ms, path, status, transport }) => info(`${method} ${path}: ${transport}, ${status}, ${ms}ms`),
        validation: ({ isValid, name, rule, value }) => info(`validation: ${name}, ${rule}, "${value}", ${isValid ? 'pass' : 'fail'}`),
    };

    window.GLSR.Event.listeners = listeners;
    window.GLSR.registry.defined = defined;
    attach((type, data) => reports[type]?.(data))
}
