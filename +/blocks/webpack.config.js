const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const { resolve } = require('path');

module.exports = {
  ...defaultConfig,
  output: {
    ...defaultConfig.output,
    path: resolve(process.cwd(), 'assets/blocks'),
  },
  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
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
};
