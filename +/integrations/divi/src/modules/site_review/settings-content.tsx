import React, { type ReactElement, useEffect, useState } from 'react';
import { set } from 'lodash';

import { __ } from '@wordpress/i18n';

import { select } from '@divi/data';
import {
    ModuleGroups,
} from '@divi/module';
import { loggedFetch } from '@divi/rest';
import {
    type BlogAttrs,
    type FieldLibrary,
    type Module,
} from '@divi/types';

import {
    isVisibleFields,
} from './callbacks';


const defaultCategories = [
    {
        label: __('All Categories', 'et_builder'),
        value: 'all',
    },
    {
        label: __('Current Category', 'et_builder'),
        value: 'current',
    },
];

/**
 * Content panel component for the Blog module settings modal.
 *
 * @param {Module.Settings.Panel.Props} param0 Content panel props.
 *
 * @returns {ReactElement}
 */
export const SettingsContent = ({
    groupConfiguration,
}: Module.Settings.Panel.Props<BlogAttrs>): ReactElement => {
    const [categories, setCategories] = useState(defaultCategories);
    const [postTypes, setPostTypes]   = useState<FieldLibrary.Select.Options>({});

    const postCategories = select('divi/settings').getSetting(['taxonomy', 'postCategories']);

    useEffect(() => {
        if (Array.isArray(postCategories)) {
            const allCategories: typeof defaultCategories = [];
            postCategories.forEach(item => {
                allCategories.push({ label: item.name, value: item.term_id.toString() });
            });
            setCategories(state => [...state, ...allCategories]);
        }
        loggedFetch({
            method:    'GET',
            restRoute: '/glsr-divi/v1/module-data/blog/types',
        }).then((value: FieldLibrary.Select.Options) => {
            setPostTypes(value);
        }).catch(error => {
            // TODO feat(D5, Logger) - We need to introduce a new logging system to log errors/rejections/etc.
            // eslint-disable-next-line no-console
            console.log(error);
        });
    }, []);

    // Insert props value to `content` group.
    if (groupConfiguration?.content?.component) {
        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedUsecurrentloop', 'visible'], isVisibleFields);
        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedExcerptmanual', 'visible'], isVisibleFields);
        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedExcerptlength', 'visible'], isVisibleFields);

        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedType', 'visible'], isVisibleFields);
        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedType', 'component', 'props', 'options'], postTypes);

        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedCategories', 'visible'], isVisibleFields);
        set(groupConfiguration, ['content', 'component', 'props', 'fields', 'postAdvancedCategories', 'component', 'props', 'options'], categories);
    }

    // Insert props value to `contentElements` group.
    if (groupConfiguration?.contentElements?.component) {
        set(groupConfiguration, ['contentElements', 'component', 'props', 'fields', 'postAdvancedShowexcerpt', 'visible'], isVisibleFields);
        set(groupConfiguration, ['contentElements', 'component', 'props', 'fields', 'readMoreAdvancedEnable', 'visible'], isVisibleFields);
    }

    // Insert props value to `contentBackground` group.
    if (groupConfiguration?.contentBackground?.component) {
        set(groupConfiguration, ['contentBackground', 'component', 'props', 'fields', 'masonryDecorationBackground', 'visible'], isVisibleFields);
    }

    return (
        <ModuleGroups
            groups={groupConfiguration}
        />
    );
};
