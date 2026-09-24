# Server Side Block Renderer

## Usage

### ServerSideBlockRenderer

Render a dynamic block through core's `ServerSideRender`, with its toolbar and inspector controls composed from keyed `controls` and `panels` maps that pass through the `site-reviews.blocks.controls` and `site-reviews.blocks.panels` filters.

```jsx
<ServerSideBlockRenderer
    controls={{
        id: <TextControl key="id" label="Custom ID" onChange={(id) => setAttributes({ id })} value={attributes.id} />,
    }}
    panels={{
        advanced: { controls: ['id'] },
    }}
    props={props}
/>
```

### BlockPanels

The composition alone: the same `controls` and `panels` maps placed into the block's toolbar and inspector, for a block that renders itself.

```jsx
<>
    <BlockPanels controls={controls} panels={panels} props={props} />
    <div {...useBlockProps()}>{/* the block's own rendering */}</div>
</>
```
