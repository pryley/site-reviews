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
  postCss: [
    require('postcss-import'),
    require('precss')(),
    require('postcss-calc')({preserve: false}),
    require('postcss-custom-properties')({preserve: false}),
    require('postcss-hexrgba'),
    require('autoprefixer'),
  ],
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
