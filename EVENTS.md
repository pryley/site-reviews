# Javascript Events

Site Reviews use a custom Event Emitting system which can be accessed globally using `GLSR.Event`.

## Available Triggers

**`site-reviews/init`**

This event is fired on DOMContentLoaded to initialise Site Reviews. You can manually trigger this if you need to initialise Site Reviews after DOMContentLoaded.

```js
GLSR.Event.trigger('site-reviews/init') // initialise Site Reviews
```

```js
GLSR.Event.on('site-reviews/init', () => {
    // do something here...
})
```

**`site-reviews/excerpts/init`**

This event is fired immediately after Site Reviews is initialised and after every time AJAX pagination is used to initialise the review excerpts.

```js
GLSR.Event.on('site-reviews/excerpts/init', (reviewsEl) => {
    // do something here...
})
```

**`site-reviews/form/handle`**

This event is fired after a review is submitted and the response returned from the server.

```js
GLSR.Event.on('site-reviews/form/handle', (response, formEl) => {
    // do something here...
})
```

**`site-reviews/modal/init`**

This event is fired immediately after Site Reviews is initialised and after every time AJAX pagination is used to initialise the review modals.

```js
GLSR.Event.on('site-reviews/modal/init', () => {
    // do something here...
})
```

**`site-reviews/modal/open`**

This event is fired after a modal is opened.

```js
GLSR.Event.on('site-reviews/modal/open', (modalEl, triggerEl, event) => {
    // do something here...
})
```

**`site-reviews/modal/close`**

This event is fired after a modal is closed.

```js
GLSR.Event.on('site-reviews/modal/close', (modalEl, triggerEl, event) => {
    // do something here...
})
```

**`site-reviews/pagination/init`**

This event is fired immediately after Site Reviews is initialised to initialise the AJAX pagination.

```js
GLSR.Event.on('site-reviews/pagination/init', () => {
    // do something here...
})
```

**`site-reviews/pagination/handle`**

This event is fired after a pagination link or button has been clicked and the response returned from the server. Site Reviews uses this event to initialise the excerpts and modals of the reviews returned in the response.

```js
GLSR.Event.on('site-reviews/pagination/handle', (response) => {
    // do something here...
})
```

**`site-reviews/pagination/popstate`**

This event is fired after the previous/next browser buttons are used to navigate the pagination browser history.

```js
GLSR.Event.on('site-reviews/pagination/popstate', (event) => {
    // event.state holds the saved history state for the page
})
```

## Methods

**Create a custom event**

```js
GLSR.Event.on(name, callback, context)
```

**Create a custom event that can be triggered only once**

```js
GLSR.Event.once(name, callback, context)
```

**Trigger a custom event**

```js
GLSR.Event.trigger(name, ...args)
```

**Remove a custom event**

```js
GLSR.Event.off(name, callback)
```
