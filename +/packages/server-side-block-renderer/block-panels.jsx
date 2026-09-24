import { __experimentalToolsPanel as ToolsPanel, PanelBody } from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import { BlockControls, InspectorControls } from '@wordpress/block-editor';
import { useMemo } from '@wordpress/element';

const defaultPanelTitles = {
    display: _x('Display', 'admin-text', 'site-reviews'),
    hide: _x('Hide', 'admin-text', 'site-reviews'),
    settings: _x('Settings', 'admin-text', 'site-reviews'),
    text: _x('Text', 'admin-text', 'site-reviews'),
};

const allowedInspectorGroups = [
    'advanced',
    'background',
    'bindings',
    'block', // special group for block toolbar controls
    'border',
    'color',
    'default',
    'dimensions',
    'effects',
    'filter',
    'list',
    'position',
    'styles',
    'typography',
];

/**
 * A block's inspector and toolbar controls from two keyed maps: `controls`
 * holds the elements by key, `panels` lists which keys each panel shows,
 * under which inspector group. Both maps pass through the
 * `site-reviews.blocks.controls` and `site-reviews.blocks.panels` filters
 * with the block's props, so another plugin can place, replace or remove
 * a control by its key.
 */
const BlockPanels = ({
    controls = {},
    panels = {},
    props,
}) => {
    const filteredControls = useMemo(
        () => applyFilters('site-reviews.blocks.controls', controls, props),
        [controls, props]
    );
    const filteredPanels = useMemo(
        () => applyFilters('site-reviews.blocks.panels', panels, props),
        [panels, props]
    );

    const normalizedPanels = Object.entries(filteredPanels).reduce((acc, [panelKey, panel]) => {
        const group = allowedInspectorGroups.includes(panel.group || panelKey)
            ? (panel.group || panelKey)
            : 'default';
        const normalizedPanel = {
            ...panel,
            controls: Array.isArray(panel.controls) ? panel.controls : [],
            title: panel.title || defaultPanelTitles[panelKey] || null,
        };
        if (0 === normalizedPanel.controls.length) {
            return acc;
        }
        acc[group] = acc[group] || {};
        acc[group][panelKey] = normalizedPanel;
        return acc;
    }, {});

    const renderControls = (controlsArray) => {
        return controlsArray
            .filter((controlKey) => controlKey in filteredControls)
            .map((controlKey) => filteredControls[controlKey]);
    }

    return (
        <>
            {Object.entries(normalizedPanels).map(([group, panels]) => {
                if ('block' === group) {
                    return (
                        <BlockControls group={group} key={group}>
                            {Object.entries(panels).map(([panelKey, panel]) => renderControls(panel.controls))}
                        </BlockControls>
                    )
                }
                return (
                    <InspectorControls group={group} key={group}>
                        {Object.entries(panels).map(([panelKey, panel]) => {
                            const { controls, ...panelProps } = panel; // Exclude controls
                            if (group === panelKey) {
                                return renderControls(controls);
                            }
                            if (panelProps.resetAll) {
                                const { title: label, ...toolsPanelProps } = panelProps;
                                return (
                                    <ToolsPanel key={panelKey} label={label} {...toolsPanelProps}>
                                        {renderControls(controls)}
                                    </ToolsPanel>
                                )
                            }
                            return (
                                <PanelBody key={panelKey} {...panelProps}>
                                    {renderControls(controls)}
                                </PanelBody>
                            )
                        })}
                    </InspectorControls>
                )
            })}
        </>
    );
};

export default BlockPanels;
