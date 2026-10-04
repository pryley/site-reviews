# Javascript Requests

Site Reviews sends its requests with `GLSR.Request`.

```js
const { data, status, success } = await GLSR.Request.send({
    method: 'POST',                 // 'GET' when omitted
    path: 'my-addon/route',         // relative to the site-reviews/v1 namespace (GLSR.config.request.url)
    params: { page: 2 },            // query parameters; nested objects become key[sub]=value
    body: formData,                 // the request body, a FormData
})
// data is the response of the route, status is its HTTP status, success is true for a 2xx status
```

## How a request is sent

1. The request goes to the REST API of WordPress.
2. If the REST API is unavailable on the site (a security plugin blocks it, or the response is not JSON), the same request goes to admin-ajax, and Site Reviews runs the same route there. You pass nothing extra for this, and the response is the same.
3. If the nonce of a logged-in visitor has expired, Site Reviews gets a new one and sends the request again.

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

## The route of a form

A form that Site Reviews sets up (`GLSR.Form`) is submitted by Site Reviews. `GLSR.Request.submit` reads the form's `site-reviews[_action]` field and posts the form to the route that `GLSR.config.request.routes` names for that action. The review form's action is `submit-review`, and its route is `submissions`.

`GLSR.config.request.routes` only holds the actions that Site Reviews sends by their name, which are those of forms. A request that your own script sends with `GLSR.Request.send` names its path and is not in it.

To submit a form of your own, register its route and name it for the action:

```php
add_filter('site-reviews/rest-api/routes', function (array $routes) {
    $routes['my-action'] = 'my-addon/my-action'; // POST site-reviews/v1/my-addon/my-action
    return $routes;
});
```

The route receives the form's fields in the `site-reviews` parameter. A controller that extends `Controllers\Api\Version1\AbstractRestController` has three helpers for it: `formRequest($request, $action)` returns the fields as the plugin's `Request`, with the action of the route and the captcha token; `lock($action)` takes the lock that prevents parallel submissions; `refuse($code, $message, $status)` returns the error of a refused request, with a `glsr_` code.

An action without a route is deprecated. While compat mode is on, its form is posted to admin-ajax as it was before 8.4.0, and debug mode logs a warning. With compat mode off the form is not sent.

## The searchable dropdowns of the admin

A dropdown that is rendered with `views/partials/listtable/filter.php` searches the route that its `data-search` attribute names, after `site-reviews/v1/`, and offers the fixed choices in its `data-options` attribute. Without a route it has no search box.

```text
GET site-reviews/v1/search/assigned-posts?search=…   posts that have a review assigned
GET site-reviews/v1/search/assigned-users?search=…   users that have a review assigned
GET site-reviews/v1/search/users?search=…            every user
```

- Each answers with `[{ id, title }]`, and searches a number as an ID.
- Each requires the capability that shows the reviews in the admin.
- A filter of the reviews table names its route in `searchRoute()`.

## Debug

In debug mode, each request logs its method, path, transport (REST or admin-ajax), status and time to the browser console. See [js-api.md](js-api.md#debug).
