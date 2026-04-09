import { createConfig } from '@site-reviews/build';
import { fileURLToPath } from 'url';
import path from 'path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const { css, commonJs, namespacedCss, js } = createConfig(__dirname);

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
    js('scripts/mce-plugin'),
    js('scripts/rollback'),
    js('scripts/site-reviews'),
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
    // Block styles
    namespacedCss('styles/bootstrap-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/contact_form_7-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/default-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/minimal-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/ninja_forms-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentyfifteen-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentysixteen-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentyseventeen-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentynineteen-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentytwenty-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentytwentyone-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/twentytwentytwo-blocks', '.wp-block', 'assets/styles/blocks'),
    namespacedCss('styles/wpforms-blocks', '.wp-block', 'assets/styles/blocks'),
];

// ------------------------------------------------------------------
//  Export
// ------------------------------------------------------------------

export default [
    ...jsBundles,
    ...cssBundles,
];
