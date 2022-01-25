# Javascript Events

Site Reviews use a custom Event Emitting system which can be accessed globally at `GLSR.Event`.

## Available Triggers

1. `site-reviews/init`

This event is fired on DOMContentLoaded to initialise Site Reviews. You can manually trigger this if you need to delay loading the Site Reviews script until after DOMContentLoaded.

```js
GLSR.Event.trigger('site-reviews/init') // initialise Site Reviews
```

```js
GLSR.Event.on('site-reviews/init', () => {
    // do something here after Site Reviews has been initialised...
})
```

2. `site-reviews/excerpts/init`

This event is fired immediately after Site Reviews is initialised and after every time AJAX pagination is used.

```js
GLSR.Event.on('site-reviews/excerpts/init', (el) => {
    // el is either document or the HTMLElement of the reviews shortcode
    // do something here...
})
```

3. `site-reviews/form/handle`

This event is fired after a review is submitted and the response returned from the server.

```js
GLSR.Event.on('site-reviews/form/handle', (response, formEl) => {
    // do something here...
})
```

4. `site-reviews/pagination/handle`

This event is fired after a pagination link has been clicked and the response returned from the server. Site Reviews uses this event to initialise the excerpts and modals of the reviews returned in the response.

```js
GLSR.Event.on('site-reviews/pagination/handle', (response) => {
    // do something here...
})
```

5. `site-reviews/pagination/popstate`

This event is fired after the previous/next browser buttons are used to navigate the pagination browser history.

```js
GLSR.Event.on('site-reviews/pagination/popstate', (event) => {
    // `event.state` holds the saved history state for the page.
})
```

## Methods

1. Create a custom event

```js
GLSR.Event.on(name, callback, context) // `context` is the value of `this` provided to `callback`
```

2. Create a custom event that can be triggered only once

```js
GLSR.Event.once(name, callback, context) // `context` is the value of `this` provided to `callback`
```

3. Trigger a custom event

```js
GLSR.Event.trigger(name, ...args)
```

4. Remove a custom event

```js
GLSR.Event.off(name, callback)
```
