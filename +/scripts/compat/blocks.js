// the blocks that 8.0.0 rebuilt
const BLOCKS = ['site-reviews/form', 'site-reviews/reviews', 'site-reviews/summary'];

// lists that were comma-separated text before 8.0.0
const LISTS = ['assigned_posts', 'assigned_terms', 'assigned_users', 'hide'];

// the attributes that 8.0.0 removed
const REMOVED = ['assign_to', 'assigned_to', 'category', 'post_id', 'user'];

// the replaced attributes as a block saved them
const REPLACED = {
    assign_to: { default: '', type: 'string' },
    assigned_posts: { default: '', type: ['array', 'string'] },
    assigned_terms: { default: '', type: ['array', 'string'] },
    assigned_to: { default: '', type: 'string' },
    assigned_users: { default: '', type: ['array', 'string'] },
    category: { default: '', type: ['number', 'string'] },
    hide: { default: '', type: ['array', 'string'] },
    post_id: { default: '', type: ['number', 'string'] },
    schema: { default: false, type: ['boolean', 'number'] },
    user: { default: '', type: ['number', 'string'] },
};

const isEligible = (saved) => REMOVED.some((name) => name in saved)
    || LISTS.some((name) => 'string' === typeof saved[name])
    || 'boolean' === typeof saved.schema;

const list = (value) => (Array.isArray(value) ? value : String(value ?? '').split(','))
    .map((item) => String(item).trim())
    .filter((item) => item && 'custom' !== item); // "custom" only showed the text field

const migrate = (today) => ({ assign_to, assigned_to, category, post_id, user, ...attributes }) => {
    const migrated = {
        ...attributes,
        assigned_posts: list(list(attributes.assigned_posts).length ? attributes.assigned_posts : assign_to || assigned_to),
        assigned_terms: list(list(attributes.assigned_terms).length ? attributes.assigned_terms : category),
        assigned_users: list(list(attributes.assigned_users).length ? attributes.assigned_users : user),
        hide: list(attributes.hide),
    };
    if ('schema' in today) {
        migrated.schema = attributes.schema ? 1 : 0;
    } else {
        delete migrated.schema;
    }
    return migrated;
};

/**
 * Give each block a deprecated version, so the editor converts a block saved before 8.0.0 when it opens the page.
 */
export default (hooks) => {
    hooks.addFilter('blocks.registerBlockType', 'site-reviews/compat/blocks', (settings, name, deprecation = null) => {
        if (deprecation || !BLOCKS.includes(name)) { // WordPress also filters each deprecated version
            return settings;
        }
        const version = {
            attributes: { ...settings.attributes, ...REPLACED },
            isEligible,
            migrate: migrate(settings.attributes),
            save: () => null,
            supports: settings.supports,
        };
        return { ...settings, deprecated: [...(settings.deprecated ?? []), version] };
    })
}
