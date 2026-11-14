# Block Components

## Usage

### AjaxSelectControl

Display a single-select (non-searchable) dropdown control with values fetched via ajax on load.

```jsx
<AjaxSelectControl
    endpoint="/site-reviews/v1/shortcodes/site_reviews/options/type"
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
    endpoint="/site-reviews/v1/shortcodes/site_reviews/options/type"
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
    endpoint="/site-reviews/v1/shortcodes/site_reviews/options/author"
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
    endpoint="/site-reviews/v1/shortcodes/site_reviews/options/assigned_posts"
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
    endpoint="/site-reviews/v1/shortcodes/site_review/options/hide"
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

## Searchable controls

`AjaxFormTokenField` is drawn by `SearchableChipSelectControl`, and `AjaxComboboxControl` and `AjaxSearchControl` by `SearchableSelectControl`. Both are our own copies of the controls of the same names in `@wordpress/ui`, which WordPress does not ship as a script: they hold none of the package's code, take the same props for what they share (`items`, `value`, `onValueChange`, `onInputValueChange`, `statusContent`, `emptyContent`, `searchPlaceholder`, `label`, `description`, and `hideLabelFromVision` on the multi select) and follow its sizes: a 40px field and 26px chips. They filter nothing: the Ajax components pass the items to show.

- **`SearchableChipSelectControl`** shows the picked items as chips above its search, which always shows its placeholder. It has no button that clears every item, which the package's control has. The list opens at a press on the field, the Down Arrow or typing, and not when the search only takes the focus. From the start of the search the Left Arrow moves the focus to the last picked item; the arrows then move it along the items and back to the search, Backspace or Delete removes the item that has it, and Enter or Space returns to the search. Backspace in an empty search removes the last item.
- **`SearchableSelectControl`** is a button that names the selection. A press, Enter, Space or an arrow opens its search and list; Enter picks the highlighted item, and Escape or Tab closes the list with the focus back on the button. `showClearButton` adds a button that clears the selection, which the package's control does not have.
- The list of either opens over the controls around it, above the field when the window has no room under it. What is typed highlights the first item found. `statusContent` and `emptyContent` are rows of the list: pass `LoadingIndicator` as the status while items are fetched and `emptyContent={ null }` with it, as the Ajax components do.

The rules are in `src/searchable.scss`, which both controls import. A build that bundles one of them emits the rules in its stylesheet, and that stylesheet has to be loaded on the screen: a block lists it in the `editorStyle` of its `block.json`.
