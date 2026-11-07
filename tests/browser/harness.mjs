/**
 * What the browser tests share. Importing this file starts nothing.
 *
 * GLSR_ENGINE=chromium|webkit picks the engine (chromium by default).
 * GLSR_BROWSER is the Chromium-family binary (Brave by default); WebKit is
 * the build Playwright ships. WP_ENV_PORT overrides the port .wp-env.json names.
 */
import { spawnSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import { createRequire } from 'node:module';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const require = createRequire(import.meta.url);
const { chromium, webkit } = require('playwright');

export const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
export const ENGINE = process.env.GLSR_ENGINE || 'chromium';
export const BROWSER = process.env.GLSR_BROWSER || '/Applications/Brave Origin.app/Contents/MacOS/Brave Origin';
const wpEnv = JSON.parse(readFileSync(path.join(ROOT, '.wp-env.json'), 'utf8'));
export const SITE = `http://localhost:${process.env.WP_ENV_PORT || wpEnv.port || 8888}`;
export const REVIEWS = '/site-reviews/v1/reviews';

const nonces = new WeakMap();

export const adminUrl = (path = '') => `${SITE}/wp-admin/${path}`;

/**
 * Create a review as the context's user and return it; `pending: true` leaves it unapproved.
 */
export const createReview = async (context, data = {}) => {
    const { pending = false, ...fields } = data;
    const review = await restRequest(context, 'POST', REVIEWS, {
        content: 'A probe review written by the browser suite.',
        rating: 4,
        title: 'Probe review',
        ...fields,
    });
    if (pending) {
        wpCli(['post', 'update', String(review.id), '--post_status=pending']);
    }
    return review;
};

export const deleteReview = (context, id) => restRequest(context, 'DELETE', `${REVIEWS}/${id}`, { force: true });

export const launch = async (options = {}) => {
    try {
        return 'webkit' === ENGINE
            ? await webkit.launch(options)
            : await chromium.launch({ executablePath: BROWSER, ...options });
    } catch (error) {
        throw new Error(
            'could not launch a browser.\n' +
            ('webkit' === ENGINE
                ? '  Playwright\'s WebKit build is missing: npx playwright install webkit\n'
                : `  tried: ${BROWSER}\n` +
                  '  set GLSR_BROWSER to a Chromium-family binary, e.g.\n' +
                  '  GLSR_BROWSER="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"\n') +
            `  (${error.message.split('\n')[0]})`
        );
    }
};

export const login = async (page, user = 'admin', password = 'password') => {
    await page.goto(`${SITE}/wp-login.php`);
    await page.fill('#user_login', user);
    await page.fill('#user_pass', password);
    await Promise.all([page.waitForNavigation(), page.click('#wp-submit')]);
    if (!page.url().includes('/wp-admin/')) {
        throw new Error(`could not log in to ${SITE} as ${user} (landed on ${page.url()}). Is wp-env running? make test:install`);
    }
};

/**
 * Collect the page's uncaught errors in a list that fills while the page runs.
 */
export const pageErrorsOf = (page) => {
    const errors = [];
    page.on('pageerror', error => {
        // Left out: WebKit reports a request that a navigation cancels as a page error, and
        // WordPress's common.js throws on a list-table row's focusout when no focusin set
        // focusedRowActions first, which a WebKit click produces because it does not focus.
        if (error.message.endsWith('due to access control checks.') || error.message.includes('focusedRowActions')) {
            return;
        }
        errors.push(error.message);
    });
    return errors;
};

/**
 * Return the assertions that print one script's PASS and FAIL lines.
 */
export const reporter = () => {
    let failures = 0;
    const assert = (ok, label) => {
        console.log(`${ok ? 'PASS' : 'FAIL'}  ${label}`);
        if (!ok) {
            failures++;
        }
    };
    return {
        assert,
        done: passed => {
            console.log(failures ? `\n${failures} failing assertion(s)` : `\n${passed}`);
            process.exit(failures ? 1 : 0);
        },
        equal: (actual, expected, label) => {
            const [got, wanted] = [JSON.stringify(actual), JSON.stringify(expected)];
            assert(got === wanted, got === wanted ? label : `${label}\n      expected ${wanted}\n      got      ${got}`);
        },
        info: line => console.log(`INFO  ${line}`),
    };
};

/**
 * Send a REST request as the context's user and return the decoded answer.
 */
export const restRequest = async (context, method, route, data = {}) => {
    if (!nonces.has(context)) {
        const response = await context.request.get(`${SITE}/wp-admin/admin-ajax.php?action=rest-nonce`);
        nonces.set(context, await response.text());
    }
    const isRead = 'GET' === method;
    // Through the context, not a page, so it works after every page is closed.
    // `rest_route` works with any permalink structure.
    const response = await context.request.fetch(`${SITE}/`, {
        data: isRead ? undefined : data,
        headers: { 'X-WP-Nonce': nonces.get(context) },
        method,
        params: { rest_route: route, ...(isRead ? data : {}) },
    });
    if (!response.ok()) {
        throw new Error(`${method} ${route} answered ${response.status()}: ${(await response.text()).slice(0, 300)}`);
    }
    return response.json();
};

/**
 * Run a wp-cli command in the wp-env container and return its output.
 *
 * @param {string[]} args the words after `wp`, one argument each
 */
export const wpCli = (args) => {
    const result = spawnSync('npx', ['@wordpress/env', 'run', 'cli', 'wp', ...args], { cwd: ROOT, encoding: 'utf8' });
    if (0 !== result.status) {
        throw new Error(`wp ${args.join(' ')} failed: ${(result.stderr || result.stdout).slice(0, 300)}`);
    }
    return result.stdout.trim();
};
