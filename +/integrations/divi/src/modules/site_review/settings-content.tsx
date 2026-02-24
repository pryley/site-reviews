import React, {
    type ReactElement,
    useEffect,
} from 'react';
import { cloneDeep, set, unset } from 'lodash';
import { ModuleGroups } from '@divi/module';
import { type Module } from '@divi/types';
import { type ModuleAttrs } from './types';
import { useCheckboxesField } from '@site-reviews-divi/hooks/useCheckboxesField';
import { useFormTokenField } from '@site-reviews-divi/hooks/useFormTokenField';

const shortcode = 'site_review';

export const SettingsContent = (
    props: Module.Settings.Panel.Props<ModuleAttrs>
): ReactElement => {
    const { attrs, groupConfiguration } = props;
    const groups = cloneDeep(groupConfiguration);
    // @ts-expect-error
    const postId = [attrs?.shortcode?.advanced?.post_id?.desktop?.value ?? ""].filter(str => str !== "");
    const postIdField = useFormTokenField(shortcode, 'post_id', postId);
    const hideField = useCheckboxesField(shortcode, 'hide');

    if (groups?.contentGeneral?.component) {
        set(groups, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedPost_id', 'component', 'props', 'options'], postIdField.options);
        set(groups, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedPost_id', 'component', 'props', 'updateSearchQuery'], postIdField.onSearchChange);
    }
    if (groups?.contentHide?.component) {
        set(groups, ['contentHide', 'component', 'props', 'fields', 'shortcodeAdvancedHide', 'component', 'props', 'options'], hideField.options);
    }

    for (let key in groups) {
        if (!key.startsWith('content')) {
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
