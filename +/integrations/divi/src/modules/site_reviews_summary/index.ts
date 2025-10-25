import defaultAttrs from './module-default-render-attributes.json';
import defaultPrintedStyleAttrs from './module-default-printed-style-attributes.json';
import metadata from './module.json';
import { moduleClassnames } from './module-classnames';
import { ModuleEdit as SharedModuleEdit } from '../../shared/edit';
import { ModuleEditProps } from '@divi/module-library';
import { ModuleScriptData } from './module-script-data';
import { ModuleStyles } from './module-styles';
import { placeholderContent } from './placeholder-content';
import { SettingsContent } from './settings-content';
import { type ModuleAttrs } from './types';
import { type ModuleLibrary } from '@divi/types';
import '../../../../../blocks/site_reviews_summary/style.scss';
import './module.scss';
import './vb.scss';

const ModuleEdit = (props: ModuleEditProps<ModuleAttrs>) => SharedModuleEdit({
    ...props,
    moduleClassnames,
    ModuleScriptData,
    ModuleStyles,
});

export const SiteReviewsSummary: ModuleLibrary.Module.RegisterDefinition<ModuleAttrs> = {
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
