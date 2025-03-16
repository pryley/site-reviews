# Block Components

## Usage

```jsx
<AjaxComboboxControl
    endpoint="/site-reviews/v1/shortcode/site_review?option=type"
    onChange={(type) => setAttributes({ type })}
    storeName="site-reviews",
    value={attributes.type}
/>
```

```jsx
<AjaxFormTokenField
    endpoint="/site-reviews/v1/shortcode/site_reviews?option=assigned_posts"
    onChange={(assigned_posts) => setAttributes({ assigned_posts })}
    prefetch={ true }
    storeName="site-reviews",
    value={attributes.assigned_posts}
/>
```

```jsx
<AjaxSearchControl
    endpoint="/site-reviews/v1/shortcode/site_review?option=type"
    onChange={(type) => setAttributes({ type })}
    value={attributes.type}
/>
```

```jsx
<AjaxSelectControl
    endpoint="/site-reviews/v1/shortcode/site_review?option=type"
    onChange={(type) => setAttributes({ type })}
    value={attributes.type}
/>
```

```jsx
<AjaxToggleGroupControl
    endpoint="/site-reviews/v1/shortcode/site_review?option=hide"
    onChange={(hide) => setAttributes({ hide })}
    value={attributes.hide}
/>
```

```jsx
<NoYesControl
    help={ _x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews') }
    onChange={ (schema) => setAttributes({ schema }) }
    label={ _x('Enable the Schema?', 'admin-text', 'site-reviews') }
    value={ attributes.schema }
/>
```
