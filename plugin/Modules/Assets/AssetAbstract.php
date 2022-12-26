<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AssetAbstract
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

    /**
     * @return bool
     */
    public function canOptimize()
    {
        if ($this->abort) {
            return false;
        }
        if ($this->isOptimizationDisabled()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isOptimizationDisabled()
    {
        return !$this->isOptimizationEnabled();
    }

    /**
     * @return bool
     */
    public function isOptimizationEnabled()
    {
        return glsr()->filterBool('optimize/'.$this->type(), false);
    }

    /**
     * @return bool
     */
    public function isOptimized()
    {
        $hash = $this->hash();
        $path = (string) $this->file('path');
        if (file_exists($path) && $hash === get_transient($this->transient())) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    public function optimize()
    {
        if (!$this->canOptimize() || $this->isOptimized()) {
            return;
        }
        $file = $this->file();
        if (!$file) {
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

    /**
     * @return string
     */
    public function url()
    {
        if ($this->canOptimize() && $this->isOptimized()) {
            return (string) $this->file('url');
        }
        return $this->originalUrl();
    }

    /**
     * @return string
     */
    public function version()
    {
        if ($this->canOptimize() && $this->isOptimized()) {
            return $this->hash();
        }
        return glsr()->version;
    }

    /**
     * @return void
     */
    protected function combine()
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

    /**
     * @param string $url
     * @param string $hash
     */
    abstract protected function enqueue($url, $hash);

    /**
     * @return array|string|false
     */
    protected function file($key = '')
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
        if (wp_mkdir_p($basedir)) {
            $file = [
                'path' => sprintf('%s/%s.%s', $basedir, glsr()->id, $this->type()),
                'url' => sprintf('%s/%s.%s', $baseurl, glsr()->id, $this->type()),
            ];
            return Arr::get($file, $key, $file);
        }
        glsr_log()->error('Unable to store optimized assets in the uploads directory.');
        return false;
    }

    /**
     * @return \WP_Filesystem_Base
     */
    protected function filesystem()
    {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH.'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        return $wp_filesystem;
    }

    /**
     * @return string
     */
    public function hash()
    {
        return md5(serialize($this->versions()));
    }

    /**
     * @return string
     */
    abstract protected function originalUrl();

    /**
     * @return void
     */
    protected function prepare()
    {
        $removedDeps = array_diff($this->handles, [glsr()->id]);
        $this->sources = array_fill_keys($this->handles, ''); // ensure correct order!
        foreach ($this->registered() as $handle => $dependency) {
            if (Str::startsWith($handle, glsr()->id)) {
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

    /**
     * @return array
     */
    abstract protected function registered();

    /**
     * @return void
     */
    protected function reset()
    {
        $this->abort = false;
        $this->after = [];
        $this->before = [];
        $this->contents = '';
        $this->dependencies = [];
        $this->handles = [];
        $this->sources = [];
    }

    /**
     * @param string $filepath
     * @return bool
     */
    protected function store($filepath)
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

    /**
     * @return string
     */
    protected function transient()
    {
        return glsr()->prefix.'optimized_'.$this->type();
    }

    /**
     * @return string
     */
    abstract protected function type();

    /**
     * @return array
     */
    protected function versions()
    {
        $versions = glsr()->retrieveAs('array', 'addons');
        $versions = Arr::prepend($versions, glsr()->version, glsr()->id);
        return $versions;
    }
}
