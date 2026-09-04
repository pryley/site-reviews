# Javascript Requests

```js
const response = await GLSR.request.send({
    method: 'POST',
    path: 'my-addon/route',                                    // relative to the site-reviews/v1 namespace (GLSR.rest_url)
    params: { page: 2 },                                       // query parameters; nested objects become key[sub]=value
    body: formData,                                            // the POST body, FormData
    legacy: () => GLSR.request.data('my-action', { page: 2 }), // fallback admin-ajax body
})
// response is { success: boolean, data: object }
```
