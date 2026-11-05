import alias from '@rollup/plugin-alias';
import babel from '@rollup/plugin-babel';
import commonjs from '@rollup/plugin-commonjs';
import postcss from 'rollup-plugin-postcss';
import postcssImport from 'postcss-import';
import postcssPresetEnv from 'postcss-preset-env';
import replace from '@rollup/plugin-replace';
import resolve from '@rollup/plugin-node-resolve';
import terser from '@rollup/plugin-terser';
import path from 'path';

const isProduction = process.env.NODE_ENV === 'production';

const cssMinify = isProduction
    ? {
        minifyFontValues: false,
        discardComments: { removeAll: true },
        zindex: false,
    }
    : false;

// Dropped by terser in production builds; info, warn and error ship.
const consolePureFuncs = Object.keys(console)
    .filter((key) => !['info', 'warn', 'error'].includes(key))
    .map((key) => `console.${key}`);

const postcssPlugins = (namespace = '', features = {}, base = '') => [
    postcssImport(),
    postcssPresetEnv({
        features: { 'custom-properties': false, ...features },
        stage: 1,
    }),
    ...(namespace || base ? [namespacePlugin({ base, namespace })] : []),
];

// A placeholder marks where a namespace is inserted: `:--base` takes the shared base's and
// `:--namespace` the style's, each expanded for a namespace with commas (Divi's). A selector that
// starts `:root` is printed as written, without it.
const MARKERS = { ':--base': 'base', ':--namespace': 'namespace' };

const namespaced = (selector, namespaces) => {
    const marker = Object.keys(MARKERS).find((marker) => selector.includes(marker));
    if (!marker) {
        return [selector.replace(/^:root\s*/, '')];
    }
    return namespaces[MARKERS[marker]].split(',').map((namespace) => selector.replaceAll(marker, namespace.trim()));
};

const namespacePlugin = (namespaces) => ({
    postcssPlugin: 'site-reviews-namespace',
    Once(root) {
        root.walkRules((rule) => {
            if (rule.parent?.type === 'rule' || rule.parent?.type === 'atrule' && /keyframes$/.test(rule.parent.name)) return;
            rule.selectors = rule.selectors.flatMap((selector) => namespaced(selector, namespaces));
        });
    },
});

// Imports a file as a plain string: markup authored as its own file but
// shipped inline (SVG icons).
const rawText = (extension) => ({
    name: 'raw-text',
    transform(code, id) {
        if (!id.endsWith(extension)) return null;
        return {
            code: 'export default ' + JSON.stringify(code.trim()) + ';',
            map: { mappings: '' },
        };
    },
});

/**
 * `import css from './foo.css'` yields the compiled, minified stylesheet as a
 * string (inject:false + extract:false), for code that adopts it into a shadow
 * root at runtime. Runs ahead of jsPlugins so the CSS is a JS module before
 * babel and terser see it.
 *
 * The logical-* features are disabled: preset-env at stage 1 downlevels
 * logical properties to physical LTR equivalents (inset-inline-start becomes
 * left), which breaks RTL. All of them are supported natively below the
 * browser floor (inset-inline-start: Chrome 87, Safari 14.1, Firefox 63).
 * Disabled only for this inlining path so existing stylesheets keep their
 * current output.
 */
const inlineCssPlugin = () => postcss({
    extract: false,
    inject: false,
    minimize: cssMinify,
    plugins: postcssPlugins('', {
        'float-clear-logical-values': false,
        'logical-overflow': false,
        'logical-overscroll-behavior': false,
        'logical-properties-and-values': false,
        'logical-resize': false,
        'logical-viewport-units': false,
    }),
    sourceMap: false,
});

const jsPlugins = (rootDir, { cjs = false, scriptsAlias = '' } = {}) => [
    rawText('.svg'),
    alias({
        entries: [
            { find: '@', replacement: scriptsAlias || path.resolve(rootDir, '+/scripts') },
        ],
    }),
    replace({
        preventAssignment: true,
        'process.env.NODE_ENV': JSON.stringify(isProduction ? 'production' : 'development'),
    }),
    ...(cjs ? [commonjs()] : []),
    resolve({
        browser: true,
        exportConditions: ['browser', 'module', 'default'],
    }),
    babel({
        babelHelpers: 'runtime',
        plugins: [
            '@babel/plugin-transform-runtime',
        ],
        presets: [
            '@babel/preset-env',
        ],
    }),
    ...(isProduction
        ? [terser({
            compress: {
                // Two extra passes let compressions expose one another;
                // measured ~0.5% gzip across bundles for pennies of build
                // time, with none of the `unsafe_*` semantics changes.
                passes: 3,
                pure_funcs: consolePureFuncs,
            },
            format: {
                // Bundled MIT vendors require their copyright notice
                // to ship with the code.
                comments: /^!|@license|@preserve/i,
            },
        })]
        : []
    ),
];

// Runs after terser (renderChunk hooks run in plugin order) so the wrapper is not minified away.
const wrapChunkPlugin = (chunkId, registry) => ({
    name: 'wrap-chunk',
    renderChunk(code) {
        return {
            code: `${registry}.define('${chunkId}',function(){${code}\n});`,
            map: null,
        };
    },
});

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

const cssPlugins = (namespace = '', base = '') => [
    postcss({
        extract: true,
        minimize: cssMinify,
        plugins: postcssPlugins(namespace, {}, base),
        sourceMap: !isProduction,
    }),
    cleanCssJsPlugin(),
];

/**
 * @param {string} rootDir
 * @param {{scriptsAlias?: string, sharedPrefix?: string}} [options]
 *   scriptsAlias: what `@` resolves to (default `${rootDir}/+/scripts`).
 *   sharedPrefix: the imports a chunk() reads from `GLSR.lib` instead of inlining (default `@/public/shared/`).
 */
export function createConfig(rootDir, { scriptsAlias = '', sharedPrefix = '@/public/shared/' } = {}) {
    /**
     * JavaScript bundle (IIFE, no CommonJS transforms).
     *
     * @param {string} source     Path relative to `+/` without extension.
     * @param {string} [outputDir='assets/scripts']
     * @param {string} [filename]  Without extension. When set, output.file
     *                             names the bundle; otherwise output.dir.
     * @param {Object} [options]
     * @param {boolean} [options.inlineCss=false]  Import stylesheets as strings,
     *                             for a bundle that adopts one into a shadow root.
     */
    const js = (source, outputDir = 'assets/scripts', filename = '', { inlineCss = false } = {}) => ({
        input: `+/${source}.js`,
        output: {
            ...(filename
                ? { file: `${outputDir}/${filename}.js` }
                : { dir: outputDir }
            ),
            format: 'iife',
            sourcemap: !isProduction,
        },
        plugins: [
            ...(inlineCss ? [inlineCssPlugin()] : []),
            ...jsPlugins(rootDir, { scriptsAlias }),
        ],
    });

    /**
     * Build a script module for the import map: ES output, with the shared sources imported by
     * their module id instead of inlined. `@site-reviews/*` imports are left for the import map.
     *
     * @param {string} source     Path relative to `+/` without extension.
     * @param {string} id         The module's name after `@site-reviews/`, which names the file.
     * @param {string} [outputDir='assets/scripts/modules']
     * @param {Object} [options]
     * @param {Object<string, string>} [options.shared]  Source import => module id, e.g.
     *                             `{ '@/public/core/index.js': '@site-reviews/core' }`.
     */
    const module = (source, id, outputDir = 'assets/scripts/modules', { shared = {} } = {}) => ({
        input: `+/${source}.js`,
        external: (importId) => importId.startsWith('@site-reviews/') || importId in shared,
        output: {
            file: `${outputDir}/${id}.js`,
            format: 'es',
            paths: shared,
            sourcemap: !isProduction,
        },
        plugins: [
            inlineCssPlugin(),
            ...jsPlugins(rootDir, { scriptsAlias }),
        ],
    });

    /**
     * JavaScript bundle (IIFE, with CommonJS transforms).
     *
     * @param {string} source     Path relative to `+/` without extension.
     * @param {string} [outputDir='assets/scripts']
     * @param {string} [filename]  Without extension. When set, output.file
     *                             names the bundle; otherwise output.dir.
     */
    const commonJs = (source, outputDir = 'assets/scripts', filename = '') => ({
        input: `+/${source}.js`,
        output: {
            ...(filename
                ? { file: `${outputDir}/${filename}.js` }
                : { dir: outputDir }
            ),
            format: 'iife',
            sourcemap: !isProduction,
        },
        plugins: jsPlugins(rootDir, { cjs: true, scriptsAlias }),
    });

    /**
     * CSS bundle with optional PostCSS selector namespacing.
     *
     * @param {string} source     Path relative to `+/` without extension.
     * @param {string} [outputDir='assets/styles']
     * @param {string} [namespace='']  What `:--namespace` in a selector becomes.
     * @param {string} [name='']  Names the emitted file {outputDir}/{name}.css
     *                            instead of the source basename.
     */
    const css = (source, outputDir = 'assets/styles', namespace = '', name = '') => ({
        // An object input names the rollup chunk, which names the extracted file.
        input: name ? { [name]: `+/${source}.css` } : `+/${source}.css`,
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

    /**
     * Build a stylesheet of two namespaces: the shared base's rules (`:--base`) under `base` and
     * the style's own (`:--namespace`) under `namespace`.
     *
     * @param {string} source     Path relative to `+/` without extension.
     * @param {string} outputDir
     * @param {Object} options
     * @param {string} options.base       The base namespace, e.g. `.glsr`.
     * @param {string} options.namespace  The style's namespace, e.g. `.glsr-default`.
     * @param {string} [options.name]     Names the emitted file; default the source basename.
     */
    const componentCss = (source, outputDir, { base, namespace, name = '' }) => ({
        input: name ? { [name]: `+/${source}.css` } : `+/${source}.css`,
        output: {
            dir: outputDir,
            format: 'es',
        },
        onwarn(warning, warn) {
            if (warning.code === 'FILE_NAME_CONFLICT') return;
            warn(warning);
        },
        plugins: cssPlugins(namespace, base),
    });

    /**
     * A CSS registry chunk: css() output named `{outputDir}/{id}.css` so the
     * premium AssetManager can compose per-context files from the manifest's
     * chunk ids. CSS needs no runtime registration; the id-keyed filename is
     * the whole contract.
     *
     * @param {string} source  Path relative to `+/` without extension.
     * @param {string} id      Chunk id, e.g. `filters.public`.
     * @param {string} [outputDir='assets/css/chunks']
     */
    const cssChunk = (source, id, outputDir = 'assets/css/chunks') => css(source, outputDir, '', id);

    /**
     * A registry chunk: like js(), but the output is a `GLSR.registry.define(id, …)`
     * definition, and the sharedPrefix imports resolve to `GLSR.lib.{name}` instead of being inlined.
     *
     * @param {string} source     Path relative to `+/` without extension.
     * @param {string} id         Chunk id, e.g. 'filters.public'.
     * @param {string} [outputDir='assets/js/chunks']
     * @param {Object} [options]
     * @param {string} [options.lib='GLSR.lib']  Where the shared modules are read.
     * @param {string} [options.registry='GLSR.registry']  Where the chunk is defined.
     */
    const chunk = (source, id, outputDir = 'assets/js/chunks', { lib = 'GLSR.lib', registry = 'GLSR.registry' } = {}) => ({
        input: `+/${source}.js`,
        external: (importId) => importId.startsWith(sharedPrefix),
        output: {
            file: `${outputDir}/${id}.js`,
            format: 'iife',
            globals: (importId) => importId.startsWith(sharedPrefix)
                ? `${lib}.${importId.slice(sharedPrefix.length).replace(/\.js$/, '')}`
                : importId,
            sourcemap: false, // the wrapper invalidates maps; dev uses js() entries
        },
        plugins: [
            inlineCssPlugin(),
            ...jsPlugins(rootDir, { scriptsAlias }),
            wrapChunkPlugin(id, registry),
        ],
    });

    /**
     * @param {string} source  Path relative to `+/`.
     * @param {string} [outputDir='assets']
     */
    const copy = (source, outputDir = 'assets') => ({
        _copy: true,
        src: `+/${source}`,
        dest: outputDir,
    });

    return {
        chunk,
        commonJs,
        componentCss,
        copy,
        css,
        cssChunk,
        js,
        module,
    };
}
