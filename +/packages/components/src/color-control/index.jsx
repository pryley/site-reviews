import {
  __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
  __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from "@wordpress/block-editor";

const ColorControl = (controlProps) => {
  const { attributeName, label, props } = controlProps;
  const { attributes, clientId, setAttributes } = props;
  const colorSettings = useMultipleOriginColorsAndGradients();
  const customAttrName = `${attributeName}_custom`;
  const setterName = `set${attributeName.charAt(0).toUpperCase()}${attributeName.slice(1)}`;
  const customValue = attributes[customAttrName];
  const styleValue = props[attributeName];
  const setStyleValue = props[setterName];
  return (
    <ColorGradientSettingsDropdown
      __experimentalIsRenderedInSidebar
      panelId={clientId}
      settings={[{
        clearable: true,
        colorValue: styleValue?.color || customValue,
        isShownByDefault: true,
        label,
        onColorChange: (color) => {
          setAttributes({ [customAttrName]: color });
          setStyleValue(color);
        },
        resetAllFilter: () => ({
          [attributeName]: '',
          [customAttrName]: '',
        }),
      }]}
      {...colorSettings}
    />
  )
};

export default ColorControl;
