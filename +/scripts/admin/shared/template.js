/**
 * A copy of a <template>'s first element with the {{ data.key }} placeholders of its attributes and
 * text filled from `data`, as wp.template filled them.
 */
export default (template, data) => {
    const fill = (text) => text.replace(/\{\{\s*data\.(\w+)\s*\}\}/g, (match, key) => key in data ? String(data[key]) : '');
    const el = template.content.firstElementChild.cloneNode(true);
    const walker = document.createTreeWalker(el, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT);
    for (let node = walker.currentNode; node; node = walker.nextNode()) {
        if (Node.TEXT_NODE === node.nodeType) {
            node.textContent = fill(node.textContent);
        } else {
            [...node.attributes].forEach(attribute => (attribute.value = fill(attribute.value)))
        }
    }
    return el;
}
