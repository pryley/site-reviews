import {
    type ModuleLibrary,
} from '@divi/types';

import { type ModuleAttrs } from './types';
import { _x } from '@wordpress/i18n';

import { ModuleEdit } from './edit';
import { placeholderContent } from './placeholder-content';
import { SettingsContent } from './settings-content';

import metadata from './module.json';
import defaultAttrs from './module-default-render-attributes.json';
import defaultPrintedStyleAttrs from './module-default-printed-style-attributes.json';

import './module.scss';
import './style.scss';

export const SiteReviews: ModuleLibrary.Module.RegisterDefinition<ModuleAttrs> = {
    defaultAttrs,
    defaultPrintedStyleAttrs,
    // @ts-expect-error
    metadata,
    placeholderContent,
    renderers: {
        edit: ModuleEdit,
    },
    settings: {
        content: SettingsContent,
    },
};
