import { _x } from '@wordpress/i18n';
import {
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

/**
 * <NoYesControl
 *     help={ _x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews') }
 *     onChange={ (schema) => setAttributes({ schema }) }
 *     label={ _x('Enable the Schema?', 'admin-text', 'site-reviews') }
 *     value={ attributes.schema }
 * />
 * 
 * @version 1.0
 */
const NoYesControl = (props) => {
    return (
        <ToggleGroupControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            { ...props }
        >
            <ToggleGroupControlOption value={ false } label={ _x('No', 'admin-text', 'site-reviews') } />
            <ToggleGroupControlOption value={ true } label={ _x('Yes', 'admin-text', 'site-reviews') } />
        </ToggleGroupControl>
    )
};

export default NoYesControl;
