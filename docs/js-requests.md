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
GLSR.Request.submit(formData)             // submits a review form
GLSR.Request.review(id, values)           // fetches a review to show in the modal
GLSR.Request.pagedReviews(values)         // fetches a page of reviews: { atts, page, schema, url }
```

## Debug

In debug mode, each request logs its method, path, transport (REST or admin-ajax), status and time to the browser console. See [js-api.md](js-api.md#debug).
