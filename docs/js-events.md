# Javascript Events

Site Reviews has its own events, which any script can listen to with `GLSR.Event`.

```js
GLSR.Event.on(name, callback, context)     // listens to an event
GLSR.Event.once(name, callback, context)   // listens to the next time it fires
GLSR.Event.off(name, callback)             // removes a listener
GLSR.Event.trigger(name, ...args)          // fires an event of your own
```

`GLSR.Event.on` can be called before the Site Reviews script has run: the listener is kept and added when it runs.

## Names

An event is named `site-reviews/{module}/{what happened}`, with the module in lower case (`form` for `GLSR.Form`). It states something that has happened; it is never an instruction. To make something happen, call the module (`GLSR.Form.init(root)`) or `GLSR_init(root)`; see [js-api.md](js-api.md).

Every event passes one object with named members.

### Event words

The last part of a name is one of these words. Each has one meaning and one shape of object, whichever module uses it.

| Word | What happened | The object | Used by |
| --- | --- | --- | --- |
| `initialized` | Elements were set up inside a root | `{ root, instances }` | `review`, `form` |
| `updated` | The content of an instance was replaced | the instance, under the name of its module (`{ summary }`) | `summary` |
| `submitted` | The server answered a submission, accepted or refused | `{ form, response, success }` | `form` |
| `paginated` | New items were put in a list | the instance and the response (`{ review, response }`) | `review` |
| `opened` | A modal opened | `{ modal, event }` | `modal` |
| `closed` | A modal closed | `{ modal, event }` | `modal` |

- An event that concerns one instance passes it under the singular name of its module: `review`, `form`, `summary`, `modal`.
- A new event uses a word from this table when its meaning fits. A word is added to the table only when none fits.
- `site-reviews/initialized` has no module: it is for everything inside a root, and its object is `{ root }`.
- `site-reviews/init` is the one name that is an instruction (see below).

## Events

**`site-reviews/initialized`**

Site Reviews has set up the reviews, forms and summaries inside a root: the whole page when it loads, or the element given to `GLSR_init(root)`.

```js
GLSR.Event.on('site-reviews/initialized', ({ root }) => {
    // root is document, or the element that was set up
})
```

**`site-reviews/review/initialized`**

Reviews have been set up: when the page loads, after `GLSR.Review.init(root)`, after a submitted review replaced a list, and after a page change. Use it to do something with reviews that have just appeared.

```js
GLSR.Event.on('site-reviews/review/initialized', ({ root, instances }) => {
    // root is document, or the element whose reviews were set up
    // instances are the lists and single reviews (GLSR.Review) that were set up
})
```

**`site-reviews/review/paginated`**

A pagination link or a "load more" button has put new reviews in a list. `site-reviews/review/initialized` follows it for that list.

```js
GLSR.Event.on('site-reviews/review/paginated', ({ review, response }) => {
    // review is the list; review.el is its element, review.pagination its pagination
})
```

**`site-reviews/form/initialized`**

Forms have been set up.

```js
GLSR.Event.on('site-reviews/form/initialized', ({ root, instances }) => {
    // instances are the forms (GLSR.Form) that were set up
})
```

**`site-reviews/form/submitted`**

A form was submitted and the server has answered. It fires once for each submission, whether the review was accepted or refused: read `success`. The form already shows its message or its errors. The page has not been updated yet, and a form that redirects has not redirected yet.

```js
GLSR.Event.on('site-reviews/form/submitted', ({ form, response, success }) => {
    // form.el is the <form> element
    // response.message, response.errors, response.review, …
})
```

After an accepted review, the list and the summary that the form updates are announced by `site-reviews/review/initialized` and `site-reviews/summary/updated`.

**`site-reviews/summary/updated`**

The content of a rating summary was replaced, which a submitted review does.

```js
GLSR.Event.on('site-reviews/summary/updated', ({ summary }) => {
    // summary.el is the element of the summary
})
```

**`site-reviews/modal/opened`**

A modal has opened. The event fires directly after the dialog is shown, before the browser draws it, so content that the listener adds at once is there from the first frame.

```js
GLSR.Event.on('site-reviews/modal/opened', ({ modal, event }) => {
    // event is the click that opened the modal, if one did
})
```

**`site-reviews/modal/closed`**

A modal has closed.

```js
GLSR.Event.on('site-reviews/modal/closed', ({ modal, event }) => {
    // do something here...
})
```

## `site-reviews/init`

This is the one event that is also an instruction. Triggering it sets up the whole page, as `GLSR_init()` does. A listener that was added before the page was ready runs before the page is set up.

```js
GLSR.Event.trigger('site-reviews/init') // sets up the whole page
```

```js
GLSR.Event.on('site-reviews/init', () => {
    // the page is about to be set up
})
```

It does not fire after a review is submitted: the form only sets up the list and the summary that it updates.

## Deprecated names

Site Reviews 8.4.0 renamed its events. The old names are deprecated: use the new ones in anything you write. They keep firing, with the arguments they had, through compat mode, which is on by default (see [js-api.md](js-api.md#deprecated-keys)). In debug mode, a script that listens to an old name logs a warning that names its replacement.

| Deprecated | Arguments | Use instead |
| --- | --- | --- |
| `site-reviews/loaded` | none | `site-reviews/initialized` |
| `site-reviews/excerpts/init` | `(reviewsEl)` | `site-reviews/review/initialized` |
| `site-reviews/modal/init` | none | `site-reviews/review/initialized` |
| `site-reviews/pagination/init` | none | `site-reviews/review/initialized` |
| `site-reviews/pagination/handle` | `(response, pagination)` | `site-reviews/review/paginated` |
| `site-reviews/forms/init` | none | `site-reviews/form/initialized` |
| `site-reviews/form/handle` | `(response, formEl)` | `site-reviews/form/submitted` |
| `site-reviews/modal/open` | `(modal, event)` | `site-reviews/modal/opened` |
| `site-reviews/modal/close` | `(modal, event)` | `site-reviews/modal/closed` |
| `block:site-reviews/form`, `…/review`, `…/reviews`, `…/summary` | `(el, attributes)` | `site-reviews/initialized` |

- `site-reviews/modal/open` still fires before the dialog is shown; `site-reviews/modal/opened` fires after.
- Triggering `site-reviews/excerpts/init`, `site-reviews/modal/init`, `site-reviews/pagination/init`, `site-reviews/forms/init` or a `block:` name still starts that work. Call `GLSR.Review.init(root)`, `GLSR.Form.init(root)` or `GLSR_init(root)` instead.

One event is removed: `site-reviews/pagination/popstate`. It repeated the browser's own event, which you can listen to directly:

```js
window.addEventListener('popstate', (event) => {
    // event.state holds the saved history state for the page
})
```
