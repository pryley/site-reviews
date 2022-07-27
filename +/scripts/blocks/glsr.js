import AssignedPostsOptions from '@/blocks/assigned_posts';
import AssignedTermsOptions from '@/blocks/assigned_terms';
import AssignedUsersOptions from '@/blocks/assigned_users';
import ConditionalSelectControl from '@/blocks/ConditionalSelectControl';
import Event from '@/public/event.js';
import onRender from '@/blocks/on-render';
import ServerSideRender from '@/blocks/server-side-render';
import TermOptions from '@/blocks/term-options';
import TypeOptions from '@/blocks/type-options';
import transformWidgetAttributes from '@/blocks/transform-widget';
import { CheckboxControlList } from '@/blocks/checkbox-control-list';

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = { Event };
}

GLSR.blocks = {
    AssignedPostsOptions,
    AssignedTermsOptions,
    AssignedUsersOptions,
    CheckboxControlList,
    ConditionalSelectControl,
    ServerSideRender,
    onRender,
    TermOptions,
    TypeOptions,
    transformWidgetAttributes,
};
