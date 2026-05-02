#!/usr/bin/env node

import { pathToFileURL } from 'url';
import path from 'path';
import { build } from './runner.mjs';

const configPath = path.resolve(process.cwd(), 'rollup.config.mjs');
const { default: configs } = await import(pathToFileURL(configPath).href);

const { errors } = await build(configs);
process.exit(errors.length ? 1 : 0);
