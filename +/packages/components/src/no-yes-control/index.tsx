import { _x } from '@wordpress/i18n';
import {
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { ControlProps } from './types';

const NoYesControl = (props: ControlProps) => {
    return (
        <ToggleGroupControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            {...props}
        >
            <ToggleGroupControlOption
                value={0}
                label={_x('No', 'admin-text', 'site-reviews')}
            />
            <ToggleGroupControlOption
                value={1}
                label={_x('Yes', 'admin-text', 'site-reviews')}
            />
        </ToggleGroupControl>
    );
};

export default NoYesControl;
