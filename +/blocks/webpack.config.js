const autoprefixer = require('autoprefixer');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const { resolve } = require('path');

// The WordPress preset pins autoprefixer to WordPress's own browser list. The blocks' styles
// load on the front end, so they use the plugin's list (package.json "browserslist") instead.
const withPluginBrowsers = rule => {
  const use = Array.isArray(rule.use) ? rule.use.map(loader => {
    const plugins = loader?.options?.postcssOptions?.plugins;
    if (!Array.isArray(plugins)) {
      return loader;
    }
    return {
      ...loader,
      options: {
        ...loader.options,
        postcssOptions: {
          ...loader.options.postcssOptions,
          plugins: plugins.map(plugin => 'autoprefixer' === plugin?.postcssPlugin ? autoprefixer({ grid: true }) : plugin),
        },
      },
    };
  }) : rule.use;
  return { ...rule, use };
};

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
        ...conf.module.rules.map(withPluginBrowsers),
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
