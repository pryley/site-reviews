import React, { type ReactElement } from 'react';
import { set } from 'lodash';

import {
    ModuleGroups,
} from '@divi/module';
import {
    type BlogAttrs,
    type Module,
} from '@divi/types';

import {
    isVisibleFields,
    isVisibleGroup,
} from './callbacks';


/**
 * Design panel component for the Blog module settings modal.
 *
 * @param {Module.Settings.Panel.Props} param0 Design panel props.
 *
 * @returns {ReactElement}
 */
export const SettingsDesign = ({
    groupConfiguration,
}: Module.Settings.Panel.Props<BlogAttrs>): ReactElement => {
    //  Insert props value to `designOverlay` group.
    if (groupConfiguration?.designOverlay?.component) {
        set(groupConfiguration, ['designOverlay', 'component', 'props', 'fields', 'color', 'visible'], isVisibleFields);
        set(groupConfiguration, ['designOverlay', 'component', 'props', 'fields', 'overlayDecorationBackground', 'visible'], isVisibleFields);
        set(groupConfiguration, ['designOverlay', 'component', 'props', 'fields', 'icon', 'visible'], isVisibleFields);
    }

    // Insert props value to `designBorder` group.
    if (groupConfiguration?.designBorder?.component) {
        set(groupConfiguration, ['designBorder', 'component', 'props', 'fields', 'postDecorationBorder', 'component', 'props', 'visible'], isVisibleGroup);
        set(groupConfiguration, ['designBorder', 'component', 'props', 'fields', 'fullwidthDecorationBorder', 'component', 'props', 'visible'], isVisibleGroup);
    }

    return (
        <ModuleGroups
            groups={groupConfiguration}
        />
    );
};
