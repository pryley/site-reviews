import { highlightAll } from '@/admin/vendor/microlighter/highlight.js';
import bash from 'microlighter/grammars/bash.js';
import css from 'microlighter/grammars/css.js';
import html from 'microlighter/grammars/html.js';
import javascript from 'microlighter/grammars/javascript.js';
import php from 'microlighter/grammars/php.js';
import shortcode from '@/admin/shared/grammars/shortcode.js';

const GRAMMARS = { bash, css, html, javascript, php, shortcode };

/**
 * The code samples of the plugin screens, highlighted with the CSS Custom Highlight API.
 */
export default class Code {
    static init (root = document) {
        if (!globalThis.CSS?.highlights) {
            return // the samples stay plain monospace
        }
        highlightAll({
            importLanguage: language => Promise.resolve(GRAMMARS[language] || null),
            root,
            selector: 'pre > code[class*="language-"]',
        })
    }

    static supports (language) {
        return language in GRAMMARS
    }
}
