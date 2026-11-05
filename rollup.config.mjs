import { createConfig } from '@site-reviews/build';
import { fileURLToPath } from 'url';
import path from 'path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const { css, commonJs, module, namespacedCss, js } = createConfig(__dirname);

// ------------------------------------------------------------------
//  Script modules (the import map, @site-reviews/{id})
// ------------------------------------------------------------------

// What a module imports by id instead of bundling: core's plumbing and the request module.
const shared = {
    '@/public/core/index.js': '@site-reviews/core',
    '@/public/modules/request/request.js': '@site-reviews/request',
};

const moduleBundles = [
    module('scripts/public/core/index', 'core'),
    module('scripts/public/modules/captcha/index', 'captcha', 'assets/scripts/modules', { shared }),
    module('scripts/public/modules/form/index', 'form', 'assets/scripts/modules', { shared }),
    module('scripts/public/modules/modal/index', 'modal', 'assets/scripts/modules', { shared }),
    module('scripts/public/modules/request/index', 'request', 'assets/scripts/modules', { shared: { '@/public/core/index.js': '@site-reviews/core' } }),
    module('scripts/public/modules/reviews/index', 'reviews', 'assets/scripts/modules', { shared }),
];

// ------------------------------------------------------------------
//  JavaScript builds (IIFE)
// ------------------------------------------------------------------

const jsBundles = [
    commonJs('scripts/site-reviews-admin'),
    js('integrations/elementor/elementor-editor', 'assets/scripts/integrations'),
    js('integrations/elementor/elementor-frontend', 'assets/scripts/integrations'),
    js('integrations/flatsome/flatsome-inline', 'assets/scripts/integrations'),
    js('integrations/fusion/fusion-inline', 'assets/scripts/integrations'),
    js('integrations/gamipress/gamipress', 'assets/scripts/integrations'),
    js('integrations/wpbakery/wpbakery-editor', 'assets/scripts/integrations'),
    js('integrations/wpbakery/wpbakery-inline', 'assets/scripts/integrations'),
    js('scripts/deactivate-plugin'),
    js('scripts/inline-script'),
    js('scripts/mce-plugin'),
    js('scripts/rollback'),
    js('scripts/site-reviews'),
    js('scripts/site-reviews-admin-compat'),
    js('scripts/site-reviews-admin-debug'),
    js('scripts/site-reviews-compat'),
    js('scripts/site-reviews-debug'),
];

// ------------------------------------------------------------------
//  CSS builds (with PostCSS namespacing)
// ------------------------------------------------------------------

const cssBundles = [
    css('styles/admin', 'assets/styles/admin'),
    css('styles/deactivate-plugin'),
    css('styles/inline-styles'),
    // Integrations
    css('integrations/bricks/bricks-inline', 'assets/styles/integrations'),
    css('integrations/divi/divi-woo-inline', 'assets/styles/integrations'),
    css('integrations/flatsome/flatsome-inline', 'assets/styles/integrations'),
    css('integrations/fusion/fusion-inline', 'assets/styles/integrations'),
    css('integrations/wpbakery/wpbakery-inline', 'assets/styles/integrations'),
    // Plugin styles
    namespacedCss('styles/bootstrap', '.glsr-bootstrap'),
    namespacedCss('styles/breakdance', '.glsr-breakdance'),
    namespacedCss('styles/contact_form_7', '.glsr-contact_form_7'),
    namespacedCss('styles/default', '.glsr-default'),
    namespacedCss('styles/divi', '#page-container .glsr-divi, .glsr-divi'),
    namespacedCss('styles/elementor', '.glsr-elementor'),
    namespacedCss('styles/minimal', '.glsr-minimal'),
    namespacedCss('styles/ninja_forms', '.glsr-ninja_forms'),
    namespacedCss('styles/twentyfifteen', '.glsr-twentyfifteen'),
    namespacedCss('styles/twentysixteen', '.glsr-twentysixteen'),
    namespacedCss('styles/twentyseventeen', '.glsr-twentyseventeen'),
    namespacedCss('styles/twentynineteen', '.glsr-twentynineteen'),
    namespacedCss('styles/twentytwenty', '.glsr-twentytwenty'),
    namespacedCss('styles/twentytwentyone', '.glsr-twentytwentyone'),
    namespacedCss('styles/twentytwentytwo', '.glsr-twentytwentytwo'),
    namespacedCss('styles/wpforms', '.glsr-wpforms'),
];

// ------------------------------------------------------------------
//  Export
// ------------------------------------------------------------------

export default [
    ...moduleBundles,
    ...jsBundles,
    ...cssBundles,
];
