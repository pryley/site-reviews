// A module ('' is Site Reviews itself) => what its events are called after the module.
export const CURRENT = {
    '': ['init', 'initialized'],
    form: ['initialized', 'submitted'],
    modal: ['closed', 'opened'],
    review: ['initialized', 'paginated'],
    summary: ['updated'],
};

// A name before 8.4.0 => its replacement. The compat script fires these.
export const OLD = {
    'block:site-reviews/form': 'site-reviews/initialized',
    'block:site-reviews/review': 'site-reviews/initialized',
    'block:site-reviews/reviews': 'site-reviews/initialized',
    'block:site-reviews/summary': 'site-reviews/initialized',
    'site-reviews/excerpts/init': 'site-reviews/review/initialized',
    'site-reviews/form/handle': 'site-reviews/form/submitted',
    'site-reviews/forms/init': 'site-reviews/form/initialized',
    'site-reviews/loaded': 'site-reviews/initialized',
    'site-reviews/modal/close': 'site-reviews/modal/closed',
    'site-reviews/modal/init': 'site-reviews/review/initialized',
    'site-reviews/modal/open': 'site-reviews/modal/opened',
    'site-reviews/pagination/handle': 'site-reviews/review/paginated',
    'site-reviews/pagination/init': 'site-reviews/review/initialized',
};

// A name that 8.4.0 removed => what to use.
export const REMOVED = {
    'site-reviews/pagination/popstate': "window.addEventListener('popstate', …)",
};
