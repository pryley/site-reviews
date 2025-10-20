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

const shortcode = 'site_reviews_summary';

export const SettingsContent = (
    props: Module.Settings.Panel.Props<ModuleAttrs>
): ReactElement => {
    const { attrs, groupConfiguration } = props;
    // @ts-expect-error
    const assignedPosts = Array.from(attrs?.shortcode?.advanced?.assigned_posts?.desktop?.value ?? [] as any).map(obj => obj.value);
    // @ts-expect-error
    const assignedTerms = Array.from(attrs?.shortcode?.advanced?.assigned_terms?.desktop?.value ?? [] as any).map(obj => obj.value);
    // @ts-expect-error
    const assignedUsers = Array.from(attrs?.shortcode?.advanced?.assigned_users?.desktop?.value ?? [] as any).map(obj => obj.value);
    const author = [attrs?.shortcode?.advanced?.author?.desktop?.value ?? ""].filter(str => str !== "");

    const apField = useFormTokenField(shortcode, 'assigned_posts', assignedPosts);
    const atField = useFormTokenField(shortcode, 'assigned_terms', assignedTerms);
    const auField = useFormTokenField(shortcode, 'assigned_users', assignedUsers);
    const authorField = useFormTokenField(shortcode, 'author', author);
    const hideField = useCheckboxesField(shortcode, 'hide');
    const termsField = useFormTokenField(shortcode, 'terms', []);
    const typeField = useFormTokenField(shortcode, 'type', []);

    if (groupConfiguration?.contentGeneral?.component) {
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_posts', 'component', 'props', 'onDropdownClose'], apField.onDropdownClose);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_posts', 'component', 'props', 'onSearchChange'], apField.onSearchChange);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_posts', 'component', 'props', 'options'], apField.options);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_terms', 'component', 'props', 'onDropdownClose'], atField.onDropdownClose);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_terms', 'component', 'props', 'onSearchChange'], atField.onSearchChange);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_terms', 'component', 'props', 'options'], atField.options);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_users', 'component', 'props', 'onDropdownClose'], auField.onDropdownClose);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_users', 'component', 'props', 'onSearchChange'], auField.onSearchChange);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAssigned_users', 'component', 'props', 'options'], auField.options);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAuthor', 'component', 'props', 'options'], authorField.options);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedAuthor', 'component', 'props', 'updateSearchQuery'], authorField.onSearchChange);
        set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedTerms', 'component', 'props', 'options'], termsField.options);
        if (Object.keys(typeField.options).length) {
            set(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedType', 'component', 'props', 'options'], typeField.options);
        } else {
            unset(groupConfiguration, ['contentGeneral', 'component', 'props', 'fields', 'shortcodeAdvancedType']);
        }
    }
    if (groupConfiguration?.contentHide?.component) {
        set(groupConfiguration, ['contentHide', 'component', 'props', 'fields', 'shortcodeAdvancedHide', 'component', 'props', 'options'], hideField.options);
    }

    for (let key in groupConfiguration) {
        if (!key.startsWith('content')) {
            continue;
        }
        // @ts-expect-error
        if (!groupConfiguration[key]?.component?.props?.fields) {
            unset(groupConfiguration, [key]);
        }
    }

    return (
        <ModuleGroups
            groups={groupConfiguration}
        />
    )
};
