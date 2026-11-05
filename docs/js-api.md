# Javascript API

Site Reviews keeps its javascript in one global, `GLSR`. This page describes it as of Site Reviews 8.4.0. The keys that 8.4.0 replaces keep working while compat mode is on; they are listed under [Deprecated keys](#deprecated-keys).

```text
window.GLSR
├── version     the Site Reviews version, e.g. '9.0.0'
├── config      the values PHP gives to the scripts (read-only)
├── {Module}    Review, Form, Summary, Modal, Event, Request, Util, …
├── lib         third-party libraries and shared modules
├── load        loads a module that nothing on the page has named
└── registry    defines scripts and loads them on demand

window.GLSR_init(root)
```

The public script (`site-reviews.js`) is a bootstrap: it carries `Event`, `Util`, `lib`, `load`, the config and a `Request` whose methods load the request module, and nothing else. The behaviours are script modules that the page loads when a root names one (see [Modules](#modules)): `GLSR.Review` and `GLSR.Summary` arrive with `@site-reviews/reviews`, `GLSR.Form` with `@site-reviews/form`, `GLSR.Modal` with `@site-reviews/modal`. A name is on `GLSR` once its module has loaded, and not before. The admin script (`site-reviews-admin.js`) carries its modules itself. The block editor loads both on one page: they then share `GLSR.Event`, `GLSR.lib` and `GLSR.registry`, and `GLSR.config` holds the subjects of both. Where both configs have a member, the public script's value is kept: `request.ajax.action` is the public action on such a page.

## Naming

- A module is PascalCase (`GLSR.Form`). Its members are camelCase (`GLSR.Form.instances`).
- A singular module belongs to Site Reviews: `Review`, `Form`, `Summary`, `Modal`, `Event`, `Request`, `Util`.
- A plural module belongs to a Site Reviews Premium feature: `Filters`, `Images`, `Themes`. It exists only on a page that loads that feature's script.
- `Editor` is the editor of an [editor screen](#an-editor-screen), whichever plugin provides it. Only one is ever on a page.
- The only lowercase keys are `version`, `config`, `lib`, `load` and `registry`.

## Before the script runs

The Site Reviews script is deferred. An inline script printed directly before it writes `GLSR.version`, `GLSR.config` and `GLSR.Event.on`, so a script that runs after the inline script can read the config and subscribe to an event before Site Reviews has initialised.

```js
GLSR.Event.on('site-reviews/initialized', ({ root }) => {
    // Site Reviews has set up everything inside root
})
```

## Initialising

Site Reviews sets up the page on DOMContentLoaded, and sets up markup that arrives later as it arrives (see [Modules](#modules)). `GLSR_init` sets a page or an element up again; it is a plain global function so that a page builder can call it by name, and it returns a promise that resolves when what it set up is ready.

```js
GLSR_init()         // sets up the whole page (it triggers site-reviews/init)
GLSR_init(root)     // sets up the reviews, forms and summaries inside an element
```

Both end with the `site-reviews/initialized` event, which carries the root. To set up only one kind of markup, use the module's `init(root)` (see below).

## Modules

Each behaviour is a script module in the page's import map, fetched only when something on the page needs it. A rendered root says what it needs in its `data-glsr-modules` attribute, one token per module (`<div class="glsr" data-glsr-modules="form captcha">`); the bootstrap sets the root up with each module it names, on the first scan and whenever a root arrives later, however it arrived. A root names its stylesheets the same way in `data-glsr-styles` (`data-glsr-styles="review star-rating flags pagination button"`), and so does a star rating printed on its own. The plugin prints them beside the first root of a page render; a fragment rendered over admin-ajax or REST carries only the names, and the bootstrap adds a `<link>` for each the page lacks.

| Module | Token | Puts on `GLSR` |
| --- | --- | --- |
| `@site-reviews/reviews` | `reviews` | `Review`, `Summary` |
| `@site-reviews/form` | `form` | `Form` |
| `@site-reviews/captcha` | `captcha` | nothing: it takes over a form's `captcha` |
| `@site-reviews/modal` | `modal` | `Modal` |
| `@site-reviews/request` | `request` | `Request` (in place of the bootstrap's) |
| `@site-reviews/core` | none | nothing: the plumbing every module imports |

A snippet that needs a module on a page where nothing named it asks for it:

```js
GLSR.load('form').then(() => GLSR.Form.find(el))
```

`GLSR.Request` is on every page. Before the request module has loaded it is a shim whose five methods load the module and call through; every method returns a promise either way. A snippet that wants to act when reviews are set up listens to `site-reviews/review/initialized` or `site-reviews/form/initialized` and never has to load anything.

A feature of Site Reviews Premium registers its module the same way (`@site-reviews/{token}`) and adds its token to a root through the `site-reviews/shortcode/attributes` filter.

Every shortcode, block and page-builder element prints its root with the attribute. A theme that prints a list or a review through `glsr_get_reviews()` or `glsr_get_review()` prints the root from the `attributes` of the built object, as the functions' documentation shows, and that string carries the attribute too. A star rating on its own (`glsr_star_rating()`, the WooCommerce ratings) needs no module.

`GLSR_init(eventName, ...args)` triggers the event with that name. It exists for the deprecated `block:` event names (see [js-events.md](js-events.md#deprecated-names)).

## Reviews, forms and summaries

`GLSR.Review`, `GLSR.Form` and `GLSR.Summary` have the same four members.

```js
GLSR.Form.instances       // the instances on the page (a read-only copy)
GLSR.Form.find(el)        // the instance of an element
GLSR.Form.init(root)      // initialises the forms inside root (default: document) and returns their instances
GLSR.Form.destroy(root)   // destroys the instances inside root; with no root, the instances whose element has left the page
```

`init(root)` creates an instance for each new element, initialises an existing instance again, and returns the instances it touched. Use it after you insert markup:

```js
container.innerHTML = response.html
const [form] = GLSR.Form.init(container)
```

Every instance names its element `el`.

`GLSR.Util.feature` builds a module with these four members. Site Reviews builds its own three with it, and a script that adds a module of its own can do the same:

```js
const { module } = GLSR.Util.feature(
    (root) => root.querySelectorAll('.my-widget'),   // the elements inside a root
    (el) => ({ el, init () {}, destroy () {} })      // the instance of an element
)
GLSR.MyWidgets = module
```

**`GLSR.Review`**

A list of reviews, or a single review.

```js
const review = GLSR.Review.find(el)
review.el             // the element
review.pagination     // the list's pagination, or null when it has none that uses AJAX
review.excerpts       // the "read more" excerpts of the list
review.init()
review.destroy()

GLSR.Review.open(id, values)          // opens a review in the modal
GLSR.Review.pagination.instances      // every pagination on the page
GLSR.Review.pagination.find(el)
GLSR.Review.excerpts.init(el)         // initialises the excerpts inside el
```

**`GLSR.Form`**

The review form.

```js
const form = GLSR.Form.find(el)
form.el               // the <form> element
form.button           // the submit button
form.validation
form.conditions
form.captcha
form.session
form.isActive
form.init()
form.destroy()
form.submit()
```

**`GLSR.Summary`**

The rating summary.

```js
const summary = GLSR.Summary.find(el)
summary.el
summary.update(html)  // replaces the content and triggers site-reviews/summary/updated
```

## Modal, Event, Request and Util

```js
GLSR.Modal    // { init, open, close, get }
GLSR.Event    // { on, off, once, trigger }
GLSR.Request  // { send, submit, review, pagedReviews, log }
GLSR.Util     // { debounce, dom, fadeIn, fadeOut, feature, historyState, isEmpty, parseJson, selectText, throttle }
```

- `GLSR.Modal` is described in [js-modal.md](js-modal.md).
- `GLSR.Event` and the events Site Reviews triggers are described in [js-events.md](js-events.md).
- `GLSR.Request` is described in [js-requests.md](js-requests.md). `send` uses the REST API, and sends the same request over admin-ajax when the REST API is unavailable; `submit`, `review` and `pagedReviews` are the requests Site Reviews makes with it; `log` writes a failed request to the browser console.
- `GLSR.Util` holds helper functions that know nothing about Site Reviews. Both scripts carry the same ten. `GLSR.Util.feature` is described under [Reviews, forms and summaries](#reviews-forms-and-summaries). `GLSR.Util.historyState(state)` is the state to write with `history.pushState` or `replaceState` on the front end: it keeps the id WordPress 7.1's interactivity runtime stamps on the current entry, without which the runtime reloads the page when the visitor goes back to that entry.

## The admin script

```js
GLSR.Notice      // { add(notices), error(message), notice(level, message) }
GLSR.Rating      // { init(selector, options), rebuild(), destroy() }
GLSR.Tinymce     // { create(editorId) }
GLSR.Event
GLSR.Request
GLSR.Util
GLSR.lib.tippy   // { tippy, plugins: { followCursor } }
```

`GLSR.Tinymce` is the shortcode button of the classic editor. Site Reviews 9.0 removes the button, and `GLSR.Tinymce` and `GLSR.config.tinymce` with it.

## An editor screen

```js
GLSR.Editor      // { init(config) }
```

A screen that is one full-page editor has an editor script, which defines `GLSR.Editor` and starts nothing. PHP prints one call directly after it, `GLSR.Editor.init(config)`, which starts the editor with the config PHP composed for it. On a screen that loads neither of the scripts above, `GLSR` holds `Editor` and nothing else.

`init` keeps the config in a `@wordpress/data` store, `site-reviews/editor`. `wp.data.select('site-reviews/editor').getConfig()` returns it: one frozen object, the same on every read. A script that runs beside the editor reads the editor's config there.

The Site Reviews Premium editors of forms, themes, alerts and emails are started this way.

## Config

`GLSR.config` holds the values that PHP gives to the scripts. It is read-only: the script freezes it. The values are grouped by subject, and the keys are camelCase.

To change or add a value, use the `site-reviews/assets/config` filter. `$bundle` is `public` or `admin`.

```php
add_filter('site-reviews/assets/config', function (array $config, string $bundle) {
    if ('public' === $bundle) {
        $config['rating']['clearable'] = true;
    }
    return $config;
}, 10, 2);
```

The filters `site-reviews/enqueue/public/localize` and `site-reviews/enqueue/admin/localize` are deprecated. While compat mode is on they keep working, and they receive the values under the names they had before 8.4.0.

The public script:

```text
GLSR.config
├── nameprefix   'site-reviews'
├── debug        { enabled, url } (see Debug)
├── compat       true when PHP printed the compat script (see Deprecated keys)
├── request      { url, nonce, routes, language, ajax: { url, action, rest } }
├── captcha      { type, class, sitekey, theme, language, tokenField, urls: { module, nomodule }, … }
├── modal        { wrappedBy }
├── pagination   { fixed, urlParameter }
├── rating       { clearable, tooltip }
├── validation   { field, form, …, strings }
├── text         { closeModal }
└── {feature}    the values of a Site Reviews Premium feature, e.g. images
```

- `debug.enabled` is `true` when PHP printed the debug script. `debug.url` is the address of the debug script, present only when it can be loaded with `?glsr-debug`.
- `request.url` is the REST API root of Site Reviews; `request.nonce` is `false` for a visitor who is not logged in. `request.routes` names the route that a form is posted to, by the form's action (see [js-requests.md](js-requests.md#the-route-of-a-form)). `request.ajax` is admin-ajax: its `url`, the `action` of the routes that only admin-ajax has, and the `rest` action that carries a REST request when the REST API is unavailable. `request.language` is the language of the page under Polylang or WPML, and is absent otherwise; every request carries it as `lang`.
- `captcha` holds the settings of the selected captcha service. It is empty when no captcha is used. `type` is the service; a service can add its own keys, such as Procaptcha's `captchaType`.
- `pagination.fixed` lists the selectors of the fixed elements that the scroll to the top of the reviews must clear. `pagination.urlParameter` is `false` when the pagination does not change the URL.
- `validation` holds the CSS classes of the selected plugin style, and `validation.strings` holds the validation messages.

The admin script:

```text
GLSR.config
├── nameprefix, debug, compat
├── request      { url, nonce, ajax: { url, action, rest } }
├── nonce        { 'clear-console', 'fetch-console', … }
├── rating       { min, max }
├── text         { cancel, cancelling, importError, rollbackError, searching, … }
├── tinymce      { plugins, required }
├── urls         { addons }
└── {feature}
```

## Libraries

`GLSR.lib` holds the third-party libraries and shared modules that are on the page.

```js
GLSR.lib.register(name, value)   // adds an entry
GLSR.lib.tippy                   // reads an entry
```

- The first registration of a name wins; an entry cannot be replaced.
- Read an entry when you use it, and handle `undefined`: a library is on the page only when something on that page needs it.

## Registry

`GLSR.registry` runs the compat and debug scripts of Site Reviews at the right time. It is on its way out: the behaviours are [modules](#modules) now, and `GLSR.load(token)` is how a script asks for one.

```js
GLSR.registry.define(id, factory)   // defines a script; the factory runs when the registry boots
GLSR.registry.boot()                // runs every defined factory once, on DOMContentLoaded
GLSR.registry.load(id)              // returns a Promise that resolves when the script has defined
GLSR.registry.register(urls)        // { id: url }, the scripts that load(id) can fetch
```

## Debug

Debug mode writes information to the browser console. It never changes what the scripts do. It is a script of its own (`site-reviews-debug.js`, and `site-reviews-admin-debug.js` in the admin) that is on the page only when debug mode is on.

Debug mode is off by default. Turn it on with the "Debug Mode" setting on the Advanced tab of the settings. `WP_DEBUG` does not turn it on. The filter receives the setting and has the last word; `$bundle` is `public` or `admin`.

```php
add_filter('site-reviews/debug/assets', fn ($debug, $bundle) => 'public' === $bundle, 10, 2);
```

To see it for one page view on any site, add `?glsr-debug` to the address of a public page. The public script then loads the debug script itself, so this also works on a cached page.

- Only the presence of the parameter is read, never its value.
- PHP does not read the parameter: the page is the same with and without it.
- The debug script only writes to the console. It sends no request, stores nothing and changes nothing on the page.
- Nothing is remembered: the next page view has no debug mode unless its address has the parameter too.
- It applies to public pages only. The admin follows the setting and the filter.

To forbid it:

```php
add_filter('site-reviews/debug/on-request', '__return_false');
```

In debug mode:

- `GLSR.Event.listeners()` returns the number of listeners of each event; `GLSR.Event.listeners(name)` returns the listeners of one event.
- `GLSR.registry.defined()` returns the defined ids in order, and whether each has run.
- `GLSR.Request` logs each request: the path, whether it used the REST API or the fallback, the status, and the time it took.
- `GLSR.Form` logs each field's validation when a form is submitted (the rule, the value, pass or fail) and the response code.
- `GLSR.Review.pagination` logs each page change.
- A deprecated key logs a warning, once, that names its replacement. So does a script that listens to a deprecated or a removed event name, or that triggers one.
- A script that listens to an event of Site Reviews that nothing fires, such as a misspelt name, logs a warning that lists the events of that module.

## Deprecated keys

Site Reviews 8.4.0 renamed the keys below. The old keys are deprecated: use the new ones in anything you write. They keep working through compat mode, which is on by default: the "Compatibility Mode" setting on the Advanced tab of the settings. In debug mode, the first use of an old key logs a warning that names its replacement.

Compat mode is a script of its own (`site-reviews-compat.js`, and `site-reviews-admin-compat.js` in the admin), printed directly after the script and before any script that depends on it. With compat mode off, the old keys do not exist. The names that only the add-ons of earlier versions used (`GLSR.addons`, `GLSR.forms`, `GLSR.pagination`, the `form` of a form instance) are gone: those add-ons do not run on this version, and a name of a module cannot be kept before the module has loaded. To check that a site no longer uses one, turn the setting off. The filter receives the setting and has the last word; `$bundle` is `public` or `admin`.

```php
add_filter('site-reviews/compat/assets', '__return_false');
```

The public script:

| Deprecated | Use instead |
| --- | --- |
| `GLSR.action` | `GLSR.config.request.ajax.action` |
| `GLSR.ajax` | `GLSR.Request`, with a REST route |
| `GLSR.ajax_pagination`, `GLSR.ajaxpagination` | `GLSR.config.pagination.fixed` |
| `GLSR.ajax_url`, `GLSR.ajaxurl` | `GLSR.config.request.ajax.url` |
| `GLSR.captcha` | `GLSR.config.captcha` (`captcha_type` is `captchaType`, `token_field` is `tokenField`) |
| `GLSR.modal_wrapped_by` | `GLSR.config.modal.wrappedBy` |
| `GLSR.nameprefix` | `GLSR.config.nameprefix` |
| `GLSR.request` | `GLSR.Request` (`send` no longer takes `legacy`, and `data` has no replacement) |
| `GLSR.rest_nonce` | `GLSR.config.request.nonce` |
| `GLSR.rest_url` | `GLSR.config.request.url` |
| `GLSR.stars_config`, `GLSR.starsconfig` | `GLSR.config.rating` |
| `GLSR.text` | `GLSR.config.text` (`close_modal` and `closemodal` are `closeModal`) |
| `GLSR.url_parameter`, `GLSR.urlparameter` | `GLSR.config.pagination.urlParameter` |
| `GLSR.Utils` | `GLSR.Util` |
| `GLSR.validation_config`, `GLSR.validationconfig` | `GLSR.config.validation` |
| `GLSR.validation_strings`, `GLSR.validationstrings` | `GLSR.config.validation.strings` |
| `GLSR.Modal.modify(id, callback)` | `GLSR.Modal.get(id)` |
| `dom` of a modal instance | the instance's `header`, `content`, `footer`, `style` and `hideClose` |

The names without an underscore (`GLSR.ajaxurl`) are those that Site Reviews had before 8.0.1.

The admin script:

| Deprecated | Use instead |
| --- | --- |
| `GLSR.action` | `GLSR.config.request.ajax.action` |
| `GLSR.addons` | `GLSR.config.{feature}` and the feature's module |
| `GLSR.addonsurl` | `GLSR.config.urls.addons` |
| `GLSR.ajax` | `GLSR.Request` |
| `GLSR.autosize` | nothing; a textarea grows with CSS `field-sizing` |
| `GLSR.keys` | `event.key` |
| `GLSR.maxrating` | `GLSR.config.rating.max` |
| `GLSR.minrating` | `GLSR.config.rating.min` |
| `GLSR.nameprefix` | `GLSR.config.nameprefix` |
| `GLSR.nonce` | `GLSR.config.nonce` |
| `GLSR.notices` | `GLSR.Notice` |
| `GLSR.shortcode` | `GLSR.Tinymce` |
| `GLSR.shortcodes` | `GLSR.config.tinymce.required` |
| `GLSR.stars` | `GLSR.Rating` |
| `GLSR.text` | `GLSR.config.text` (the keys are camelCase) |
| `GLSR.tinymce` | `GLSR.config.tinymce.plugins` |
| `GLSR.Tippy` | `GLSR.lib.tippy` |
| `GLSR.Utils` | `GLSR.Util` |

Three keys are removed in 8.4.0 with no replacement:

- `GLSR.state`, which nothing read.
- `GLSR.Event.events`, which was never meant to be public (use `GLSR.Event.listeners()` in debug mode).
- `GLSR.filters` in the admin script. The fixed choices of a searchable dropdown are now on its element, in `data-options`.
