<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * The addons registered with the plugin, and how one gets in.
 */
class Registry
{
    /** @var array<string, string> addon id => addon class */
    protected array $addons = [];
    /** @var array<array{addon: string, host: ?PluginContract}> */
    protected array $queued = [];

    /**
     * @return array<string, string> addon id => addon class
     */
    public function all(): array
    {
        return $this->addons;
    }

    /**
     * The class registered under an addon id, or null.
     *
     * @return class-string|null
     */
    public function get(string $addonId): ?string
    {
        return $this->addons[$addonId] ?? null;
    }

    /**
     * Register an addon, or queue it while "site-reviews/premium/register" runs.
     * MainController::registerAddons() fires that action on "plugins_loaded:-50" and then calls registerQueued().
     */
    public function register(string $addon, ?PluginContract $host = null): void
    {
        if (doing_action(glsr()->id.'/premium/register')) {
            $this->queued[] = compact('addon', 'host');
            return;
        }
        $entry = $this->inspect($addon, $host);
        if (empty($entry)) {
            return;
        }
        if ($this->isReplaced($entry)) {
            $this->shelve($entry);
            return;
        }
        $this->recordReplacements($entry);
        $this->start($entry);
    }

    /**
     * Register the addons queued while "site-reviews/premium/register" ran.
     * Every REPLACES list is read before an addon starts, so the order plugins hooked the action never decides who runs.
     */
    public function registerQueued(): void
    {
        $entries = [];
        foreach ($this->queued as $queued) {
            $entry = $this->inspect($queued['addon'], $queued['host']);
            if (!empty($entry)) {
                $entries[] = $entry;
            }
        }
        $this->queued = [];
        foreach ($this->resolveQueue($entries) as $entry) {
            $this->start($entry);
        }
    }

    /**
     * The registration record of an addon, or an empty array when its class or its plugin file is missing.
     *
     * @return array{}|array{
     *     addon: string,
     *     host: ?PluginContract,
     *     id: string,
     *     licensed: bool,
     *     replaces: string[], // the ids of the addons it replaces
     *     supported: bool, // if this version of Site Reviews is within the addon's GLSR headers
     * }
     */
    protected function inspect(string $addon, ?PluginContract $host): array
    {
        try {
            $reflection = new \ReflectionClass($addon); // make sure that the class exists
        } catch (\ReflectionException $e) {
            glsr_log()->error("Attempted to register an invalid addon [$addon]");
            return [];
        }
        $addonId = $reflection->getConstant('ID');
        $file = dirname(dirname($reflection->getFileName()));
        $file = trailingslashit($file).$addonId.'.php';
        if (!file_exists($file)) {
            if (!$host instanceof PluginContract) {
                glsr_log()->error("Attempted to register an invalid addon [$addonId].");
                return [];
            }
            $file = $host->file; // hosted addon: the host's headers gate the whole bundle
        }
        $pluginData = get_file_data($file, [
            'glsr_version_required' => 'GLSR requires at least',
            'glsr_version_unsupported' => 'GLSR unsupported version',
        ], 'plugin');
        return [
            'addon' => $addon,
            'host' => $host,
            'id' => $addonId,
            'licensed' => true === $reflection->getConstant('LICENSED'),
            'replaces' => Arr::consolidate($reflection->getConstant('REPLACES')),
            'supported' => version_compare(glsr()->version, $pluginData['glsr_version_required'], '>=')
                && version_compare(glsr()->version, $pluginData['glsr_version_unsupported'], '<'),
        ];
    }

    /**
     * @param array{host: ?PluginContract} $entry
     */
    protected function isHostedBy(array $entry, string $addonId): bool
    {
        return $entry['host'] instanceof PluginContract && $entry['host']->id === $addonId;
    }

    /**
     * Tell whether another queued addon that could start names this one on its REPLACES list.
     *
     * @param array{host: ?PluginContract, id: string} $entry
     * @param array<int, array{id: string, replaces: string[], supported: bool}> $queued
     */
    protected function isNamedByQueued(int $index, array $entry, array $queued): bool
    {
        foreach ($queued as $otherIndex => $other) {
            if ($otherIndex === $index || !$other['supported'] || $this->isHostedBy($entry, $other['id'])) {
                continue;
            }
            if (in_array($entry['id'], $other['replaces'], true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Tell whether another addon has declared that it replaces this one's id, and does not host it.
     *
     * @param array{host: ?PluginContract, id: string} $entry
     */
    protected function isReplaced(array $entry): bool
    {
        $replacedBy = glsr()->retrieveAs('array', 'replacedBy', [])[$entry['id']] ?? '';
        return '' !== $replacedBy && !$this->isHostedBy($entry, $replacedBy);
    }

    /**
     * Record the addon as the one that replaces each id on its REPLACES list.
     *
     * @param array{id: string, replaces: string[], supported: bool} $entry
     */
    protected function recordReplacements(array $entry): void
    {
        if (!$entry['supported']) {
            return; // an addon that never starts shelves nothing
        }
        $replacedBy = glsr()->retrieveAs('array', 'replacedBy', []);
        foreach ($entry['replaces'] as $addonId) {
            if (!isset($replacedBy[$addonId])) {
                glsr()->append('replacedBy', $entry['id'], $addonId); // the first addon to declare an id replaces it
            }
        }
    }

    /**
     * Record the REPLACES lists of the queued addons that start, shelve the addons they name, and return the rest in queue order.
     * A list counts when its addon starts, so an addon named by a list that counts is shelved and its own list is not read.
     *
     * @param array<int, array{addon: string, host: ?PluginContract, id: string, licensed: bool, replaces: string[], supported: bool}> $entries
     *
     * @return array<int, array{addon: string, host: ?PluginContract, id: string, licensed: bool, replaces: string[], supported: bool}>
     */
    protected function resolveQueue(array $entries): array
    {
        $undecided = $entries;
        $starting = [];
        while (!empty($undecided)) {
            $free = array_filter($undecided, fn ($entry, $index) => !$this->isNamedByQueued($index, $entry, $undecided), \ARRAY_FILTER_USE_BOTH);
            if (empty($free)) {
                $free = array_slice($undecided, 0, 1, true); // two addons replace each other: the first to register wins
            }
            foreach ($free as $index => $entry) {
                $this->recordReplacements($entry);
                $starting[$index] = $entry;
                unset($undecided[$index]);
            }
            foreach ($undecided as $index => $entry) {
                if ($this->isReplaced($entry)) {
                    $this->shelve($entry);
                    unset($undecided[$index]);
                }
            }
        }
        ksort($starting);
        return $starting;
    }

    /**
     * Park a replaced addon on the "replaced" list, which the tests read.
     *
     * @param array{addon: string} $entry
     */
    protected function shelve(array $entry): void
    {
        glsr()->append('replaced', $entry['addon']);
    }

    /**
     * Bind the addon as a singleton, alias it by its id and run its init().
     *
     * @param array{addon: string, host: ?PluginContract, id: string, licensed: bool, supported: bool} $entry
     */
    protected function start(array $entry): void
    {
        if ($entry['licensed']) {
            glsr()->append('licensed', $entry['addon'], $entry['id']); // before the version gate, so an outdated addon's key can still fetch its update
        }
        if (!$entry['supported']) {
            return;
        }
        $addon = $entry['addon'];
        $host = $entry['host'];
        $this->addons[$entry['id']] = $addon;
        glsr()->singleton($addon); // this goes first!
        $parameters = [];
        if ($host instanceof PluginContract) {
            $parameters = ['host' => $host];
            if ($host instanceof Addon) {
                $host->markAsHost();
            }
        }
        $instance = glsr()->make($addon, $parameters);
        glsr()->alias($addon, $instance); // instances built with parameters are not auto-cached
        glsr()->alias($entry['id'], $instance);
        $instance->init();
        glsr()->append('addons', $instance->version, $instance->id);
        glsr()->discard('settings'); // recompose the settings view now that this addon's option is mounted
    }
}
