/**
 * A TextMate grammar for a shortcode example
 */
export default {
    patterns: [ // order is intentional: the first rule to match at a position wins
        {
            captures: {
                1: { name: 'punctuation.definition.tag' },
                2: { name: 'entity.name.tag' },
            },
            match: '(\\[)([^\\s\\]]+)',
        },
        {
            match: '"(?:\\\\.|[^"\\\\])*"',
            name: 'string.quoted.double',
        },
        {
            captures: {
                1: { name: 'entity.other.attribute-name' },
                2: { name: 'punctuation.separator.key-value' },
            },
            match: '([a-z_]+)(=)',
        },
        {
            match: '\\]',
            name: 'punctuation.definition.tag',
        },
    ],
    scopeName: 'source.shortcode',
};
