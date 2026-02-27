import {
  __experimentalToolsPanelItem as ToolsPanelItem,
  __experimentalUnitControl as DefaultUnitControl,
} from '@wordpress/components';

const UnitControl = (controlProps) => {
  const {
    attributeName,
    defaultValue = '1em',
    help,
    isLarge = false,
    label,
    props,
    units,
  } = controlProps;
  const { attributes, setAttributes } = props;
  const value = attributes[attributeName];
  return (
    <ToolsPanelItem
      hasValue={() => value !== defaultValue}
      isShownByDefault
      label={label}
      onDeselect={() => setAttributes({ [attributeName]: defaultValue })}
      style={{ gridColumn: `span ${isLarge ? 2 : 1}` }}
    >
      <DefaultUnitControl
        __next40pxDefaultSize
        help={help}
        isResetValueOnUnitChange
        label={label}
        min={0}
        onChange={(newValue) => setAttributes({ [attributeName]: newValue })}
        units={units}
        value={value}
      />
    </ToolsPanelItem>
  );
};

export default UnitControl;
