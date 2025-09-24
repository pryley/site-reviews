import { addFilter } from '@wordpress/hooks';

import {
  type BlogAttrs,
  type ModuleLibrary,
} from '@divi/types';

import metadata from './module.json';
import defaultAttrs from './module-default-printed-style-attributes.json';
import defaultPrintedStyleAttrs from './module-default-render-attributes.json';

import { ModuleEdit } from './edit';
import { ModuleStyles } from './module-styles';

import { placeholderContent } from './placeholder-content';
import { SettingsContent } from './settings-content';
import { SettingsDesign } from './settings-design';

import {
  optionGroupPresetPrimaryAttrNameResolverBlog,
  optionGroupPresetResolverAttrNameBlog,
} from './option-group-preset-resolver';
// Register the filter to resolve the option group presets data.
addFilter('divi.optionGroupPresetPrimaryAttrNameResolver.diviBlog', 'divi', optionGroupPresetPrimaryAttrNameResolverBlog);
addFilter('divi.optionGroupPresetResolverAttrName', 'divi', optionGroupPresetResolverAttrNameBlog);

export const SiteReview: ModuleLibrary.Module.RegisterDefinition<BlogAttrs> = {
    defaultAttrs,
    defaultPrintedStyleAttrs,
    // @ts-expect-error
    metadata,
    placeholderContent,
    renderers: {
        edit: ModuleEdit,
        styles: ModuleStyles,
    },
    settings: {
        content: SettingsContent,
        design:  SettingsDesign,
    },
};
