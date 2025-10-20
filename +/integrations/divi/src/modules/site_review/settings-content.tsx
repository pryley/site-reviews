import React, {
    type ReactElement,
    useEffect,
} from 'react';
import { set, unset } from 'lodash';
import { ModuleGroups } from '@divi/module';
import { type Module } from '@divi/types';
import { type ModuleAttrs } from './types';
import { useCheckboxesField } from '../../hooks/useCheckboxesField';
import { useFormTokenField } from '../../hooks/useFormTokenField';

const shortcode = 'site_review';

export const SettingsContent = (
    props: Module.Settings.Panel.Props<ModuleAttrs>
): ReactElement => {
    const { attrs, groupConfiguration } = props;
    // @ts-expect-error
    const postId = [attrs?.shortcode?.advanced?.post_id?.desktop?.value ?? ""].filter(str => str !== "");
    const postIdField = useFormTokenField(shortcode, 'post_id', postId);
    const hideField = useCheckboxesField(shortcode, 'hide');

    if (groupConfiguration?.contentGeneral?.component) {
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedPost_id', 'component', 'props', 'options'], postIdField.options);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedPost_id', 'component', 'props', 'updateSearchQuery'], postIdField.onSearchChange);
    }
    if (groupConfiguration?.contentHide?.component) {
        set(groupConfiguration, ['contentHide', 'component', 'props', 'fields', 'shortcodeAdvancedHide', 'component', 'props', 'options'], hideField.options);
    }

    return (
        <ModuleGroups
            groups={groupConfiguration}
        />
    )
};
