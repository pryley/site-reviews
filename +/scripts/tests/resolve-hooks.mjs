/**
 * Resolves `@site-reviews/{name}` to the BUILT module, as the import map does on a page. The
 * `run` query of the importing file is carried over, so each page load gets fresh module instances.
 */
const PREFIX = '@site-reviews/';

export async function resolve(specifier, context, next) {
    if (!specifier.startsWith(PREFIX)) {
        return next(specifier, context);
    }
    const run = new URL(context.parentURL || 'file:///').searchParams.get('run') || '0';
    const url = new URL(`../../../assets/scripts/modules/${specifier.slice(PREFIX.length)}.js`, import.meta.url);
    url.searchParams.set('run', run)
    return { format: 'module', shortCircuit: true, url: url.href };
}
