
import babel from '@rollup/plugin-babel';
import filesize from 'rollup-plugin-filesize';
import resolve from '@rollup/plugin-node-resolve';
import { terser } from "rollup-plugin-terser";

const production = process.env.NODE_ENV === 'production';

const babelConfig = {
  babelHelpers: 'runtime',
  plugins: [
    "@babel/plugin-proposal-optional-chaining",
    "@babel/plugin-transform-runtime",
  ],
  presets: [
    "@babel/preset-env"
  ],
}

const pureFuncs = Object
  .keys(console)
  .filter(key => !~['info', 'warn', 'error'].indexOf(key))
  .map(key => `console.${key}`);

export default [
  {
    input: {
      'site-reviews': '+/scripts/site-reviews.js',
    },
    output: {
      dir: 'assets/scripts',
      entryFileNames: '[name].js',
      format: 'iife',
    },
    plugins: [
      resolve(),
      filesize(),
      babel(babelConfig),
      terser({
        compress: {
          pure_funcs: pureFuncs,
        },
        format: {
          comments: false,
        },
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
    ]
  },
];
