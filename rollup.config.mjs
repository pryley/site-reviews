import alias from '@rollup/plugin-alias';
import babel from '@rollup/plugin-babel';
import filesize from 'rollup-plugin-filesize';
import postcss from 'rollup-plugin-postcss'
import resolve from '@rollup/plugin-node-resolve';
import terser from '@rollup/plugin-terser';

const pluginsJavascript = () => [
  alias({
    entries: {
      '@': './+/scripts/',
    },
  }),
  resolve(),
  filesize(),
  babel({
    babelHelpers: 'runtime',
    plugins: [
      '@babel/plugin-proposal-optional-chaining',
      '@babel/plugin-transform-runtime',
    ],
    presets: [
      '@babel/preset-env',
    ],
  }),
  terser({
    compress: {
      pure_funcs: Object.keys(console)
        .filter(key => !~['info', 'warn', 'error'].indexOf(key))
        .map(key => `console.${key}`),
    },
    format: { comments: false },
    // mangle: {
    //   reserved: [
    //     'close',
    //     'destroy',
    //     'disableButton',
    //     'enableButton',
    //     'events',
    //     'execute',
    //     'get',
    //     'init',
    //     'off',
    //     'on',
    //     'once',
    //     'open',
    //     'post',
    //     'render',
    //     'reset',
    //     'setErrors',
    //     'submitForm',
    //     'toggleError',
    //     'trigger',
    //     'validate',
    //   ],
    //   // properties: true,
    //   properties: {
    //     regex: /^_/,
    //   },
    // },
  }),
  // require('rollup-plugin-sizes')({ details: true }),
  // require('rollup-plugin-visualizer').visualizer({ template: 'sunburst' }),
];

const pluginsStylesheet = (namespace) => {
  return [
    postcss({
      extract: true,
      minimize: false,
      plugins: [
        require('postcss-import'),
        require('postcss-preset-env')({
          features: { 'custom-properties': false },
          stage: 1,
        }),
        require('postcss-calc'),
        require('postcss-selector-namespace')({ namespace }),
      ],
    }),
  ];
}

const css = (id, namespace = '', dir = 'assets/styles') => ({
  input: `+/styles/${id}.css`,
  plugins: pluginsStylesheet(namespace),
  output: {
    file: `./${dir}/${id}.css`,
  },
  onwarn (warning, warn) {
    if (warning.code === 'FILE_NAME_CONFLICT') return
    warn(warning)
  }
})

const js = (id) => ({
  input: `+/scripts/${id}.js`,
  plugins: pluginsJavascript(),
  output: {
    dir: 'assets/scripts',
    format: 'iife',
  },
})

export default [
  js('site-reviews', ),
  // css('inline-styles'),
  // css('bootstrap_4', '.glsr-bootstrap_4'),
  // css('bootstrap_4_custom', '.glsr-bootstrap_4_custom'),
  // css('contact_form_7', '.glsr-contact_form_7'),
  // css('default', '.glsr-default'),
  // css('divi', '.et-db #et-main-area .glsr-divi, .et-db #et-boc .glsr-divi, .glsr-divi'),
  // css('minimal', '.glsr-minimal'),
  // css('ninja_forms', '.glsr-ninja_forms'),
  // css('twentyfifteen', '.glsr-twentyfifteen'),
  // css('twentysixteen', '.glsr-twentysixteen'),
  // css('twentyseventeen', '.glsr-twentyseventeen'),
  // css('twentynineteen', '.glsr-twentynineteen'),
  // css('twentytwenty', '.glsr-twentytwenty'),
  // css('twentytwentyone', '.glsr-twentytwentyone'),
  // css('twentytwentytwo', '.glsr-twentytwentytwo'),
  // css('wpforms', '.glsr-wpforms'),
  // css('bootstrap_4-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('bootstrap_4_custom-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('contact_form_7-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('default-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('divi-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('minimal-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('ninja_forms-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentyfifteen-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentysixteen-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentyseventeen-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentynineteen-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentytwenty-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentytwentyone-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('twentytwentytwo-blocks', '[data-block]', 'assets/styles/blocks'),
  // css('wpforms-blocks', '[data-block]', 'assets/styles/blocks'),
];
