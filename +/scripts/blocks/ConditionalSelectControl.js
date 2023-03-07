const { _x } = wp.i18n;
const { BaseControl, SelectControl, TextControl } = wp.components;
const { isEmpty } = lodash;
const { useInstanceId } = wp.compose;

export default function ConditionalSelectControl({
    children,
    custom_value = 'custom',
    help,
    label,
    onChange,
    options = [],
    className,
    hideLabelFromVision,
    selectedValue,
    ...props
}) {
    const instanceId = useInstanceId( ConditionalSelectControl );
    const id = `inspector-select-control-${ instanceId }`;
    const { value } = props;
    // Disable reason: A select with an onchange throws a warning
    /* eslint-disable jsx-a11y/no-onchange */
    return (
        ! isEmpty( options ) && (
            <BaseControl
                label={ label }
                hideLabelFromVision={ hideLabelFromVision }
                id={ id }
                help={ help }
                className={ className }
                __nextHasNoMarginBottom
            >
                <SelectControl
                    id={ id }
                    className="components-select-control__input"
                    aria-describedby={ !! help ? `${ id }__help` : undefined }
                    onChange={ onChange }
                    options={ options }
                    { ...props }
                    __next36pxDefaultSize
                    __nextHasNoMarginBottom
                />
                { custom_value === value && children }
            </BaseControl>
        )
    );
    /* eslint-enable jsx-a11y/no-onchange */
}
