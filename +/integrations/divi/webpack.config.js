const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );

module.exports = {
  // Webpack starts bundling the assets from the following file.
  // @see https://webpack.js.org/concepts/#entry
  entry: {
    bundle: path.resolve(process.cwd(), '+/integrations/divi/src/index.ts'),
  },

  // Divi Visual Builder use of scripts that is already enqueued by WordPress and available
  // in global scope so those scripts don't need to be included on the bundle. For webpack
  // to recognize those files, the global variable needs to be registered as externals.
  // These allows global variable listed below to be imported into the module.
  // @see https://webpack.js.org/configuration/externals/#externals
  externals: {

    // Third party dependencies.
    jquery: 'jQuery',
    lodash: 'lodash',
    'object-hash': ['vendor', 'object-hash'],
    react: ['vendor', 'React'],
    'react-dom': ['vendor', 'ReactDOM'],
    underscore: '_',

    // WordPress dependencies.
    '@wordpress/i18n': ['vendor', 'wp', 'i18n'],
    '@wordpress/hooks': ['vendor', 'wp', 'hooks'],

    // Divi dependencies.
    '@divi/conversion': ['divi', 'conversion'],
    '@divi/data': ['divi', 'data'],
    '@divi/field-library': ['divi', 'fieldLibrary'],
    '@divi/icon-library': ['divi', 'iconLibrary'],
    '@divi/modal': ['divi', 'modal'],
    '@divi/module': ['divi', 'module'],
    '@divi/module-library': ['divi', 'moduleLibrary'],
    '@divi/module-utils': ['divi', 'moduleUtils'],
    '@divi/rest': ['divi', 'rest'],
    '@divi/script-library': ['divi', 'scriptLibrary'],
    '@divi/style-library': ['divi', 'styleLibrary'],
    '@divi/ui-library': ['divi', 'uiLibrary'],
  },

  // This option determine how different types of module within the project will be treated.
  // @see https://webpack.js.org/configuration/module/
  module: {

    // This option sets up loaders for webpack configuration.
    // Loaders allow webpack to process various types because by default webpack only
    // understand JavaScript and JSON files.
    // @see https://webpack.js.org/concepts/#loaders
    rules: [
      // Handle `.tsx` and `.ts` files.
      {
        test: /\.tsx?$/,
        use: {
          loader: 'ts-loader',
          options: {
            transpileOnly: true,
          },
        },
        exclude: /node_modules/,
      },

      // Handle `.jsx` files.
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: [

          // Spawns multiple processes and split work between them. This makes faster build.
          // @see https://webpack.js.org/loaders/thread-loader/
          {
            loader: 'thread-loader',
            options: {
              workers: - 1,
            },
          },

          // Transpiles JavaScript files using Babel. Translates newer syntax with less support
          // into older syntax with more support so the project can use newer syntax and have
          // them automatically translated into older syntax for compatibility suppoert.
          // @see https://www.npmjs.com/package/babel-loader
          // @see https://babeljs.io/
          {
            loader: 'babel-loader',
            options: {
              compact: false,
              presets: [

                // Preset that adds configuration for handling latest JavaScript syntax.
                // @see https://babeljs.io/docs/en/babel-preset-env
                ['@babel/preset-env', {
                  modules: false,
                  targets: '> 5%',
                }],

                // Preset that added configuration for handling react & JSX.
                // @see https://babeljs.io/docs/en/babel-preset-react
                '@babel/preset-react',
              ],
              plugins: [
                // Transform class properties syntax.
                // @see https://babeljs.io/docs/en/babel-plugin-transform-class-properties
                '@babel/plugin-transform-class-properties',
              ],
              cacheDirectory: false,
            },
          }
        ]
      },

      // Handle `.css` and `.scss` files.
      {
        test: /\.s?css$/i,
        use: [

          // Loader that enables imported css to be extracted and outputted into its own file.
          // @see https://webpack.js.org/plugins/mini-css-extract-plugin/#loader-options
          {
            loader: MiniCssExtractPlugin.loader,
          },

          // Loader that interprets @import and url() like import/require() and resolve them.
          // @see https://webpack.js.org/loaders/css-loader/
          {
            loader: 'css-loader',
            options: {
              url: false,
              importLoaders: 2,
            },
          },

          // Loader that loads SASS/SCSS and compiles it into CSS
          // @see https://webpack.js.org/loaders/sass-loader/
          {
            loader: 'sass-loader',
            options: {
            },
          },
        ],
      }
    ]
  },
  optimization: {
    // Split CSS code for visual builder and frontend.
    // vb.scss file will used for visual builder.
    // module.scss or any other *.scss file without vb.scss will be used for visual builder and frontend.
    splitChunks: {
      cacheGroups: {
        vb: {
          type: 'css/mini-extract',
          test: /[\\/]vb(\.module)?\.(sc|sa|c)ss$/,
          chunks: 'all',
          enforce: true,
          name( _, chunks, cacheGroupKey ) {
            const chunkName = chunks[ 0 ].name;
            return `${ path.dirname(
              chunkName
            ) }/${ cacheGroupKey }-${ path.basename( chunkName ) }`;
          },
        },
        default: false,
      },
    },
  },

  // Plugins alow webpack to perform wider range of tasks.
  plugins: [

    // Extract and output CSS into its own file.
    // @see https://webpack.js.org/plugins/mini-css-extract-plugin/
    new MiniCssExtractPlugin({
      filename: '../styles/[name].css',
    }),
    new CopyWebpackPlugin( {
      patterns: [
        {
          from: '**/module.json',
          context: '+/integrations/divi/src/modules',
          to: path.resolve(process.cwd(), 'assets/divi/modules-json'),
        },
      ]
    } ),
  ],

  // Determine how modules are resolved.
  // @see https://webpack.js.org/configuration/resolve/
  resolve: {
    alias: {'@site-reviews-divi': path.resolve(__dirname, './src')},
    // Allows extension to be leave off when importing.
    // @see https://webpack.js.org/configuration/resolve/#resolveextensions
    extensions: ['.js', '.jsx', '.tsx', '.ts', '.json'],
  },

  // Determine where the created bundles will be outputted.
  // @see https://webpack.js.org/concepts/#output
  output: {
    filename: '[name].js',
    path: path.resolve(process.cwd(), 'assets/divi/scripts'),
  },
  stats: {
    errorDetails: true,
  },
};
