import { createConfig } from '@site-reviews/build';
import { fileURLToPath } from 'url';
import fs from 'fs';
import path from 'path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const { componentCss, css, commonJs, module, js } = createConfig(__dirname);

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
];

// ------------------------------------------------------------------
//  Plugin styles: the base per component, and each style's own components
// ------------------------------------------------------------------

// A style's selectors; the base's (`:--base`) compile under `.glsr`, Divi's under its own.
const NAMESPACES = {
    bootstrap: '.glsr-bootstrap',
    breakdance: '.glsr-breakdance',
    contact_form_7: '.glsr-contact_form_7',
    default: '.glsr-default',
    divi: '#page-container .glsr-divi, .glsr-divi',
    elementor: '.glsr-elementor',
    minimal: '.glsr-minimal',
    ninja_forms: '.glsr-ninja_forms',
    twentyfifteen: '.glsr-twentyfifteen',
    twentynineteen: '.glsr-twentynineteen',
    twentyseventeen: '.glsr-twentyseventeen',
    twentysixteen: '.glsr-twentysixteen',
    twentytwenty: '.glsr-twentytwenty',
    twentytwentyone: '.glsr-twentytwentyone',
    twentytwentytwo: '.glsr-twentytwentytwo',
    wpforms: '.glsr-wpforms',
};

const BASE_NAMESPACES = {
    divi: NAMESPACES.divi, // Divi's theme CSS selects with #page-container, so the base rules need it too
};

const baseNamespace = (style) => BASE_NAMESPACES[style] || '.glsr';

// `+/styles/{style}.css` is the style's component list: `public/base/{c}` for a component it
// leaves as the base is, `public/{style}/{c}` for one it changes.
const styleComponents = (style) => [...fs.readFileSync(path.join(__dirname, `+/styles/${style}.css`), 'utf8').matchAll(/@import\s+"public\/([\w-]+)\/([\w-]+)"/g)]
    .map(([, dir, component]) => ({ component, dir }));

const baseComponents = styleComponents('minimal').map(({ component }) => component); // the base in the order a page needs it

const styleBundles = [
    ...baseComponents.map((component) => componentCss(`styles/public/base/${component}`, 'assets/styles/base', { base: '.glsr', namespace: '.glsr' })),
    ...Object.entries(NAMESPACES).flatMap(([style, namespace]) => styleComponents(style)
        .filter(({ dir }) => dir === style)
        .map(({ component }) => componentCss(`styles/public/${style}/${component}`, `assets/styles/${style}`, { base: baseNamespace(style), namespace }))),
];

// What the renderer reads: per style and component, the file to print, in the order the page needs them.
const manifest = Object.fromEntries(Object.keys(NAMESPACES).map((style) => [
    style,
    Object.fromEntries(styleComponents(style).map(({ component, dir }) => [component, `${dir}/${component}.css`])),
]));
fs.writeFileSync(path.join(__dirname, 'assets/styles/manifest.php'), [
    '<?php',
    '',
    '// Written by rollup.config.mjs from the import lists in +/styles/{style}.css; do not edit.',
    'return ' + JSON.stringify({ components: baseComponents, styles: manifest }, null, 4)
        .replace(/\{/g, '[').replace(/\}/g, ']').replace(/": /g, '" => ').replace(/"/g, "'") + ';',
    '',
].join('\n'));

// ------------------------------------------------------------------
//  Export
// ------------------------------------------------------------------

export default [
    ...moduleBundles,
    ...jsBundles,
    ...cssBundles,
    ...styleBundles,
];
