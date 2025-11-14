import React, { type ReactElement } from 'react';
import { cloneDeep, unset } from 'lodash';
import { ModuleGroups } from '@divi/module';
import { InternalAttrs, type Module } from '@divi/types';

export const SettingsDesign = (props: Module.Settings.Panel.Props<InternalAttrs>): ReactElement => {
    const { groupConfiguration } = props;
    const groups = cloneDeep(groupConfiguration);
    for (let key in groups) {
        if (!key.startsWith('design')) {
            continue;
        }
        // @ts-expect-error
        if (!groups[key]?.component?.props?.fields) {
            unset(groups, [key]);
        }
    }
    return (
        <ModuleGroups
            groups={groups}
        />
    )
};
