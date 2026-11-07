import Prism from 'prismjs';
import tippy from 'tippy.js';

/**
 * The tooltips of the plugin screens; one with a data-syntax attribute shows its content as highlighted code.
 */
export default class Tooltip {
    static init (root = document) {
        const els = [...root.querySelectorAll('.glsr-tooltip, .glsr-setting-field button[data-tippy-content]')];
        if (!els.length) return;
        tippy(els, { appendTo: () => document.body })
        els.forEach(el => {
            const { syntax, tippyContent } = el.dataset;
            if (el._tippy && tippyContent && syntax && Prism.languages[syntax]) {
                el._tippy.setContent(`<pre class="language-${syntax}"><code>${Prism.highlight(tippyContent, Prism.languages[syntax], syntax)}</code></pre>`)
            }
        })
    }
}
