import Prism from 'prismjs';

/**
 * The code samples of the plugin screens, highlighted by Prism, with a grammar for the shortcodes.
 */
export default class Code {
    static init () {
        Prism.languages.shortcode = { // order is intentional: Prism matches the tokens in turn
            tag: {
                lookbehind: true,
                pattern: /^(\[)[^\s]+/i,
            },
            string: {
                greedy: true,
                pattern: /"(?:\\[\s\S]|[^\\"])*"/,
            },
            variable: {
                pattern: /([a-z_]+(?==))/i,
            },
            punctuation: {
                pattern: /[=\[\]]/i,
            },
        };
        Prism.highlightAll()
    }
}
