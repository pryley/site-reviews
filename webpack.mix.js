const mix = require('laravel-mix');
const path = require('path');

require('laravel-mix-bundle-analyzer');

mix.disableSuccessNotifications();

mix.babelConfig({
  presets: ["@wordpress/default"],
});

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
  .babel([
    '+/scripts/mce-plugin.js',
  ], 'assets/scripts/mce-plugin.js')
  .combine([
    'node_modules/star-rating.js/src/star-rating.js',
    '+/scripts/public/init.js',
    '+/scripts/public/ajax.js',
    '+/scripts/public/excerpts.js',
    '+/scripts/public/forms.js',
    '+/scripts/public/pagination.js',
    '+/scripts/public/recaptcha.js',
    '+/scripts/public/validation.js',
    '+/scripts/site-reviews.js',
  ], 'assets/scripts/site-reviews.js')
  .combine([
    'node_modules/star-rating.js/src/star-rating.js',
    '+/scripts/admin/ajax.js',
    '+/scripts/admin/categories.js',
    '+/scripts/admin/color-picker.js',
    '+/scripts/admin/forms.js',
    '+/scripts/admin/metabox.js',
    '+/scripts/admin/notices.js',
    '+/scripts/admin/pinned.js',
    '+/scripts/admin/pointers.js',
    '+/scripts/admin/search.js',
    '+/scripts/admin/serializer.js',
    '+/scripts/admin/shortcode.js',
    '+/scripts/admin/status.js',
    '+/scripts/admin/sync.js',
    '+/scripts/admin/tabs.js',
    '+/scripts/admin/textarea-resize.js',
    '+/scripts/admin/tools.js',
    '+/scripts/site-reviews-admin.js',
  ], 'assets/scripts/site-reviews-admin.js')
  .js('+/scripts/site-reviews-blocks.js', 'assets/scripts')
  .sass('+/styles/inline-styles.scss', 'assets/styles')
  .sass('+/styles/site-reviews.scss', 'assets/styles')
  .sass('+/styles/site-reviews-admin.scss', 'assets/styles')
  .sass('+/styles/site-reviews-blocks.scss', 'assets/styles')
  .sass('+/styles/custom/bootstrap_4_custom.scss', 'assets/styles/custom')
  .sass('+/styles/custom/contact_form_7.scss', 'assets/styles/custom')
  .sass('+/styles/custom/divi.scss', 'assets/styles/custom')
  .sass('+/styles/custom/materialize.scss', 'assets/styles/custom')
  .sass('+/styles/custom/minimal.scss', 'assets/styles/custom')
  .sass('+/styles/custom/twentyfifteen.scss', 'assets/styles/custom')
  .sass('+/styles/custom/twentynineteen.scss', 'assets/styles/custom')
  .sass('+/styles/custom/twentyseventeen.scss', 'assets/styles/custom')
  .browserSync('site-reviews.test');

if (mix.inProduction()) {
  // mix.bundleAnalyzer();
}
