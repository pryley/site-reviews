#!/usr/bin/env php
<?php

/**
 * Regenerates the third-party stubs in tests/stubs from tests/bin/stubs-manifest.php.
 *
 * Usage:
 *   php tests/bin/generate-stubs.php             regenerate every entry whose source is available
 *   php tests/bin/generate-stubs.php woocommerce elementor
 *   php tests/bin/generate-stubs.php --list      show the manifest and each source's availability
 *
 * Entries sourced from a local zip that is absent are skipped with a notice —
 * unless the slug was requested explicitly, which makes the absence an error.
 *
 * After writing, every file in tests/stubs (including the hand-written fakes) is
 * scanned for symbols declared in more than one file: the suite loads the stubs
 * together, so a cross-stub duplicate is a fatal redeclaration waiting to happen.
 */

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;
use StubsGenerator\Finder;
use StubsGenerator\StubsGenerator;

error_reporting(E_ALL);

define('REPO_ROOT', dirname(__DIR__, 2));
define('STUBS_DIR', REPO_ROOT.'/tests/stubs');
define('MANIFEST', __DIR__.'/stubs-manifest.php');
define('DEFAULT_EXCLUDES', ['vendor', 'vendors', 'node_modules', 'tests', 'Tests', 'test', 'Test', 'build', 'dist']);

// The hand-written working fakes. Never generated, never overwritten.
define('PROTECTED_STUBS', ['akismet', 'polylang', 'wp-cli']);

// Polyfills of these are guarded by function_exists() upstream; the generator
// strips the guard, and phpstan's own phar already declares them — a fatal
// redeclaration when phpstan loads the stub. (getallheaders: bundled by
// SureCart via ralouphie/getallheaders.)
define('PRUNE_FUNCTIONS', ['getallheaders']);

if (!is_file(REPO_ROOT.'/vendor/php-stubs/generator/src/StubsGenerator.php')) {
    fwrite(STDERR, "php-stubs/generator is not installed — run: make test:install\n");
    exit(1);
}
require REPO_ROOT.'/vendor/autoload.php';

$args = array_slice($argv, 1);
$list = in_array('--list', $args, true);
$requested = array_values(array_filter($args, fn ($arg) => !str_starts_with($arg, '--')));

$manifest = require MANIFEST;
foreach (PROTECTED_STUBS as $fake) {
    if (isset($manifest[$fake])) {
        fwrite(STDERR, "error: [{$fake}] is a hand-written fake, not a generated stub; remove it from the manifest.\n");
        exit(1);
    }
}
foreach ($requested as $slug) {
    if (in_array($slug, PROTECTED_STUBS, true)) {
        fwrite(STDERR, "error: [{$slug}] is a hand-written fake, not a generated stub; it cannot be regenerated.\n");
        exit(1);
    }
    if (!isset($manifest[$slug])) {
        fwrite(STDERR, "error: [{$slug}] is not in the manifest ".MANIFEST."\n");
        exit(1);
    }
}

/**
 * @return string[] every source spec of an entry
 */
function sourcesOf(array $entry): array
{
    return (array) ($entry['source'] ?? []);
}

/**
 * @return string 'url'|'github'|'zip'|'dir'
 */
function sourceType(string $source): string
{
    if (preg_match('{^https?://}', $source)) {
        return 'url';
    }
    if (str_starts_with($source, 'github:')) {
        return 'github';
    }
    if (str_ends_with($source, '.zip')) {
        return 'zip';
    }
    return 'dir';
}

function httpGet(string $url): string|false
{
    $context = stream_context_create([
        'http' => [
            'follow_location' => 1,
            'timeout' => 120,
            'user_agent' => 'site-reviews-stub-generator',
        ],
    ]);
    return @file_get_contents($url, false, $context);
}

/**
 * @return string|false the extracted source root
 */
function extractZip(string $zipfile, string $dest): string|false
{
    $zip = new ZipArchive();
    if (true !== $zip->open($zipfile)) {
        return false;
    }
    if (!$zip->extractTo($dest)) {
        $zip->close();
        return false;
    }
    $zip->close();
    // wp.org and GitHub zips wrap everything in one top-level directory; zips
    // made with the macOS Finder add __MACOSX/ (AppleDouble copies, including
    // ._*.php files no parser should ever see) beside it.
    $entries = array_values(array_filter(
        array_diff(scandir($dest), ['.', '..', '__MACOSX']),
        fn ($entry) => !str_starts_with($entry, '.')
    ));
    if (1 === count($entries) && is_dir("{$dest}/{$entries[0]}")) {
        return "{$dest}/{$entries[0]}";
    }
    return $dest;
}

/**
 * Reads the Version header from a plugin's main file or a theme's style.css.
 */
function detectVersion(string $dir): string
{
    $candidates = array_merge(glob("{$dir}/*.php") ?: [], glob("{$dir}/style.css") ?: []);
    foreach ($candidates as $file) {
        $contents = (string) file_get_contents($file, false, null, 0, 8192);
        if (preg_match('/^(?:[ \t\/*#@]*)Version:\s*(\S+)/mi', $contents, $match)) {
            return $match[1];
        }
    }
    return 'unknown version';
}

/**
 * Resolves one source spec to ['dir' => …, 'label' => …] or a skip/error string.
 *
 * @return array{dir: string, label: string}|string
 */
function resolveSource(string $source, string $slug, string $workdir, int $index): array|string
{
    $type = sourceType($source);
    if ('dir' === $type) {
        $dir = REPO_ROOT.'/'.$source;
        if (!is_dir($dir)) {
            return "error: source directory not found: {$source}";
        }
        return ['dir' => $dir, 'label' => $source];
    }
    if ('zip' === $type && !preg_match('{^https?://}', $source)) {
        $zipfile = REPO_ROOT.'/'.$source;
        if (!is_file($zipfile)) {
            return "skip: local zip not found: {$source}";
        }
        $dir = extractZip($zipfile, "{$workdir}/{$slug}-{$index}");
        return $dir ? ['dir' => $dir, 'label' => $source] : "error: could not unzip {$source}";
    }
    if ('github' === $type) {
        $repo = substr($source, strlen('github:'));
        $release = httpGet("https://api.github.com/repos/{$repo}/releases/latest");
        $tag = $release ? (json_decode($release, true)['tag_name'] ?? '') : '';
        if ('' === $tag) {
            return "error: could not resolve the latest release of {$repo}";
        }
        $source = "https://codeload.github.com/{$repo}/zip/refs/tags/{$tag}";
    }
    echo "  downloading {$source}\n";
    $data = httpGet($source);
    if (false === $data) {
        return "error: download failed: {$source}";
    }
    $zipfile = "{$workdir}/{$slug}-{$index}.zip";
    file_put_contents($zipfile, $data);
    $dir = extractZip($zipfile, "{$workdir}/{$slug}-{$index}");
    return $dir ? ['dir' => $dir, 'label' => $source] : "error: could not unzip {$source}";
}

/**
 * Lists the top-level symbols a stub file declares, tolerating any PHP.
 * Used for the cross-stub duplicate scan, so it reads the real files.
 *
 * @return string[] e.g. ['class:foo\bar', 'function:baz', 'constant:QUX']
 */
function declaredSymbols(string $file): array
{
    $tokens = token_get_all((string) file_get_contents($file));
    $ns = '';
    $depth = 0;
    $topDepth = 0; // 1 while inside a braced namespace block, else 0
    $symbols = [];
    $total = count($tokens);
    for ($i = 0; $i < $total; ++$i) {
        $token = $tokens[$i];
        if (is_string($token)) {
            $depth += ('{' === $token) ? 1 : (('}' === $token) ? -1 : 0);
            continue;
        }
        [$id, $text] = $token;
        if (in_array($id, [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true)) {
            ++$depth;
            continue;
        }
        $nextMeaningful = function (int $from) use ($tokens, $total): array {
            for ($j = $from; $j < $total; ++$j) {
                if (is_string($tokens[$j])) {
                    return [$tokens[$j], null, $j];
                }
                if (T_WHITESPACE !== $tokens[$j][0] && T_COMMENT !== $tokens[$j][0] && T_DOC_COMMENT !== $tokens[$j][0]) {
                    return [$tokens[$j][1], $tokens[$j][0], $j];
                }
            }
            return ['', null, $total];
        };
        if (T_NAMESPACE === $id) {
            [$name, $type, $at] = $nextMeaningful($i + 1);
            $ns = (null !== $type && in_array($type, [T_STRING, T_NAME_QUALIFIED], true)) ? $name : '';
            // A braced namespace shifts the top level one deeper; `namespace X;`
            // keeps it at zero.
            [$next] = ('' === $ns) ? [$name] : $nextMeaningful($at + 1);
            $topDepth = ('{' === $next) ? 1 : 0;
            continue;
        }
        if ($depth > $topDepth) {
            continue; // inside a class body
        }
        if (in_array($id, [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], true)) {
            [$name, $type] = $nextMeaningful($i + 1);
            if (T_STRING === $type) { // skips ::class and anonymous classes
                $symbols[] = 'class:'.strtolower(($ns ? $ns.'\\' : '').$name);
            }
        } elseif (T_FUNCTION === $id) {
            [$name, $type] = $nextMeaningful($i + 1);
            if ('&' === $name) {
                [$name, $type] = $nextMeaningful($i + 2);
            }
            if (T_STRING === $type) {
                $symbols[] = 'function:'.strtolower(($ns ? $ns.'\\' : '').$name);
            }
        } elseif (in_array($id, [T_STRING, T_NAME_FULLY_QUALIFIED], true)
            && in_array(strtolower($text), ['define', '\define'], true)) {
            [$name] = $nextMeaningful($i + 1);
            if ('(' === $name) {
                [$name, $type] = $nextMeaningful($i + 2);
                if (T_CONSTANT_ENCAPSED_STRING === $type) {
                    $symbols[] = 'constant:'.trim($name, '"\'');
                }
            }
        }
    }
    return array_unique($symbols);
}

/**
 * Normalizes the generator's output into [namespaceName => Stmt[]] pairs.
 *
 * @param Node[] $stmts
 * @return array<int, array{0: string, 1: Stmt[]}>
 */
function groupByNamespace(array $stmts): array
{
    $groups = [];
    $global = [];
    foreach ($stmts as $stmt) {
        if ($stmt instanceof Stmt\Namespace_) {
            $groups[] = [$stmt->name ? $stmt->name->toString() : '', $stmt->stmts];
        } else {
            $global[] = $stmt;
        }
    }
    if ($global) {
        $groups[] = ['', $global];
    }
    return $groups;
}

/**
 * Removes every class whose ancestry reaches \WP_CLI_Command. Plugins ship CLI
 * commands freely, but nothing declares WP_CLI_Command for the stubs' two
 * consumers — the suite's wp-cli fake declares only WP_CLI, and phpstan merely
 * scans (never executes) the wp-cli-stubs — so such a class is a fatal
 * "class not found" at load time in both.
 *
 * @param Node[] $stmts
 * @return Stmt\Namespace_[]
 */
function pruneWpCliCommands(array $stmts): array
{
    $groups = groupByNamespace($stmts);
    $parents = []; // lc fqn => lc parent fqn
    foreach ($groups as [$ns, $nsStmts]) {
        foreach ($nsStmts as $stmt) {
            if ($stmt instanceof Stmt\Class_ && $stmt->name && $stmt->extends) {
                $fqn = strtolower(($ns ? $ns.'\\' : '').$stmt->name->name);
                $parents[$fqn] = strtolower(ltrim($stmt->extends->toString(), '\\'));
            }
        }
    }
    $doomed = function (string $fqn) use ($parents): bool {
        $seen = [];
        while (isset($parents[$fqn]) && !isset($seen[$fqn])) {
            $seen[$fqn] = true;
            $fqn = $parents[$fqn];
        }
        return 'wp_cli_command' === $fqn;
    };
    $pruned = [];
    foreach ($groups as [$ns, $nsStmts]) {
        $kept = array_values(array_filter($nsStmts, function (Stmt $stmt) use ($ns, $doomed) {
            if ($stmt instanceof Stmt\Class_ && $stmt->name) {
                return !$doomed(strtolower(($ns ? $ns.'\\' : '').$stmt->name->name));
            }
            return true;
        }));
        if ($kept) {
            $pruned[] = new Stmt\Namespace_($ns ? new Name($ns) : null, $kept);
        }
    }
    return $pruned;
}

/**
 * Removes every symbol WordPress core already declares — plugins override
 * pluggable functions inside function_exists() guards, the generator strips the
 * guard, and the result is a fatal redeclaration in both consumers (phpstan
 * bootstraps wordpress-stubs; the suite runs real WordPress). The name set
 * comes from the wordpress-stubs the analysis itself uses.
 *
 * @param Node[] $stmts
 * @return Stmt\Namespace_[]
 */
function pruneWordPressSymbols(array $stmts): array
{
    static $wordpress = null;
    if (null === $wordpress) {
        $wordpress = array_flip(declaredSymbols(REPO_ROOT.'/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php'));
        foreach (PRUNE_FUNCTIONS as $function) {
            $wordpress['function:'.strtolower($function)] = true;
        }
    }
    $pruned = [];
    foreach (groupByNamespace($stmts) as [$ns, $nsStmts]) {
        $kept = array_values(array_filter($nsStmts, function (Stmt $stmt) use ($ns, $wordpress) {
            $prefix = $ns ? strtolower($ns).'\\' : '';
            if ($stmt instanceof Stmt\ClassLike && $stmt->name) {
                return !isset($wordpress['class:'.$prefix.strtolower($stmt->name->name)]);
            }
            if ($stmt instanceof Stmt\Function_) {
                return !isset($wordpress['function:'.$prefix.strtolower($stmt->name->name)]);
            }
            if (null !== ($constant = definedConstantName($stmt))) {
                return !isset($wordpress['constant:'.$constant]);
            }
            return true;
        }));
        if ($kept) {
            $pruned[] = new Stmt\Namespace_($ns ? new Name($ns) : null, $kept);
        }
    }
    return $pruned;
}

/**
 * Reorders the define() statements so the file can actually be executed.
 *
 * The generator buckets global defines after namespaced ones, so a namespaced
 * `define('B', A . '/src')` can run before the `define('A', …)` it reads
 * (ProfilePress does exactly this). All define()d names are global regardless
 * of the surrounding namespace, so the defines are hoisted into one leading
 * global block, topologically sorted among themselves. A define whose value
 * calls a function the stub itself declares is instead deferred to the end of
 * the file (the function must be declared first) along with anything that
 * depends on it.
 *
 * @param Node[] $stmts
 * @return Stmt\Namespace_[]
 */
function sortDefines(array $stmts): array
{
    $groups = groupByNamespace($stmts);
    $defines = []; // name => value expression
    $functions = []; // lc function basename => true
    foreach ($groups as [$ns, $nsStmts]) {
        foreach ($nsStmts as $stmt) {
            if (null !== ($constant = definedConstantName($stmt))) {
                $defines[$constant] = $stmt;
            } elseif ($stmt instanceof Stmt\Function_) {
                $functions[strtolower($stmt->name->name)] = true;
            }
        }
    }
    if (!$defines) {
        return array_map(fn ($group) => new Stmt\Namespace_($group[0] ? new Name($group[0]) : null, $group[1]), $groups);
    }
    $finder = new PhpParser\NodeFinder();
    $dependencies = [];
    $deferred = [];
    foreach ($defines as $constant => $stmt) {
        $value = $stmt->expr->args[1]->value ?? null;
        $dependencies[$constant] = [];
        if (!$value instanceof Node) {
            continue;
        }
        foreach ($finder->findInstanceOf([$value], PhpParser\Node\Expr\ConstFetch::class) as $fetch) {
            $name = $fetch->name->getLast();
            if (isset($defines[$name])) {
                $dependencies[$constant][] = $name;
            }
        }
        foreach ($finder->findInstanceOf([$value], FuncCall::class) as $call) {
            if ($call->name instanceof Name && isset($functions[strtolower($call->name->getLast())])) {
                $deferred[$constant] = true;
            }
        }
    }
    // A define depending on a deferred define is deferred too.
    do {
        $changed = false;
        foreach ($dependencies as $constant => $needs) {
            if (!isset($deferred[$constant]) && array_intersect_key(array_flip($needs), $deferred)) {
                $deferred[$constant] = true;
                $changed = true;
            }
        }
    } while ($changed);
    // Stable topological sort (dependencies first; cycles fall back to input order).
    $sorted = [];
    $visiting = [];
    $visit = function (string $constant) use (&$visit, &$sorted, &$visiting, $dependencies) {
        if (isset($sorted[$constant]) || isset($visiting[$constant])) {
            return; // done, or a cycle — fall back to input order
        }
        $visiting[$constant] = true;
        foreach ($dependencies[$constant] as $need) {
            $visit($need);
        }
        unset($visiting[$constant]);
        $sorted[$constant] = true;
    };
    foreach (array_keys($defines) as $constant) {
        $visit($constant);
    }
    $order = array_keys($sorted);
    $head = [];
    $tail = [];
    foreach ($order as $constant) {
        if (isset($deferred[$constant])) {
            $tail[] = $defines[$constant];
        } else {
            $head[] = $defines[$constant];
        }
    }
    $result = [];
    if ($head) {
        $result[] = new Stmt\Namespace_(null, $head);
    }
    foreach ($groups as [$ns, $nsStmts]) {
        $kept = array_values(array_filter($nsStmts, fn ($stmt) => null === definedConstantName($stmt)));
        if ($kept) {
            $result[] = new Stmt\Namespace_($ns ? new Name($ns) : null, $kept);
        }
    }
    if ($tail) {
        $result[] = new Stmt\Namespace_(null, $tail);
    }
    return $result;
}

/**
 * Removes every class-like whose parents (extends/implements/use) cannot exist
 * at stub load time — declaring one is an immediate "class not found" fatal.
 * SureCart ships an AffiliateWP integration whose parent class only exists on
 * sites running AffiliateWP; upstream never loads the file without it, but a
 * stub is loaded unconditionally.
 *
 * A parent is resolvable if it is (a) in this stub, (b) in WordPress core
 * (wordpress-stubs is the authority), (c) a PHP/extension built-in, or (d) in
 * one of the OTHER stub files, which load alongside this one. Removals cascade
 * to children.
 *
 * @param Node[] $stmts
 * @return Stmt\Namespace_[]
 */
function pruneDanglingClassLikes(array $stmts, string $slug): array
{
    static $wordpress = null;
    if (null === $wordpress) {
        $wordpress = array_flip(declaredSymbols(REPO_ROOT.'/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php'));
    }
    $external = null; // other stubs' symbols, loaded lazily per entry: a stub
    // regenerated earlier in this run must be re-read, not remembered
    $otherStubs = array_filter(
        glob(STUBS_DIR.'/*.php') ?: [],
        fn ($file) => basename($file) !== "{$slug}.php"
    );
    $groups = groupByNamespace($stmts);
    $own = [];
    foreach ($groups as [$ns, $nsStmts]) {
        foreach ($nsStmts as $stmt) {
            if ($stmt instanceof Stmt\ClassLike && $stmt->name) {
                $own[strtolower(($ns ? $ns.'\\' : '').$stmt->name->name)] = true;
            }
        }
    }
    $resolvable = function (string $dependency) use ($wordpress, &$external, $otherStubs, &$own): bool {
        if (isset($own[$dependency]) || isset($wordpress['class:'.$dependency])) {
            return true;
        }
        if (class_exists($dependency, false) || interface_exists($dependency, false) || trait_exists($dependency, false)) {
            return true;
        }
        if (null === $external) {
            $external = [];
            foreach ($otherStubs as $file) {
                $external += array_flip(declaredSymbols($file));
            }
        }
        return isset($external['class:'.$dependency]);
    };
    $dependenciesOf = function (Stmt\ClassLike $node): array {
        $names = [];
        if ($node instanceof Stmt\Class_) {
            if ($node->extends) {
                $names[] = $node->extends;
            }
            $names = array_merge($names, $node->implements);
        } elseif ($node instanceof Stmt\Interface_) {
            $names = $node->extends;
        }
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Stmt\TraitUse) {
                $names = array_merge($names, $stmt->traits);
            }
        }
        return array_map(fn (Name $name) => strtolower(ltrim($name->toString(), '\\')), $names);
    };
    do {
        $changed = false;
        foreach ($groups as $g => [$ns, $nsStmts]) {
            foreach ($nsStmts as $s => $stmt) {
                if (!$stmt instanceof Stmt\ClassLike || !$stmt->name) {
                    continue;
                }
                foreach ($dependenciesOf($stmt) as $dependency) {
                    if (!$resolvable($dependency)) {
                        $fqn = strtolower(($ns ? $ns.'\\' : '').$stmt->name->name);
                        unset($own[$fqn], $groups[$g][1][$s]);
                        $changed = true;
                        break;
                    }
                }
            }
        }
    } while ($changed);
    $pruned = [];
    foreach ($groups as [$ns, $nsStmts]) {
        if ($nsStmts) {
            $pruned[] = new Stmt\Namespace_($ns ? new Name($ns) : null, array_values($nsStmts));
        }
    }
    return $pruned;
}

/**
 * Adds #[\ReturnTypeWillChange] to every untyped method that overrides a PHP
 * internal method with a tentative return type (ArrayAccess::offsetGet and
 * friends). Upstream code that omits both the return type and the attribute
 * triggers a deprecation at class-declaration time — on every suite boot,
 * since the stubs load unconditionally. The attribute is exactly what PHP
 * tells such code to ship, and it is inert on a method that needs no
 * suppression.
 *
 * @param Node[] $stmts
 * @return Stmt\Namespace_[]
 */
function addReturnTypeWillChange(array $stmts): array
{
    $tentative = [ // lc method name => the internal interface that types it
        'offsetexists' => 'ArrayAccess',
        'offsetget' => 'ArrayAccess',
        'offsetset' => 'ArrayAccess',
        'offsetunset' => 'ArrayAccess',
        'count' => 'Countable',
        'current' => 'Iterator',
        'key' => 'Iterator',
        'next' => 'Iterator',
        'rewind' => 'Iterator',
        'valid' => 'Iterator',
        'getiterator' => 'IteratorAggregate',
        'jsonserialize' => 'JsonSerializable',
        'seek' => 'SeekableIterator',
    ];
    $groups = groupByNamespace($stmts);
    $edges = []; // lc fqn => lc ancestor fqns (extends + implements)
    foreach ($groups as [$ns, $nsStmts]) {
        foreach ($nsStmts as $stmt) {
            if (!$stmt instanceof Stmt\ClassLike || !$stmt->name) {
                continue;
            }
            $names = [];
            if ($stmt instanceof Stmt\Class_) {
                $names = array_merge($stmt->extends ? [$stmt->extends] : [], $stmt->implements);
            } elseif ($stmt instanceof Stmt\Interface_) {
                $names = $stmt->extends;
            } elseif ($stmt instanceof Stmt\Enum_) {
                $names = $stmt->implements;
            }
            $edges[strtolower(($ns ? $ns.'\\' : '').$stmt->name->name)] =
                array_map(fn (Name $name) => strtolower(ltrim($name->toString(), '\\')), $names);
        }
    }
    $memo = [];
    $reaches = function (string $name, string $interface) use (&$reaches, &$memo, $edges): bool {
        $key = "{$name}>{$interface}";
        if (isset($memo[$key])) {
            return $memo[$key];
        }
        $memo[$key] = false; // also the cycle guard: a cycle proves nothing
        if ($name === strtolower($interface)) {
            return $memo[$key] = true;
        }
        if (!isset($edges[$name])) {
            // Outside this stub: only a loaded built-in can answer. A class
            // in ANOTHER stub file cannot, so an ancestry that crosses stubs
            // is invisible here — acceptable, as the declaring stub gets the
            // attribute where the interface actually enters the chain.
            return $memo[$key] = (class_exists($name, false) || interface_exists($name, false))
                && is_a($name, $interface, true);
        }
        foreach ($edges[$name] as $ancestor) {
            if ($reaches($ancestor, $interface)) {
                return $memo[$key] = true;
            }
        }
        return false;
    };
    foreach ($groups as [$ns, $nsStmts]) {
        foreach ($nsStmts as $stmt) {
            if (!$stmt instanceof Stmt\ClassLike || !$stmt->name) {
                continue;
            }
            $fqn = strtolower(($ns ? $ns.'\\' : '').$stmt->name->name);
            foreach ($stmt->getMethods() as $method) {
                $name = $method->name->toLowerString();
                if (null !== $method->returnType || !isset($tentative[$name])) {
                    continue;
                }
                foreach ($method->attrGroups as $attrGroup) {
                    foreach ($attrGroup->attrs as $attr) {
                        if ('returntypewillchange' === strtolower(ltrim($attr->name->toString(), '\\'))) {
                            continue 3; // upstream already ships it
                        }
                    }
                }
                if ($reaches($fqn, $tentative[$name])) {
                    $method->attrGroups[] = new Node\AttributeGroup([
                        new Node\Attribute(new Name\FullyQualified('ReturnTypeWillChange')),
                    ]);
                }
            }
        }
    }
    return array_map(fn ($group) => new Stmt\Namespace_($group[0] ? new Name($group[0]) : null, $group[1]), $groups);
}

function definedConstantName(Stmt $stmt): ?string
{
    if ($stmt instanceof Stmt\Expression
        && $stmt->expr instanceof FuncCall
        && $stmt->expr->name instanceof Name
        && 'define' === strtolower(ltrim($stmt->expr->name->toString(), '\\'))
        && isset($stmt->expr->args[0])
        && $stmt->expr->args[0] instanceof Node\Arg
        && $stmt->expr->args[0]->value instanceof String_) {
        return $stmt->expr->args[0]->value->value;
    }
    return null;
}

/**
 * Keeps only the allowlisted symbols, plus every parent class, interface and
 * trait a kept class-like needs in order to be declarable at load time.
 *
 * @param Node[] $stmts
 * @param string[] $allowlist
 * @return array{stmts: Stmt\Namespace_[], missing: string[]}
 */
function filterSymbols(array $stmts, array $allowlist): array
{
    $allow = [];
    foreach ($allowlist as $symbol) {
        $allow[strtolower(ltrim($symbol, '\\'))] = $symbol;
    }
    $groups = groupByNamespace($stmts);

    // Index the class-likes so the dependency closure can find them.
    $classLikes = []; // lc fqn => [groupIndex, stmtIndex]
    foreach ($groups as $g => [$ns, $nsStmts]) {
        foreach ($nsStmts as $s => $stmt) {
            if ($stmt instanceof Stmt\ClassLike && $stmt->name) {
                $classLikes[strtolower(($ns ? $ns.'\\' : '').$stmt->name->name)] = [$g, $s];
            }
        }
    }

    $dependenciesOf = function (Stmt\ClassLike $node): array {
        $names = [];
        if ($node instanceof Stmt\Class_) {
            if ($node->extends) {
                $names[] = $node->extends;
            }
            $names = array_merge($names, $node->implements);
        } elseif ($node instanceof Stmt\Interface_) {
            $names = $node->extends;
        }
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Trait_ || $node instanceof Stmt\Enum_) {
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Stmt\TraitUse) {
                    $names = array_merge($names, $stmt->traits);
                }
            }
        }
        return array_map(fn (Name $name) => strtolower(ltrim($name->toString(), '\\')), $names);
    };

    $matched = []; // allowlist keys that were found
    $keep = []; // "g:s" => true
    $queue = [];
    foreach ($groups as $g => [$ns, $nsStmts]) {
        foreach ($nsStmts as $s => $stmt) {
            $fqn = null;
            if (($stmt instanceof Stmt\ClassLike || $stmt instanceof Stmt\Function_) && $stmt->name) {
                $fqn = ($ns ? $ns.'\\' : '').$stmt->name->name;
            } elseif (null !== ($constant = definedConstantName($stmt))) {
                $fqn = $constant;
            } elseif ($stmt instanceof Stmt\Const_) {
                foreach ($stmt->consts as $const) {
                    $fqn = ($ns ? $ns.'\\' : '').$const->name->name;
                    break;
                }
            }
            if (null !== $fqn && isset($allow[strtolower($fqn)])) {
                $matched[strtolower($fqn)] = true;
                $keep["{$g}:{$s}"] = true;
                if ($stmt instanceof Stmt\ClassLike) {
                    $queue[] = [$g, $s];
                }
            }
        }
    }
    while ($queue) {
        [$g, $s] = array_pop($queue);
        $node = $groups[$g][1][$s];
        foreach ($dependenciesOf($node) as $dependency) {
            if (isset($classLikes[$dependency])) {
                [$dg, $ds] = $classLikes[$dependency];
                if (!isset($keep["{$dg}:{$ds}"])) {
                    $keep["{$dg}:{$ds}"] = true;
                    $queue[] = [$dg, $ds];
                }
            }
        }
    }

    $filtered = [];
    foreach ($groups as $g => [$ns, $nsStmts]) {
        $kept = [];
        foreach ($nsStmts as $s => $stmt) {
            if (isset($keep["{$g}:{$s}"])) {
                $kept[] = $stmt;
            }
        }
        if ($kept) {
            $filtered[] = new Stmt\Namespace_($ns ? new Name($ns) : null, $kept);
        }
    }
    $missing = [];
    foreach ($allow as $key => $original) {
        if (!isset($matched[$key])) {
            $missing[] = $original;
        }
    }
    return ['stmts' => $filtered, 'missing' => $missing];
}

// ---------------------------------------------------------------------------

if ($list) {
    foreach ($manifest as $slug => $entry) {
        $notes = [];
        foreach (sourcesOf($entry) as $source) {
            $type = sourceType($source);
            if ('zip' === $type && !is_file(REPO_ROOT.'/'.$source)) {
                $notes[] = "{$source} (MISSING — entry is skipped)";
            } else {
                $notes[] = $source;
            }
        }
        $mode = isset($entry['symbols']) ? sprintf('%d symbols', count($entry['symbols'])) : 'everything';
        printf("%-20s %-10s %s\n", $slug, $mode, implode(' + ', $notes));
    }
    exit(0);
}

$workdir = sys_get_temp_dir().'/site-reviews-stubs-'.getmypid();
mkdir($workdir, 0777, true);
register_shutdown_function(function () use ($workdir) {
    exec('rm -rf '.escapeshellarg($workdir));
});

$slugs = $requested ?: array_keys($manifest);
$failures = [];
$skipped = [];
$written = [];

foreach ($slugs as $slug) {
    $entry = $manifest[$slug];
    echo "[{$slug}]\n";
    $dirs = [];
    $headerSources = [];
    $skip = false;
    foreach (array_values(sourcesOf($entry)) as $index => $source) {
        $resolved = resolveSource($source, $slug, $workdir, $index);
        if (is_string($resolved)) {
            if (str_starts_with($resolved, 'skip:') && !in_array($slug, $requested, true)) {
                echo '  '.substr($resolved, 6)." — skipped\n";
                $skipped[] = $slug;
            } else {
                fwrite(STDERR, "  ".preg_replace('/^(skip|error): /', '', $resolved)."\n");
                $failures[] = $slug;
            }
            $skip = true;
            break;
        }
        $dirs[] = $resolved['dir'];
        $headerSources[] = ['label' => $resolved['label'], 'version' => detectVersion($resolved['dir'])];
    }
    if ($skip) {
        continue;
    }
    if (!$dirs) {
        fwrite(STDERR, "  no source configured\n");
        $failures[] = $slug;
        continue;
    }

    $excludes = $entry['exclude'] ?? DEFAULT_EXCLUDES;
    $excludedFiles = array_filter($excludes, fn ($exclude) => str_ends_with($exclude, '.php'));
    $finder = new Finder();
    $finder->in($dirs)->exclude(array_diff($excludes, $excludedFiles))->sortByName();
    foreach ($excludedFiles as $file) {
        $finder->notPath('{^'.preg_quote($file, '{}').'$}');
    }
    $generator = new StubsGenerator(
        StubsGenerator::FUNCTIONS
        | StubsGenerator::CLASSES
        | StubsGenerator::INTERFACES
        | StubsGenerator::TRAITS
        | StubsGenerator::ENUMS
        | StubsGenerator::CONSTANTS,
        // Without this the generator drops protected members of final classes,
        // leaving abstract parent methods unimplemented — a stub that cannot
        // even be linted (Elementor's prefixed Twig is the proof).
        ['include_inaccessible_class_nodes' => true]
    );
    $result = $generator->generate($finder);
    $stmts = pruneWordPressSymbols(pruneWpCliCommands($result->getStubStmts()));
    $stmts = pruneDanglingClassLikes($stmts, $slug);
    $stmts = addReturnTypeWillChange($stmts);

    if (isset($entry['symbols'])) {
        ['stmts' => $stmts, 'missing' => $missing] = filterSymbols($stmts, $entry['symbols']);
        foreach ($missing as $symbol) {
            fwrite(STDERR, "  warning: symbol not found in source: {$symbol}\n");
        }
    }
    $stmts = sortDefines($stmts);
    if (!$stmts) {
        fwrite(STDERR, "  produced no symbols — refusing to write an empty stub\n");
        $failures[] = $slug;
        continue;
    }

    $versions = implode(' + ', array_column($headerSources, 'version'));
    $header = "/**\n * {$slug} {$versions}\n"
        ." * Generated by tests/bin/generate-stubs.php — do not edit, rerun `make stubs`.\n"
        .implode('', array_map(
            fn ($source) => " * Source: {$source['label']}".(count($headerSources) > 1 ? " ({$source['version']})" : '')."\n",
            $headerSources
        ))
        .' */';
    $code = (new Standard())->prettyPrintFile($stmts);
    $code = preg_replace('/^<\?php/', "<?php\n\n{$header}", $code, 1)."\n";
    if (isset($entry['append'])) {
        $fragment = REPO_ROOT.'/'.$entry['append'];
        if (!is_file($fragment)) {
            fwrite(STDERR, "  append fragment not found: {$entry['append']}\n");
            $failures[] = $slug;
            continue;
        }
        $code .= preg_replace('/^<\?php\s*/', '', (string) file_get_contents($fragment));
    }

    $outfile = STUBS_DIR."/{$slug}.php";
    $changed = !is_file($outfile) || md5_file($outfile) !== md5($code);
    file_put_contents($outfile, $code);
    $written[] = $slug;
    printf("  wrote %s (%s lines, %s)\n", "tests/stubs/{$slug}.php", number_format(substr_count($code, "\n")), $changed ? 'CHANGED' : 'unchanged');
}

// Cross-stub duplicate scan: the suite loads these files together, so the same
// symbol in two files is a fatal redeclaration. (action-scheduler.php is only
// loaded by phpstan, but phpstan loads every stub at once, so it plays too.)
$declarations = [];
foreach (glob(STUBS_DIR.'/*.php') as $file) {
    foreach (declaredSymbols($file) as $symbol) {
        $declarations[$symbol][] = basename($file);
    }
}
$duplicates = array_filter($declarations, fn ($files) => count($files) > 1);
if ($duplicates) {
    fwrite(STDERR, "\nwarning: symbols declared in more than one stub (fatal when loaded together):\n");
    foreach ($duplicates as $symbol => $files) {
        fwrite(STDERR, "  {$symbol}  in  ".implode(', ', $files)."\n");
    }
}

printf(
    "\ndone: %d written, %d skipped, %d failed\n",
    count($written),
    count($skipped),
    count($failures)
);
if ($skipped) {
    echo 'skipped (local zip absent): '.implode(', ', $skipped)."\n";
}
if ($written) {
    echo "Now run: make analyse && make test — ActiveIntegrationsTest asserts which\nintegrations the stubs wake, so a regenerated stub may legitimately change it.\n";
}
exit($failures ? 1 : 0);
