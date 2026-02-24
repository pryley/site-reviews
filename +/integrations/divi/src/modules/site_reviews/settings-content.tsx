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

const shortcode = 'site_reviews';

export const SettingsContent = (
    props: Module.Settings.Panel.Props<ModuleAttrs>
): ReactElement => {
    const { attrs, groupConfiguration } = props;
    const groups = cloneDeep(groupConfiguration);
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
    const paginationField = useFormTokenField(shortcode, 'pagination', []);
    const termsField = useFormTokenField(shortcode, 'terms', []);
    const typeField = useFormTokenField(shortcode, 'type', []);
    const verifiedField = useFormTokenField(shortcode, 'verified', []);

    if (groups?.contentGeneral?.component) {
        const base = ['contentGeneral', 'component', 'props', 'fields'];
        set(groups, [...base, 'shortcodeAdvancedAssigned_posts', 'component', 'props', 'onDropdownClose'], apField.onDropdownClose);
        set(groups, [...base, 'shortcodeAdvancedAssigned_posts', 'component', 'props', 'onSearchChange'], apField.onSearchChange);
        set(groups, [...base, 'shortcodeAdvancedAssigned_posts', 'component', 'props', 'options'], apField.options);
        set(groups, [...base, 'shortcodeAdvancedAssigned_terms', 'component', 'props', 'onDropdownClose'], atField.onDropdownClose);
        set(groups, [...base, 'shortcodeAdvancedAssigned_terms', 'component', 'props', 'onSearchChange'], atField.onSearchChange);
        set(groups, [...base, 'shortcodeAdvancedAssigned_terms', 'component', 'props', 'options'], atField.options);
        set(groups, [...base, 'shortcodeAdvancedAssigned_users', 'component', 'props', 'onDropdownClose'], auField.onDropdownClose);
        set(groups, [...base, 'shortcodeAdvancedAssigned_users', 'component', 'props', 'onSearchChange'], auField.onSearchChange);
        set(groups, [...base, 'shortcodeAdvancedAssigned_users', 'component', 'props', 'options'], auField.options);
        set(groups, [...base, 'shortcodeAdvancedAuthor', 'component', 'props', 'options'], authorField.options);
        set(groups, [...base, 'shortcodeAdvancedAuthor', 'component', 'props', 'updateSearchQuery'], authorField.onSearchChange);
        set(groups, [...base, 'shortcodeAdvancedTerms', 'component', 'props', 'options'], termsField.options);
        set(groups, [...base, 'shortcodeAdvancedVerified', 'component', 'props', 'options'], verifiedField.options);
        if (Object.keys(typeField.options).length) {
            set(groups, [...base, 'shortcodeAdvancedType', 'component', 'props', 'options'], typeField.options);
        } else {
            unset(groups, [...base, 'shortcodeAdvancedType']);
        }
    }
    if (groups?.contentDisplay?.component) {
        set(groups, ['contentDisplay', 'component', 'props', 'fields', 'shortcodeAdvancedPagination', 'component', 'props', 'options'], paginationField.options);
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
