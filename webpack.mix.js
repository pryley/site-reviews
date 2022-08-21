const mix = require('laravel-mix')
const path = require('path')
const postCss = namespace => {
  return [
    require('postcss-import'),
    require('postcss-preset-env')({
      features: { 'custom-properties': false },
      stage: 1,
    }),
    require('postcss-calc'),
    require('postcss-hexrgba'),
    require('postcss-selector-namespace')({ namespace }),
  ]
}

require('laravel-mix-bundle-analyzer')

mix.disableSuccessNotifications()

let pureFuncs = Object
  .keys(console)
  .filter(key => !~['info', 'warn', 'error'].indexOf(key))
  .map(key => `console.${key}`);

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
        pure_funcs: mix.inProduction() ? pureFuncs : [],
      },
      mangle: {
        // properties: {regex: /[a-zA-Z]+_$/},
        // properties: {regex: /^_[a-zA-Z]+$/},
      },
    },
  },
})

mix.webpackConfig({
  target: ['web', 'es5'],
  resolve: {
    alias: {'@': path.resolve(__dirname, '+/scripts/')},
    modules: ['node_modules'],
  },
  // stats: {
  //   children: true,
  // },
})

mix
  .setPublicPath('.')
  .babel('+/scripts/mce-plugin.js', 'assets/scripts/mce-plugin.js')
  .babel('+/scripts/rollback.js', 'assets/scripts/rollback.js')
  // .js('+/scripts/site-reviews.js', 'assets/scripts')
  .js('+/scripts/site-reviews-admin.js', 'assets/scripts')
  .js('+/scripts/site-reviews-blocks.js', 'assets/scripts')
  .sass('+/styles/admin.scss', 'assets/styles/admin')
  .postCss('+/styles/inline-styles.css', 'assets/styles', postCss())
  .postCss('+/styles/bootstrap.css', 'assets/styles', postCss('.glsr-bootstrap'))
  .postCss('+/styles/contact_form_7.css', 'assets/styles', postCss('.glsr-contact_form_7'))
  .postCss('+/styles/default.css', 'assets/styles', postCss('.glsr-default'))
  .postCss('+/styles/divi.css', 'assets/styles', postCss('.et-db #et-main-area .glsr-divi, .et-db #et-boc .glsr-divi, .glsr-divi'))
  .postCss('+/styles/elementor.css', 'assets/styles', postCss('.glsr-elementor'))
  .postCss('+/styles/minimal.css', 'assets/styles', postCss('.glsr-minimal'))
  .postCss('+/styles/ninja_forms.css', 'assets/styles', postCss('.glsr-ninja_forms'))
  .postCss('+/styles/twentyfifteen.css', 'assets/styles', postCss('.glsr-twentyfifteen'))
  .postCss('+/styles/twentysixteen.css', 'assets/styles', postCss('.glsr-twentysixteen'))
  .postCss('+/styles/twentyseventeen.css', 'assets/styles', postCss('.glsr-twentyseventeen'))
  .postCss('+/styles/twentynineteen.css', 'assets/styles', postCss('.glsr-twentynineteen'))
  .postCss('+/styles/twentytwenty.css', 'assets/styles', postCss('.glsr-twentytwenty'))
  .postCss('+/styles/twentytwentyone.css', 'assets/styles', postCss('.glsr-twentytwentyone'))
  .postCss('+/styles/twentytwentytwo.css', 'assets/styles', postCss('.glsr-twentytwentytwo'))
  .postCss('+/styles/wpforms.css', 'assets/styles', postCss('.glsr-wpforms'))
  .postCss('+/styles/bootstrap-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/contact_form_7-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/default-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/minimal-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/ninja_forms-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentyfifteen-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentysixteen-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentyseventeen-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentynineteen-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentytwenty-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentytwentyone-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/twentytwentytwo-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  .postCss('+/styles/wpforms-blocks.css', 'assets/styles/blocks', postCss('.wp-block'))
  // .version()
  .browserSync({
    files: [
        '+/**/*.(css|js)', 
        'plugin/(views|templates)/**/*.php', 
    ],
    notify: false,
    proxy: 'https://site-reviews.test',
  })

if (mix.inProduction()) {
  // mix.bundleAnalyzer()
}
