# Block Components

## Usage

### AjaxSelectControl

Display a single-select (non-searchable) dropdown control with values fetched via ajax on load.

```jsx
<AjaxSelectControl
    endpoint="/site-reviews/v1/shortcode/site_reviews?option=type"
    fallback={
        <BaseControl __nextHasNoMarginBottom>
            <Notice status="warning" politeness="polite" isDismissible={ false }>
                { _x('Nothing found.', 'admin-text', 'site-reviews') }
            </Notice>
        </BaseControl>
    }
    help={ _x('The control description.', 'admin-text', 'site-reviews') }
    onChange={(type) => setAttributes({ type })}
    storeName="site-reviews",
    value={attributes.type}
/>
```

### AjaxComboboxControl

Display a single-select (searchable) dropdown control with values fetched via ajax on load.

```jsx
<AjaxComboboxControl
    endpoint="/site-reviews/v1/shortcode/site_reviews?option=type"
    fallback={
        <BaseControl __nextHasNoMarginBottom>
            <Notice status="warning" politeness="polite" isDismissible={ false }>
                { _x('Nothing found.', 'admin-text', 'site-reviews') }
            </Notice>
        </BaseControl>
    }
    help={ _x('The control description.', 'admin-text', 'site-reviews') }
    onChange={(type) => setAttributes({ type })}
    storeName="site-reviews",
    value={attributes.type}
/>
```

### AjaxSearchControl

Display a single-select (searchable) dropdown control with values fetched via ajax on search.

```jsx
<AjaxSearchControl
    endpoint="/site-reviews/v1/shortcode/site_reviews?option=author"
    help={ _x('The control description.', 'admin-text', 'site-reviews') }
    onChange={(author) => setAttributes({ author })}
    prefetch={true}
    storeName="site-reviews",
    value={attributes.author}
/>
```

### AjaxFormTokenField

Display a multi-select (searchable) dropdown control with values fetched via ajax on search.

```jsx
<AjaxFormTokenField
    endpoint="/site-reviews/v1/shortcode/site_reviews?option=assigned_posts"
    help={ _x('The control description.', 'admin-text', 'site-reviews') }
    onChange={(assigned_posts) => setAttributes({ assigned_posts })}
    prefetch={true}
    storeName="site-reviews",
    value={attributes.assigned_posts}
/>
```

### AjaxToggleGroupControl

Display a group of toggle controls with labels and values fetched via ajax.

```jsx
<AjaxToggleGroupControl
    endpoint="/site-reviews/v1/shortcode/site_review?option=hide"
    onChange={(hide) => setAttributes({ hide })}
    value={attributes.hide}
/>
```

### NoYesControl

Display a textual toggle control with no and yes as the values.

```jsx
<NoYesControl
    help={ _x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews') }
    onChange={ (schema) => setAttributes({ schema }) }
    label={ _x('Enable the Schema?', 'admin-text', 'site-reviews') }
    value={ attributes.schema }
/>
```
