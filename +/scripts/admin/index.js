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
import Filter from '@/admin/reviews/filter.js';
import Filters from '@/admin/reviews/filters.js';
import Flyout from '@/admin/shared/flyout.js';
import FooterNotice from '@/admin/shared/footer-notice.js';
import legacy from '@/admin/legacy.js';
import Metabox from '@/admin/review/metabox.js';
import Notice, { Notices } from '@/admin/shared/notice.js';
import PublishAction from '@/admin/review/publish-action.js';
import QuickEdit from '@/admin/reviews/quick-edit.js';
import Request from '@/public/modules/request/request.js';
import ScreenMeta from '@/admin/shared/screen-meta.js';
import { share } from '@/admin/shared.js';
import Status from '@/admin/reviews/status.js';
import StarRating from '@/public/modules/form/starrating.js';
import Tabs from '@/admin/shared/tabs.js';
import ToggleFlag from '@/admin/reviews/toggle-flag.js';
import Tooltip from '@/admin/shared/tooltip.js';
import UploadForm from '@/admin/shared/upload-form.js';
import Verification from '@/admin/review/verification.js';
import tippy, { followCursor } from 'tippy.js';

GLSR.Event = adopt(GLSR.Event);
GLSR.lib = lib;
GLSR.Notice = Notice;
GLSR.Rating = StarRating();
GLSR.Request = Request;
GLSR.Util = { debounce, dom, fadeIn, fadeOut, feature, historyState, isEmpty, parseJson, selectText, throttle };
lib.register('tippy', { tippy, plugins: { followCursor } })
share({ Ajax, notices: () => Notice })

const SHARED = [Cards, Code, Flyout, FooterNotice, Notices, ScreenMeta, Tabs, Tooltip, UploadForm];

// the modules of each screen determined by the classes of its body
const SCREENS = [
    { classes: ['edit-php', 'post-type-site-review'], modules: [Assigned, Filter, Filters, QuickEdit, Status, ToggleFlag] },
    { classes: ['edit-tags-php', 'taxonomy-site-review-category'], modules: [CategoryQuickEdit] },
    { classes: ['post-new-php', 'post-type-site-review'], modules: [Assigned, Filter, Metabox, PublishAction, Verification] },
    { classes: ['post-php', 'post-type-site-review'], modules: [Assigned, Filter, Metabox, PublishAction, Verification] },
];

const init = () => {
    const { classList } = document.body;
    SHARED.forEach(module => module.init(document))
    SCREENS.filter(({ classes }) => classes.every(name => classList.contains(name)))
        .forEach(({ modules }) => modules.forEach(module => module.init(document)))
    GLSR.Rating.init('.glsr-field-rating select', { clearable: true })
    legacy()
};

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', init)
} else {
    init()
}
