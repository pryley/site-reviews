const { _x } = wp.i18n;
const { BaseControl, TextControl } = wp.components;
const { isEmpty } = lodash;
const { useInstanceId } = wp.compose;

export default function ConditionalSelectControl({
    children,
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
    const onChangeValue = ( event ) => {
        onChange( event.target.value );
    };
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
            >
                <select
                    id={ id }
                    className="components-select-control__input"
                    onChange={ onChangeValue }
                    aria-describedby={ !! help ? `${ id }__help` : undefined }
                    { ...props }
                >
                    { options.map((option, index) => (
                        <option
                            key={ `${ option.label }-${ option.value }-${ index }` }
                            value={ option.value }
                            disabled={ option.disabled }
                        >
                            { option.label }
                        </option>
                    ))}
                </select>
                { 'custom' === value && children }
            </BaseControl>
        )
    );
    /* eslint-enable jsx-a11y/no-onchange */
}
