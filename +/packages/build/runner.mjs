import { cpus } from 'os';
import { copyFileSync, mkdirSync, readFileSync } from 'fs';
import { gzipSync } from 'zlib';
import { performance } from 'perf_hooks';
import { rollup } from 'rollup';
import path from 'path';
import pLimit from 'p-limit';

// ------------------------------------------------------------------
//  ANSI helpers
// ------------------------------------------------------------------

const c = {
    bold: (s) => `\x1b[1m${s}\x1b[0m`,
    cyan: (s) => `\x1b[36m${s}\x1b[0m`,
    dim: (s) => `\x1b[2m${s}\x1b[0m`,
    green: (s) => `\x1b[32m${s}\x1b[0m`,
    red: (s) => `\x1b[31m${s}\x1b[0m`,
    yellow: (s) => `\x1b[33m${s}\x1b[0m`,
};

const stripAnsi = (s) => s.replace(/\x1b\[[0-9;]*m/g, '');
const padEndVisible = (s, n) => s + ' '.repeat(Math.max(0, n - stripAnsi(s).length));
const padStartVisible = (s, n) => ' '.repeat(Math.max(0, n - stripAnsi(s).length)) + s;

const formatSize = (bytes) => {
    if (bytes < 1024) return `${bytes} B`;
    return `${(bytes / 1024).toFixed(2)} KB`;
};

const colorSize = (bytes) => {
    const str = formatSize(bytes);
    if (bytes > 100 * 1024) return c.yellow(str);
    if (bytes > 50 * 1024) return c.cyan(str);
    return c.green(str);
};

const formatTime = (ms) => {
    if (ms < 1000) return `${Math.round(ms)}ms`;
    return `${(ms / 1000).toFixed(2)}s`;
};

// ------------------------------------------------------------------
//  Build runner
// ------------------------------------------------------------------

/**
 * Run a parallel Rollup build for the given configs.
 *
 * @param {import('rollup').RollupOptions[]} configs
 * @returns {Promise<void>}
 */
export async function build(configs) {
    const copyTasks = configs.filter((c) => c._copy);
    const rollupConfigs = configs.filter((c) => !c._copy);

    const concurrency = Math.max(cpus().length, 4);
    const limit = pLimit(concurrency);
    const results = [];
    const errors = [];
    const start = performance.now();

    // Copy static files
    for (const task of copyTasks) {
        try {
            mkdirSync(task.dest, { recursive: true });
            const dest = path.join(task.dest, path.basename(task.src));
            copyFileSync(task.src, dest);
            const contents = readFileSync(task.src);
            results.push({
                file: dest,
                size: contents.length,
                gzipped: gzipSync(contents).length,
                copied: true,
            });
        } catch (err) {
            errors.push({ err, input: task.src });
        }
    }

    // Build Rollup bundles
    const tasks = rollupConfigs.map((config) => limit(async () => {
        const input = typeof config.input === 'string'
            ? config.input
            : Object.values(config.input)[0];
        try {
            const bundle = await rollup({
                ...config,
                onwarn(warning, warn) {
                    if (warning.code === 'CIRCULAR_DEPENDENCY') return;
                    if (warning.code === 'MISSING_NAME_OPTION_FOR_IIFE_EXPORT') return;
                    if (config.onwarn) return config.onwarn(warning, warn);
                    warn(warning);
                },
            });
            const outputs = Array.isArray(config.output) ? config.output : [config.output];
            for (const outputOptions of outputs) {
                const { output } = await bundle.write(outputOptions);
                for (const chunk of output) {
                    const source = chunk.type === 'asset' ? chunk.source : chunk.code;
                    const size = typeof source === 'string' ? Buffer.byteLength(source) : source.length;
                    const gzipped = gzipSync(source).length;
                    const outputDir = outputOptions.dir || path.dirname(outputOptions.file || '');
                    const fileName = chunk.fileName;
                    results.push({
                        file: path.join(outputDir, fileName),
                        gzipped,
                        size,
                    });
                }
            }
            await bundle.close();
        } catch (err) {
            errors.push({ err, input });
        }
    }));

    await Promise.all(tasks);

    const elapsed = performance.now() - start;

    // ------------------------------------------------------------------
    //  Print errors
    // ------------------------------------------------------------------

    if (errors.length) {
        console.log(`\n  ${c.red(c.bold('Errors'))}`);
        for (const { err, input } of errors) {
            console.log(`\n  ${c.red('✗')} ${c.bold(input)}`);
            console.log(`    ${err.message}`);
            if (err.frame) console.log(err.frame);
        }
    }

    // ------------------------------------------------------------------
    //  Print summary
    // ------------------------------------------------------------------

    const js = results.filter(r => !r.copied && r.file.endsWith('.js')).sort((a, b) => b.size - a.size);
    const css = results.filter(r => !r.copied && r.file.endsWith('.css')).sort((a, b) => b.size - a.size);
    const copied = results.filter(r => r.copied);
    const all = [...js, ...css, ...copied];

    if (all.length) {
        const maxFile = Math.max(...all.map(r => r.file.length), 4);
        const maxSize = Math.max(...all.map(r => formatSize(r.size).length), 4);
        const maxGzip = Math.max(...all.map(r => formatSize(r.gzipped).length), 4);
        const divider = c.dim('─'.repeat(maxFile + maxSize + maxGzip + 4));

        const printTable = (label, rows) => {
            if (!rows.length) return;
            const sizeHeader = c.dim('Size'.padStart(maxSize));
            const gzipHeader = c.dim('Gzip'.padStart(maxGzip));
            console.log(`\n  ${c.bold(label.padEnd(maxFile))}  ${sizeHeader}  ${gzipHeader}`);
            console.log(`  ${divider}`);
            for (const r of rows) {
                const file = padEndVisible(c.dim(r.file), maxFile);
                const size = padStartVisible(colorSize(r.size), maxSize);
                const gzip = padStartVisible(c.dim(formatSize(r.gzipped)), maxGzip);
                console.log(`  ${file}  ${size}  ${gzip}`);
            }
        };

        printTable('JavaScript', js);
        printTable('CSS', css);
        printTable('Copied', copied);
    }

    // ------------------------------------------------------------------
    //  Footer
    // ------------------------------------------------------------------

    const status = errors.length
        ? c.red(`${errors.length} failed`)
        : c.green(`${all.length} bundles`);

    console.log(`\n  ${status} ${c.dim('in')} ${c.bold(formatTime(elapsed))}\n`);

    return { results, errors, elapsed };
}
