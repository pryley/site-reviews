import alias from '@rollup/plugin-alias';
import babel from '@rollup/plugin-babel';
import commonjs from '@rollup/plugin-commonjs';
import postcss from 'rollup-plugin-postcss';
import postcssImport from 'postcss-import';
import postcssPresetEnv from 'postcss-preset-env';
import postcssSelectorNamespace from 'postcss-selector-namespace';
import replace from '@rollup/plugin-replace';
import resolve from '@rollup/plugin-node-resolve';
import terser from '@rollup/plugin-terser';
import path from 'path';
import { fileURLToPath } from 'url';
import { gzipSync } from 'zlib';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const isProduction = process.env.NODE_ENV === 'production';

// ------------------------------------------------------------------
//  Build summary
// ------------------------------------------------------------------

const buildResults = [];
let buildCount = 0;
let totalConfigs = 0;

// ANSI color helpers
const c = {
    bold: (s) => `\x1b[1m${s}\x1b[0m`,
    cyan: (s) => `\x1b[36m${s}\x1b[0m`,
    dim: (s) => `\x1b[2m${s}\x1b[0m`,
    green: (s) => `\x1b[32m${s}\x1b[0m`,
    yellow: (s) => `\x1b[33m${s}\x1b[0m`,
};

const formatSize = (bytes) => {
    if (bytes < 1024) return `${bytes} B`;
    return `${(bytes / 1024).toFixed(2)} KB`;
};

const colorSize = (bytes) => {
    const str = formatSize(bytes);
    if (bytes > 100 * 1024) return c.yellow(str);
    if (bytes > 50 * 1024) return c.cyan(str);
    return c.green(str);
};

const summaryPlugin = () => ({
    name: 'build-summary',
    generateBundle(options, bundle) {
        for (const [fileName, chunk] of Object.entries(bundle)) {
            const source = chunk.type === 'asset' ? chunk.source : chunk.code;
            const size = typeof source === 'string' ? Buffer.byteLength(source) : source.length;
            const gzipped = gzipSync(source).length;
            const outputDir = options.dir || path.dirname(options.file || '');
            buildResults.push({
                file: path.join(outputDir, fileName),
                gzipped,
                size,
            });
        }
    },
    closeBundle() {
        buildCount++;
        if (buildCount < totalConfigs) return;
        const js = buildResults.filter(r => r.file.endsWith('.js')).sort((a, b) => b.size - a.size);
        const css = buildResults.filter(r => r.file.endsWith('.css')).sort((a, b) => b.size - a.size);
        const all = [...js, ...css];
        const maxFile = Math.max(...all.map(r => r.file.length), 4);
        const maxSize = Math.max(...all.map(r => formatSize(r.size).length), 4);
        const maxGzip = Math.max(...all.map(r => formatSize(r.gzipped).length), 4);
        const divider = c.dim('─'.repeat(maxFile + maxSize + maxGzip + 6));
        const printTable = (label, rows) => {
            if (!rows.length) return;
            const sizeHeader = c.dim('Size'.padStart(maxSize));
            const gzipHeader = c.dim('Gzip'.padStart(maxGzip));
            console.log(`\n  ${c.bold(label.padEnd(maxFile))}  ${sizeHeader}  ${gzipHeader}`);
            console.log(`  ${divider}`);
            for (const r of rows) {
                const file = c.dim(r.file.padEnd(maxFile));
                const size = colorSize(r.size).padStart(maxSize + 10);
                const gzip = c.dim(formatSize(r.gzipped)).padStart(maxGzip + 8);
                console.log(`  ${file}  ${size}  ${gzip}`);
            }
        };
        printTable('JavaScript', js);
        printTable('CSS', css);
        console.log('');
    },
});

// ------------------------------------------------------------------
//  Console methods to strip in production (keep info, warn, error)
// ------------------------------------------------------------------

const consolePureFuncs = Object.keys(console)
    .filter((key) => !['info', 'warn', 'error'].includes(key))
    .map((key) => `console.${key}`);

// ------------------------------------------------------------------
//  PostCSS plugins factory
// ------------------------------------------------------------------

const postcssPlugins = (namespace = '') => [
    postcssImport(),
    postcssPresetEnv({
        features: { 'custom-properties': false },
        stage: 1,
    }),
    ...(namespace
        ? [postcssSelectorNamespace({ namespace })]
        : []
    ),
];

// ------------------------------------------------------------------
//  Shared Rollup plugins
// ------------------------------------------------------------------

const jsPlugins = ({ cjs = false } = {}) => [
    alias({
        entries: [
            { find: '@', replacement: path.resolve(__dirname, '+/scripts') },
        ],
    }),
    replace({
        preventAssignment: true,
        'process.env.NODE_ENV': JSON.stringify(isProduction ? 'production' : 'development'),
    }),
    resolve(),
    ...(cjs ? [commonjs()] : []),
    babel({
        babelHelpers: 'runtime',
        plugins: [
            '@babel/plugin-transform-optional-chaining',
            '@babel/plugin-transform-runtime',
        ],
        presets: [
            '@babel/preset-env',
        ],
    }),
    ...(isProduction
        ? [terser({
            compress: {
                pure_funcs: consolePureFuncs,
            },
            format: {
                comments: false,
            },
        })]
        : []
    ),
    summaryPlugin(),
];

// Remove the stub JS files generated by rollup-plugin-postcss
// when processing CSS-only entries with extract: true.
const cleanCssJsPlugin = () => ({
    name: 'clean-css-js',
    generateBundle(_, bundle) {
        for (const fileName of Object.keys(bundle)) {
            if (fileName.endsWith('.js') || fileName.endsWith('.js.map')) {
                delete bundle[fileName];
            }
        }
    },
});

const cssPlugins = (namespace) => [
    postcss({
        extract: true,
        minimize: isProduction
            ? {
                minifyFontValues: false,
                discardComments: { removeAll: true },
                zindex: false,
            }
            : false,
        plugins: postcssPlugins(namespace),
        sourceMap: !isProduction,
    }),
    cleanCssJsPlugin(),
    summaryPlugin(),
];

// ------------------------------------------------------------------
//  Config builders
// ------------------------------------------------------------------

const js = (source, outputDir = 'assets/scripts') => ({
    input: `+/${source}.js`,
    output: {
        dir: outputDir,
        format: 'iife',
        sourcemap: !isProduction,
    },
    plugins: jsPlugins(),
});

const jsAdmin = (source, outputDir = 'assets/scripts') => ({
    input: `+/${source}.js`,
    output: {
        dir: outputDir,
        format: 'iife',
        sourcemap: !isProduction,
    },
    plugins: jsPlugins({ cjs: true }),
});

const css = (source, namespace = '', outputDir = 'assets/styles') => ({
    input: `+/styles/${source}.css`,
    output: {
        dir: outputDir,
        format: 'es',
    },
    onwarn(warning, warn) {
        if (warning.code === 'FILE_NAME_CONFLICT') return;
        warn(warning);
    },
    plugins: cssPlugins(namespace),
});

const integrationCss = (source, outputDir = 'assets/styles/integrations') => ({
    input: `+/integrations/${source}.css`,
    output: {
        dir: outputDir,
        format: 'es',
    },
    onwarn(warning, warn) {
        if (warning.code === 'FILE_NAME_CONFLICT') return;
        warn(warning);
    },
    plugins: cssPlugins(''),
});

// ------------------------------------------------------------------
//  JavaScript builds (IIFE)
// ------------------------------------------------------------------

const jsBundles = [
    // Frontend
    js('scripts/site-reviews'),
    js('scripts/site-reviews-extra'),
    js('integrations/elementor/elementor-editor', 'assets/scripts/integrations'),
    js('integrations/elementor/elementor-frontend', 'assets/scripts/integrations'),
    js('integrations/fusion/fusion-inline', 'assets/scripts/integrations'),
    // Admin
    jsAdmin('scripts/deactivate-plugin'),
    jsAdmin('scripts/mce-plugin'),
    jsAdmin('scripts/rollback'),
    jsAdmin('scripts/site-reviews-admin'),
    jsAdmin('integrations/flatsome/flatsome-inline', 'assets/scripts/integrations'),
    jsAdmin('integrations/gamipress/gamipress', 'assets/scripts/integrations'),
    jsAdmin('integrations/wpbakery/wpbakery-editor', 'assets/scripts/integrations'),
    jsAdmin('integrations/wpbakery/wpbakery-inline', 'assets/scripts/integrations'),
];

// ------------------------------------------------------------------
//  Integration CSS builds (no namespace)
// ------------------------------------------------------------------

const integrationCssBundles = [
    integrationCss('bricks/bricks-inline'),
    integrationCss('flatsome/flatsome-inline'),
    integrationCss('fusion/fusion-inline'),
    integrationCss('wpbakery/wpbakery-inline'),
];

// ------------------------------------------------------------------
//  CSS builds (with PostCSS namespacing)
// ------------------------------------------------------------------

const cssBundles = [
    // Admin (no namespace)
    css('admin', '', 'assets/styles/admin'),
    css('deactivate-plugin'),
    // Global (no namespace)
    css('inline-styles'),
    // Theme styles (namespaced)
    css('bootstrap', '.glsr-bootstrap'),
    css('breakdance', '.glsr-breakdance'),
    css('contact_form_7', '.glsr-contact_form_7'),
    css('default', '.glsr-default'),
    css('divi', '#page-container .glsr-divi, .glsr-divi'),
    css('elementor', '.glsr-elementor'),
    css('minimal', '.glsr-minimal'),
    css('ninja_forms', '.glsr-ninja_forms'),
    css('twentyfifteen', '.glsr-twentyfifteen'),
    css('twentysixteen', '.glsr-twentysixteen'),
    css('twentyseventeen', '.glsr-twentyseventeen'),
    css('twentynineteen', '.glsr-twentynineteen'),
    css('twentytwenty', '.glsr-twentytwenty'),
    css('twentytwentyone', '.glsr-twentytwentyone'),
    css('twentytwentytwo', '.glsr-twentytwentytwo'),
    css('wpforms', '.glsr-wpforms'),
    // Block styles (namespaced to .wp-block)
    css('bootstrap-blocks', '.wp-block', 'assets/styles/blocks'),
    css('contact_form_7-blocks', '.wp-block', 'assets/styles/blocks'),
    css('default-blocks', '.wp-block', 'assets/styles/blocks'),
    css('minimal-blocks', '.wp-block', 'assets/styles/blocks'),
    css('ninja_forms-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentyfifteen-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentysixteen-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentyseventeen-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentynineteen-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentytwenty-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentytwentyone-blocks', '.wp-block', 'assets/styles/blocks'),
    css('twentytwentytwo-blocks', '.wp-block', 'assets/styles/blocks'),
    css('wpforms-blocks', '.wp-block', 'assets/styles/blocks'),
];

// ------------------------------------------------------------------
//  Export
// ------------------------------------------------------------------

const configs = [
    ...jsBundles,
    ...integrationCssBundles,
    ...cssBundles,
];

totalConfigs = configs.length;

export default configs;
