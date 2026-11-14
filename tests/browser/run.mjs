/**
 * Runs the admin screen tests, each in Chromium and in WebKit. They need wp-env.
 *
 *   npm run test:browser               every screen
 *   npm run test:browser -- settings   one screen
 *
 * GLSR_ENGINE limits the run to one engine.
 */
import { spawnSync } from 'node:child_process';
import path from 'node:path';
import { ROOT } from './harness.mjs';

const TESTS = {
    'block-editor': 'block-editor.mjs',
    'category-quick-edit': 'category-quick-edit.mjs',
    dashboard: 'dashboard.mjs',
    documentation: 'documentation.mjs',
    'foreign-screens': 'foreign-screens.mjs',
    notices: 'notices.mjs',
    plugins: 'plugins.mjs',
    'review-editor': 'review-editor.mjs',
    'reviews-list': 'reviews-list.mjs',
    settings: 'settings.mjs',
    tools: 'tools.mjs',
    welcome: 'welcome.mjs',
};
const ENGINES = process.env.GLSR_ENGINE ? [process.env.GLSR_ENGINE] : ['chromium', 'webkit'];

const names = process.argv.slice(2);
const unknown = names.filter(name => !TESTS[name]);
if (unknown.length) {
    console.error(`browser: no test for ${unknown.join(', ')}. Known: ${Object.keys(TESTS).join(', ')}`);
    process.exit(1);
}

const failed = [];
for (const name of names.length ? names : Object.keys(TESTS)) {
    for (const engine of ENGINES) {
        console.log(`\n== ${name} in ${engine}`);
        const { status } = spawnSync(process.execPath, [path.join(ROOT, 'tests/browser', TESTS[name])], {
            env: { ...process.env, GLSR_ENGINE: engine },
            stdio: 'inherit',
        });
        if (0 !== status) {
            failed.push(`${name} in ${engine}`);
        }
    }
}

console.log(failed.length ? `\nFailed: ${failed.join(', ')}` : '\nEvery screen test passed');
process.exit(failed.length ? 1 : 0);
