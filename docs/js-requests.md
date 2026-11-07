# Javascript Requests

Site Reviews sends its requests with `GLSR.Request`.

```js
const { data, status, success } = await GLSR.Request.send({
    method: 'POST',                 // 'GET' when omitted; PUT, PATCH and DELETE go as a POST with the X-HTTP-Method-Override header, which WordPress honours
    path: 'my-addon/route',         // relative to the site-reviews/v1 namespace (GLSR.config.request.url); a path that starts with / is read from the REST root, such as /wp/v2/users/me
    params: { page: 2 },            // query parameters; nested objects become key[sub]=value
    body: formData,                 // the request body: a FormData, or an object that is sent as JSON
    signal: controller.signal,      // an AbortSignal, optional: an aborted request is given up, and is not sent again over admin-ajax
})
// data is the response of the route, status is its HTTP status, success is true for a 2xx status
```

In place of `method` and `path`, `route` names an entry of `GLSR.config.request.routes` (see [The route of a form](#the-route-of-a-form)). A `{placeholder}` in the entry's path is filled from the option of that name:

```js
GLSR.Request.send({ route: 'status', id: 7, body })   // PUT reviews/7/status, when routes.status is { method: 'PUT', path: 'reviews/{id}/status' }
```

## How a request is sent

1. The request goes to the REST API of WordPress, with `Accept: application/json`, so WordPress answers a fatal error as JSON.
2. If the REST API is unavailable on the site (a security plugin blocks it, or the response is not JSON), the same request goes to admin-ajax, and Site Reviews runs the same route there. A route of WordPress runs there only for a logged-in user, only `/wp/v2/users/me`, and only when WordPress would answer it over `/wp-json`: a site that disables the REST API keeps it disabled through admin-ajax, while the plugin's own routes, which the page needs, still answer. You pass nothing extra for this, and the response is the same. Some answers say that the REST API as a whole is unavailable: a 401 or a 403, the codes `rest_disabled`, `rest_forbidden` and `rest_not_logged_in`, and a `window.fetch` that another script has replaced. After one of those, the remaining requests of the page go to admin-ajax directly. A removed route, a response that is not JSON or a network error sends only that one request again.
3. A status of 500 or above is the final answer, whether the response is JSON or not: the route failed, and it is not run a second time over admin-ajax.
4. If the nonce of a logged-in visitor has expired, Site Reviews gets a new one and sends the request again.

Under Polylang or WPML, every request carries the language of the page as `lang` (`GLSR.config.request.language`), over the REST API and over admin-ajax alike, so the route answers in that language.

An error that the route itself returns, such as a 400 for an invalid form, is the final answer: `success` is `false` and `data` holds the error.

A 401 or a 403 is read as a blocked REST API, and the request is sent again over admin-ajax, unless the error code begins with `glsr_`. A route that refuses a request must therefore answer with such a code. A `permission_callback` that returns `false` is answered by WordPress with `rest_forbidden`, which is sent again; return a `WP_Error` instead:

```php
'permission_callback' => function () {
    if (!current_user_can('edit_posts')) {
        return new WP_Error('glsr_forbidden', 'You cannot do this.', ['status' => 403]);
    }
    return true;
},
```

The route must be registered in the `site-reviews/v1` namespace:

```php
add_action('rest_api_init', function () {
    register_rest_route('site-reviews/v1', '/my-addon/route', [
        'callback' => 'my_addon_route',
        'methods' => 'POST',
        'permission_callback' => '__return_true',
    ]);
});
```

## The requests of Site Reviews

```js
GLSR.Request.submit(formData)             // submits a form to the route of its action
GLSR.Request.review(id, values)           // fetches a review to show in the modal
GLSR.Request.pagedReviews(values)         // fetches a page of reviews: { atts, page, schema, url }
```

## Logging a failed request

`GLSR.Request.log` writes a failed request to the browser console at the level of what happened. A 4xx status is the server saying no (an invalid form, an expired session) and logs a warning. Any other status is a fault (a server error, a lost connection) and logs an error.

```js
const { data, status, success } = await GLSR.Request.send({ path: 'my-addon/route' })
if (!success) {
    GLSR.Request.log(status, data)
}
```

## The route of a form

A form that Site Reviews sets up (`GLSR.Form`) is submitted by Site Reviews. `GLSR.Request.submit` reads the form's `site-reviews[_action]` field and posts the form to the route that `GLSR.config.request.routes` names for that action. The review form's action is `submit-review`, and its route is `submissions`.

`GLSR.config.request.routes` only holds the actions that Site Reviews sends by their name, which are those of forms. A request that your own script sends with `GLSR.Request.send` names its path and is not in it.

To submit a form of your own, register its route and name it for the action:

```php
add_filter('site-reviews/rest-api/routes', function (array $routes) {
    $routes['my-action'] = 'my-addon/my-action'; // POST site-reviews/v1/my-addon/my-action
    $routes['my-other-action'] = ['method' => 'PUT', 'path' => 'my-addon/my-thing']; // a route that is not a POST
    return $routes;
});
```

The route receives the form's fields in the `site-reviews` parameter. A controller that extends `Controllers\Api\Version1\AbstractRestController` has three helpers for it: `formRequest($request, $action)` returns the fields as the plugin's `Request`, with the action of the route and the captcha token; `lock($action)` takes the lock that prevents parallel submissions; `refuse($code, $message, $status)` returns the error of a refused request, with a `glsr_` code.

An action without a route is deprecated. While compat mode is on, its form is posted to admin-ajax as it was before 9.0.0, and debug mode logs a warning. With compat mode off the form is not sent.

## The searchable dropdowns of the admin

A dropdown that is rendered with `views/partials/listtable/filter.php` searches the route that its `data-search` attribute names, after `site-reviews/v1/`, and offers the fixed choices in its `data-options` attribute. Without a route it has no search box.

```text
GET site-reviews/v1/search/assigned-posts?search=…   posts that have a review assigned
GET site-reviews/v1/search/assigned-users?search=…   users that have a review assigned
GET site-reviews/v1/search/posts?search=…            every published post, with its url
GET site-reviews/v1/search/users?search=…            every user, with the nickname and the url of the author page
```

- Each answers with `[{ id, title }]` and the fields named above, and searches a number as an ID.
- Each requires the capability that shows the reviews in the admin.
- A filter of the reviews table names its route in `searchRoute()`.

The Strings tab of the settings searches the translatable strings of the plugin with `GET site-reviews/v1/search/strings?search=…&exclude[]=…`, which answers with `[{ desc, domain, id, p1, s1, text }]` and requires the capability that shows the settings.

## Debug

In debug mode, each request logs its method, path, transport (REST or admin-ajax), status and time to the browser console. See [js-api.md](js-api.md#debug).
