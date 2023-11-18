<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Helpers\Arr;

abstract class AbstractAsset
{
    public $abort;
    public $after;
    public $before;
    public $contents;
    public $dependencies;
    public $handles;
    public $sources;

    public function __construct()
    {
        $this->reset();
        if ('1' === filter_input(INPUT_GET, 'nocache') || $this->isOptimizationDisabled()) {
            $this->abort = true;
            delete_transient($this->transient());
        }
    }

    public function canOptimize(): bool
    {
        if ($this->abort) {
            return false;
        }
        if ($this->isOptimizationDisabled()) {
            return false;
        }
        return true;
    }

    public function isOptimizationDisabled(): bool
    {
        return !$this->isOptimizationEnabled();
    }

    public function isOptimizationEnabled(): bool
    {
        $assetType = $this->type();
        return glsr()->filterBool("optimize/{$assetType}", false);
    }

    public function isOptimized(): bool
    {
        $hash = $this->hash();
        $path = Arr::getAs('string', $this->file(), 'path');
        if (file_exists($path) && $hash === get_transient($this->transient())) {
            return true;
        }
        return false;
    }

    public function optimize(): void
    {
        if (!$this->canOptimize() || $this->isOptimized()) {
            return;
        }
        $file = $this->file();
        if (empty($file)) {
            return;
        }
        $hash = $this->hash();
        $this->handles = array_keys($this->versions());
        $this->prepare();
        if ($hash !== get_transient($this->transient())) {
            $this->combine();
            if ($this->store($file['path'])) {
                set_transient($this->transient(), $hash);
            }
        }
        if (!$this->abort && file_exists($file['path'])) {
            $this->enqueue($file['url'], $hash);
        }
        $this->reset();
    }

    public function url(): string
    {
        if ($this->canOptimize() && $this->isOptimized()) {
            return Arr::getAs('string', $this->file(), 'url');
        }
        return $this->originalUrl();
    }

    public function version(): string
    {
        if ($this->canOptimize() && $this->isOptimized()) {
            return $this->hash();
        }
        return glsr()->version;
    }

    protected function combine(): void
    {
        $pluginDirUrl = plugin_dir_url('');
        $pluginDirPath = substr(glsr()->path(), 0, -1 * strlen(glsr()->id.'/'));
        $sources = array_filter($this->sources);
        foreach ($sources as $url) {
            $path = str_replace($pluginDirUrl, $pluginDirPath, $url);
            if ($path !== $url) {
                $contents = $this->filesystem()->get_contents($path);
            }
            if (empty($contents)) { // @todo if this fails, do the addon assets still load?
                $this->abort = true;
                break;
            }
            $this->contents .= $contents;
        }
    }

    abstract protected function enqueue(string $url, string $hash): void;

    protected function file(): array
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        $uploads = wp_upload_dir();
        if (!file_exists($uploads['basedir'])) {
            $uploads = wp_upload_dir(null, true, true); // maybe the site has been moved, so refresh the cached uploads path
        }
        $basedir = sprintf('%s/%s/assets', $uploads['basedir'], glsr()->id);
        $baseurl = sprintf('%s/%s/assets', $uploads['baseurl'], glsr()->id);
        if (is_ssl()) { // fix SSL just in case...
            $baseurl = str_replace('http://', 'https://', $baseurl);
        }
        if (!wp_mkdir_p($basedir)) {
            glsr_log()->error('Unable to store optimized assets in the uploads directory.');
            return [];
        }
        return [
            'path' => sprintf('%s/%s.%s', $basedir, glsr()->id, $this->type()),
            'url' => sprintf('%s/%s.%s', $baseurl, glsr()->id, $this->type()),
        ];
    }

    protected function filesystem(): \WP_Filesystem_Base
    {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH.'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        return $wp_filesystem;
    }

    public function hash(): string
    {
        return md5(serialize($this->versions()));
    }

    abstract protected function originalUrl(): string;

    protected function prepare(): void
    {
        $removedDeps = array_diff($this->handles, [glsr()->id]);
        $this->sources = array_fill_keys($this->handles, ''); // ensure correct order!
        foreach ($this->registered() as $handle => $dependency) {
            if (str_starts_with($handle, glsr()->id)) {
                $dependency->deps = array_diff($dependency->deps, $removedDeps);
            }
            if (!in_array($handle, $this->handles)) {
                continue;
            }
            if (!empty($dependency->extra['after'])) {
                $this->after = array_merge($this->after, $dependency->extra['after']);
                $this->after = Arr::reindex(Arr::unique($this->after));
            }
            if (!empty($dependency->extra['before'])) {
                $this->before = array_merge($this->before, $dependency->extra['before']);
                $this->before = Arr::reindex(Arr::unique($this->before));
            }
            if (!empty($dependency->deps)) {
                $this->dependencies = array_merge($this->dependencies, $dependency->deps);
                $this->dependencies = array_diff($dependency->deps, [glsr()->id]);
            }
            $this->sources[$handle] = $dependency->src;
        }
    }

    abstract protected function registered(): array;

    protected function reset(): void
    {
        $this->abort = false;
        $this->after = [];
        $this->before = [];
        $this->contents = '';
        $this->dependencies = [];
        $this->handles = [];
        $this->sources = [];
    }

    protected function store(string $filepath): bool
    {
        if ($this->abort) {
            return false;
        }
        if ($this->filesystem()->put_contents($filepath, $this->contents)) {
            return true;
        }
        glsr_log()->error('Unable to write content to optimized assets.');
        return false;
    }

    protected function transient(): string
    {
        $assetType = $this->type();
        return glsr()->prefix."optimized_{$assetType}";
    }

    abstract protected function type(): string;

    protected function versions(): array
    {
        $versions = glsr()->retrieveAs('array', 'addons');
        $versions = Arr::prepend($versions, glsr()->version, glsr()->id);
        return $versions;
    }
}
