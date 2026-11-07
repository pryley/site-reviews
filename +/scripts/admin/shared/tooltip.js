import Code from '@/admin/shared/code.js';
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
            if (el._tippy && tippyContent && Code.supports(syntax)) {
                const code = document.createElement('code');
                code.className = `language-${syntax}`;
                code.textContent = tippyContent;
                const pre = document.createElement('pre');
                pre.append(code);
                el._tippy.setContent(pre)
                el._tippy.setProps({ onShown: () => Code.init() }) // the popup is in the body so the whole document is scanned again
            }
        })
    }
}
