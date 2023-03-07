const { CheckboxControl } = wp.components;
const { useState } = wp.element;

export const CheckboxControlList = (options, hide, setAttributes) => {
    const checkboxes = [];
    jQuery.each(options, (name, label) => {
        const [isChecked, setChecked] = useState(false);
        const isAlreadyChecked = hide.split(',').indexOf(name) > -1;
        checkboxes.push((
            <CheckboxControl
                key={ `hide-${ name }` }
                className={ 'glsr-checkbox-control' }
                checked={ isAlreadyChecked || isChecked }
                label={ label }
                onChange={ checked => {
                    setChecked(checked);
                    hide = _.without(_.without(hide.split(','), ''), name);
                    if (checked) {
                        hide.push(name);
                    }
                    setAttributes({ hide: hide.toString() });
                }}
                __nextHasNoMarginBottom
            />
        ));
    });
    return checkboxes;
};
