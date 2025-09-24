import React, {
    type ReactElement,
    useEffect,
    useState,
} from 'react';
import { set } from 'lodash';

import { ModuleGroups } from '@divi/module';
import { loggedFetch } from '@divi/rest';
import {
  type FieldLibrary,
  type Module,
} from '@divi/types';

import {
    type Item,
    type ModuleAttrs,
    type TransformedItem,
} from './types';

export const SettingsContent = ({
    groupConfiguration,
}: Module.Settings.Panel.Props<ModuleAttrs>): ReactElement => {
    const [hideOptions, setHideOptions] = useState<TransformedItem[]>([]);

    const transformItem = (item: Item): TransformedItem => ({
        label: item.title,
        value: String(item.id),
    });

    useEffect(() => {
        loggedFetch({
            data: { option: 'hide' },
            method: 'GET',
            restRoute: '/site-reviews/v1/shortcode/site_reviews',
        }).then((values: Item[]) => {
            setHideOptions(values.map(transformItem));
        }).catch((error: Error) => {
            if ('AbortError' !== error.name) {
                console.error('Failed to fetch hide options:', error);
            }
        });
    }, []);

    if (groupConfiguration?.contentHide?.component) {
        set(groupConfiguration, ['contentHide', 'component', 'props', 'fields', 'shortcodeAdvancedHide', 'component', 'props', 'options'], hideOptions);
    }

    return (
        <ModuleGroups
            groups={groupConfiguration}
        />
    )
};
