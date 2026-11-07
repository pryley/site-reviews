/** global: GLSR */

/**
 * The admin script: the pieces every plugin screen uses, then the modules of the screen the body
 * class names.
 */
import { adopt, debounce, dom, fadeIn, fadeOut, feature, historyState, isEmpty, lib, parseJson, selectText, throttle } from '@/public/core/index.js';
import Ajax from '@/admin/ajax.js';
import Cards from '@/admin/shared/cards.js';
import Code from '@/admin/shared/code.js';
import Flyout from '@/admin/shared/flyout.js';
import FooterNotice from '@/admin/shared/footer-notice.js';
import legacy from '@/admin/legacy.js';
import Notice, { Notices } from '@/admin/shared/notice.js';
import Request from '@/public/modules/request/request.js';
import ScreenMeta from '@/admin/shared/screen-meta.js';
import { share } from '@/admin/shared.js';
import StarRating from '@/public/modules/form/starrating.js';
import Tabs from '@/admin/shared/tabs.js';
import Tooltip from '@/admin/shared/tooltip.js';
import UploadForm from '@/admin/shared/upload-form.js';
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

const init = () => {
    SHARED.forEach(module => module.init(document))
    GLSR.Rating.init('.glsr-field-rating select', { clearable: true })
    legacy()
};

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', init)
} else {
    init()
}
