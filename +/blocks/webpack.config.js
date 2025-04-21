const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const { resolve } = require('path');

let config = defaultConfig;

// When --experimental-modules is passed, config is an array (with two objects)
// instead of an object. This ensures that config is always an array.
if (!Array.isArray(config)) {
  config = [ config ];
}

config = config.map(conf => {
  return {
    ...conf,
    output: {
      ...conf.output,
      path: resolve(process.cwd(), 'assets/blocks'),
    },
    module: {
      ...conf.module,
      rules: [
        ...conf.module.rules,
        {
          test: /(j|t)sx?$/,
          include: [
            resolve(process.cwd(), '+/packages'),
          ],
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                '@wordpress/babel-preset-default',
              ],
            },
          },
        },
      ],
    },
  }
});

module.exports = config;
