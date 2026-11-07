/** global: GLSR */

/**
 * The admin script: the pieces every plugin screen uses, then the modules of the screen the body
 * class names.
 */
import { adopt, debounce, dom, fadeIn, fadeOut, feature, historyState, isEmpty, lib, parseJson, selectText, throttle } from '@/public/core/index.js';
import Ajax from '@/admin/ajax.js';
import Assigned from '@/admin/review/assigned.js';
import CategoryQuickEdit from '@/admin/category/quick-edit.js';
import Cards from '@/admin/shared/cards.js';
import Code from '@/admin/shared/code.js';
import Copy from '@/admin/documentation/copy.js';
import ColorPicker from '@/admin/settings/color-picker.js';
import Console from '@/admin/tools/console.js';
import Depends from '@/admin/settings/depends.js';
import Filter from '@/admin/reviews/filter.js';
import Filters from '@/admin/reviews/filters.js';
import Flyout from '@/admin/shared/flyout.js';
import FooterNotice from '@/admin/shared/footer-notice.js';
import Import from '@/admin/tools/import.js';
import IpDetection from '@/admin/tools/ip-detection.js';
import Metabox from '@/admin/review/metabox.js';
import Notice, { Notices } from '@/admin/shared/notice.js';
import PremiumLicense from '@/admin/settings/premium-license.js';
import PublishAction from '@/admin/review/publish-action.js';
import QuickEdit from '@/admin/reviews/quick-edit.js';
import Request from '@/public/modules/request/request.js';
import ResetButton from '@/admin/settings/reset-button.js';
import Rollback from '@/admin/tools/rollback.js';
import ScreenMeta from '@/admin/shared/screen-meta.js';
import { share } from '@/admin/shared.js';
import StarRating from '@/public/modules/form/starrating.js';
import Status from '@/admin/reviews/status.js';
import Strings from '@/admin/settings/strings.js';
import SystemInfo from '@/admin/tools/system-info.js';
import Tabs from '@/admin/shared/tabs.js';
import TemplateEditor from '@/admin/settings/template-editor.js';
import ToolButton from '@/admin/tools/tool-button.js';
import ToggleFlag from '@/admin/reviews/toggle-flag.js';
import Tooltip from '@/admin/shared/tooltip.js';
import TrackSelect from '@/admin/settings/track-select.js';
import UploadForm from '@/admin/shared/upload-form.js';
import Verification from '@/admin/review/verification.js';
import Videos from '@/admin/documentation/videos.js';
import tippy, { followCursor } from 'tippy.js';

GLSR.Event = adopt(GLSR.Event);
GLSR.lib = lib;
GLSR.Notice = Notice;
GLSR.Rating = StarRating();
GLSR.Request = Request;
GLSR.Util = { debounce, dom, fadeIn, fadeOut, feature, historyState, isEmpty, parseJson, selectText, throttle };
lib.register('tippy', { tippy, plugins: { followCursor } })
share({ Ajax, notices: () => Notice })

const SHARED = [Cards, Code, Flyout, FooterNotice, Notices, ScreenMeta, Tabs, ToolButton, Tooltip, UploadForm]; // ToolButton: the migration notice has one on every screen

// the modules of each screen determined by the classes of its body
const SCREENS = [
    { classes: ['dashboard_page_site-reviews-welcome'], modules: [Copy, Videos] },
    { classes: ['edit-php', 'post-type-site-review'], modules: [Assigned, Filter, Filters, QuickEdit, Status, ToggleFlag] },
    { classes: ['edit-tags-php', 'taxonomy-site-review-category'], modules: [CategoryQuickEdit] },
    { classes: ['post-new-php', 'post-type-site-review'], modules: [Assigned, Filter, Metabox, PublishAction, Verification] },
    { classes: ['post-php', 'post-type-site-review'], modules: [Assigned, Filter, Metabox, PublishAction, Verification] },
    { classes: ['site-review_page_glsr-documentation'], modules: [Copy, Videos] },
    { classes: ['site-review_page_glsr-settings'], modules: [ColorPicker, Depends, PremiumLicense, ResetButton, Strings, TemplateEditor, TrackSelect] },
    { classes: ['site-review_page_glsr-tools'], modules: [Console, Import, IpDetection, Rollback, SystemInfo] },
];

const init = () => {
    const { classList } = document.body;
    SHARED.forEach(module => module.init(document))
    SCREENS.filter(({ classes }) => classes.every(name => classList.contains(name)))
        .forEach(({ modules }) => modules.forEach(module => module.init(document)))
    GLSR.Rating.init('.glsr-field-rating select', { clearable: true })
};

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', init, { once: true })
} else {
    init()
}
