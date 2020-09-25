const mix = require('laravel-mix');
const path = require('path');
const postCss = namespace => {
  return [
    require('postcss-import'),
    require('precss')(),
    require('postcss-calc')({preserve: false}),
    require('postcss-hexrgba'),
    require('postcss-custom-properties')({preserve: false}),
    require('./+/postcss/postcss-selector-namespaces')({namespace: namespace}),
    require('autoprefixer'),
  ];
};

require('laravel-mix-bundle-analyzer');

mix.babelConfig({
  plugins: [
    ['prismjs', {
        'languages': ['javascript', 'php', 'html', 'css'],
        'plugins': ['line-numbers'],
        'css': false,
    }],
  ],
  presets: [
    "@babel/preset-env",
    "@wordpress/default",
  ],
});

mix.disableSuccessNotifications();

mix.options({
  clearConsole: false,
  cssNano: {
    minifyFontValues: false,
    discardComments: {removeAll: true},
    zindex: false,
  },
  hmrOptions: {
    host: 'localhost',
    port: 3000,
  },
  processCssUrls: false,
  purifyCss: false,
  terser: {
    terserOptions: {
      compress: {
        drop_console: mix.inProduction(),
      },
      mangle: {
        properties: {regex: /[a-zA-Z]+_$/},
      },
    },
  },
});

mix.webpackConfig({
  resolve: {
    alias: {'@': path.resolve(__dirname, '+/scripts/')},
    modules: ['node_modules'],
  },
});

mix
  .babel('+/scripts/mce-plugin.js', 'assets/scripts/mce-plugin.js')
  .js('+/scripts/site-reviews.js', 'assets/scripts/site-reviews.js')
  .js('+/scripts/site-reviews-admin.js', 'assets/scripts/site-reviews-admin.js')
  .js('+/scripts/site-reviews-blocks.js', 'assets/scripts')
  .sass('+/styles/admin.scss', 'assets/styles/admin')
  .postCss('+/styles/inline-styles.css', 'assets/styles', postCss())
  .postCss('+/styles/bootstrap_4.css', 'assets/styles', postCss('.glsr-bootstrap_4'))
  .postCss('+/styles/bootstrap_4-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/bootstrap_4_custom.css', 'assets/styles', postCss('.glsr-bootstrap_4_custom'))
  .postCss('+/styles/bootstrap_4_custom-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/contact_form_7.css', 'assets/styles', postCss('.glsr-contact_form_7'))
  .postCss('+/styles/contact_form_7-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/default.css', 'assets/styles', postCss('.glsr-default'))
  .postCss('+/styles/default-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/divi.css', 'assets/styles', postCss('.et-db #et-main-area .glsr-divi, .et-db #et-boc .glsr-divi, .glsr-divi'))
  .postCss('+/styles/divi-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/materialize.css', 'assets/styles', postCss('.glsr-materialize'))
  .postCss('+/styles/materialize-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/minimal.css', 'assets/styles', postCss('.glsr-minimal'))
  .postCss('+/styles/minimal-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/ninja_forms.css', 'assets/styles', postCss('.glsr-ninja_forms'))
  .postCss('+/styles/ninja_forms-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/twentyfifteen.css', 'assets/styles', postCss('.glsr-twentyfifteen'))
  .postCss('+/styles/twentyfifteen-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/twentynineteen.css', 'assets/styles', postCss('.glsr-twentynineteen'))
  .postCss('+/styles/twentynineteen-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/twentyseventeen.css', 'assets/styles', postCss('.glsr-twentyseventeen'))
  .postCss('+/styles/twentyseventeen-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/twentysixteen.css', 'assets/styles', postCss('.glsr-twentysixteen'))
  .postCss('+/styles/twentysixteen-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .postCss('+/styles/twentytwenty.css', 'assets/styles', postCss('.glsr-twentytwenty'))
  .postCss('+/styles/twentytwenty-blocks.css', 'assets/styles/blocks', postCss('[data-block]'))
  .browserSync('site-reviews.test');

if (mix.inProduction()) {
  // mix.bundleAnalyzer();
}
