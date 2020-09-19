const mix = require('laravel-mix');
const path = require('path');

require('laravel-mix-bundle-analyzer');

mix.babelConfig({
  plugins: [
    ['prismjs', {
        'languages': ['javascript', 'php', 'html', 'css'],
        'plugins': ['line-numbers'],
        'css': false,
    }]
  ],
  presets: [
    "@babel/preset-env",
    "@wordpress/default",
  ],
});

mix.disableSuccessNotifications();

let postCss = namespace => {
  return [
    require('postcss-import'),
    require('precss')(),
    require('postcss-calc')({preserve: false}),
    require('postcss-custom-properties')({preserve: false}),
    require('postcss-hexrgba'),
    require('postcss-selector-namespace')({ namespace: namespace}),
    require('autoprefixer'),
  ];
};

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
        properties: {regex: /[a-zA-Z]+_$/}
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
  .postCss('+/styles/bootstrap_4-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-bootstrap_4'))
  .postCss('+/styles/bootstrap_4_custom.css', 'assets/styles', postCss('.glsr-bootstrap_4_custom'))
  .postCss('+/styles/bootstrap_4_custom-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-bootstrap_4_custom'))
  .postCss('+/styles/contact_form_7.css', 'assets/styles', postCss('.glsr-contact_form_7'))
  .postCss('+/styles/contact_form_7-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-contact_form_7'))
  .postCss('+/styles/default.css', 'assets/styles', postCss('.glsr-default'))
  .postCss('+/styles/default-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-default'))
  .postCss('+/styles/divi.css', 'assets/styles', postCss('.glsr-divi'))
  .postCss('+/styles/divi-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-divi'))
  .postCss('+/styles/materialize.css', 'assets/styles', postCss('.glsr-materialize'))
  .postCss('+/styles/materialize-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-materialize'))
  .postCss('+/styles/minimal.css', 'assets/styles', postCss('.glsr-minimal'))
  .postCss('+/styles/minimal-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-minimal'))
  .postCss('+/styles/twentyfifteen.css', 'assets/styles', postCss('.glsr-twentyfifteen'))
  .postCss('+/styles/twentyfifteen-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-twentyfifteen'))
  .postCss('+/styles/twentynineteen.css', 'assets/styles', postCss('.glsr-twentynineteen'))
  .postCss('+/styles/twentynineteen-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-twentynineteen'))
  .postCss('+/styles/twentyseventeen.css', 'assets/styles', postCss('.glsr-twentyseventeen'))
  .postCss('+/styles/twentyseventeen-blocks.css', 'assets/styles/blocks', postCss('[data-block] .glsr-twentyseventeen'))
  .browserSync('site-reviews.test');

if (mix.inProduction()) {
  // mix.bundleAnalyzer();
}
