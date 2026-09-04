# Javascript Modal

Site Reviews use a custom Modal which can be accessed globally using `GLSR.Modal`.

```js
GLSR.Modal.init(id, config)  // registers a modal
GLSR.Modal.open(id, config)  // opens a modal
GLSR.Modal.close(id)         // closes a modal; omit the id to close every open modal
GLSR.Modal.get(id)           // returns the modal instance, or null
```

## Modal config

```js
const config = {
    focus: false,
    onClose: (Modal, event) => {},
    onOpen: (Modal, event) => {},
};
```

## Modal instance

```js
const modal = GLSR.Modal.get('glsr-modal-review');
modal.header(html, attributes)   // html can be a string or a Node
modal.content(html, attributes)  // html can be a string or a Node
modal.footer(html, attributes)   // html can be a string or a Node
modal.style('max-width: 640px')  // a cssText string or an object of style properties
modal.hideClose()                // hides the close button
modal.close()                    // closes the modal
modal.isOpen                     // returns true if the modal is open
modal.trigger                    // the element that opened the modal
```

## Theming

```css
.glsr-modal {
    --glsr-modal-bg: #fff;
    --glsr-modal-radius: 3px;
    --glsr-duration-fast: .15s;
    --glsr-duration-slow:
}
.glsr-modal::part(dialog) {
    box-shadow: none;
}
.glsr-modal::part(close) {
    color: currentColor;
}
```
