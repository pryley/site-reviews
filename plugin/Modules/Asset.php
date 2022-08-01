<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Asset
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
        if ('1' === filter_input(INPUT_GET, 'nocache')) {
            $this->abort = true;
            delete_transient(glsr()->prefix.'optimized_css');
            delete_transient(glsr()->prefix.'optimized_js');
        }
    }

    /**
     * @param string $type
     */
    public function optimize($type)
    {
        if ($this->abort
            || !glsr()->filterBool('optimize/'.$type, false)
            || empty(Str::restrictTo(['css', 'js'], $type))
            || $this->isOptimized($type)) {
            return;
        }
        $file = $this->file($type);
        if (!$file) {
            return;
        }
        $this->handles = array_keys($this->versions());
        $hash = $this->hash();
        $registeredMethod = Helper::buildMethodName($type, 'registered');
        $registered = call_user_func([$this, $registeredMethod]);
        $transient = glsr()->prefix.'optimized_'.$type;
        $this->prepare($registered);
        if ($hash !== get_transient($transient)) {
            $this->combine();
            if ($this->store($file['path'])) {
                set_transient($transient, $hash);
            }
        }
        if (file_exists($file['path'])) {
            $enqueueMethod = Helper::buildMethodName($type, 'enqueue');
            call_user_func([$this, $enqueueMethod], $file['url'], $hash);
        }
        $this->reset();
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isOptimized($type)
    {
        $file = $this->file($type);
        $hash = $this->hash();
        if (file_exists(Arr::get($file, 'path'))
            && $hash === get_transient(glsr()->prefix.'optimized_'.$type)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $type
     * @return string
     */
    public function url($type)
    {
        if ($this->isOptimized($type)) {
            $file = $this->file($type);
            return $file['url'];
        }
        if ('css' === $type) {
            return glsr()->url(sprintf('assets/styles/%s.css', glsr(Style::class)->style));
        }
        if ('js' === $type) {
            return glsr()->url(sprintf('assets/scripts/%s.js', glsr()->id));
        }
        return '';
    }

    protected function combine()
    {
        $pluginDirUrl = plugin_dir_url('');
        $pluginDirPath = substr(glsr()->path(), 0, -1 * strlen(glsr()->id.'/'));
        foreach ($this->sources as $url) {
            $path = str_replace($pluginDirUrl, $pluginDirPath, $url);
            if ($path !== $url) {
                $contents = $this->filesystem()->get_contents($path);
            }
            if (empty($contents)) {
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
    protected function enqueueCss($url, $hash)
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
        wp_enqueue_style(glsr()->id, $url, $this->dependencies, $hash);
        if (!empty($this->after)) {
            $styles = array_reduce($this->after, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_style(glsr()->id, $styles);
        }
    }

    /**
     * @param string $url
     * @param string $hash
     */
    protected function enqueueJs($url, $hash)
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }
        wp_enqueue_script(glsr()->id, $url, $this->dependencies, $hash, true);
        if (!empty($this->after)) {
            $script = array_reduce($this->after, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_script(glsr()->id, $script);
        }
        if (!empty($this->before)) {
            $script = array_reduce($this->before, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_script(glsr()->id, $script, 'before');
        }
    }

    /**
     * @param string $type
     * @return array|false
     */
    protected function file($type)
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
            return [
                'path' => sprintf('%s/%s.%s', $basedir, glsr()->id, $type),
                'url' => sprintf('%s/%s.%s', $baseurl, glsr()->id, $type),
            ];
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
    protected function hash()
    {
        return md5(serialize($this->versions()));
    }

    protected function prepare(array $registered)
    {
        $removedDeps = array_diff($this->handles, [glsr()->id]);
        $this->sources = array_fill_keys($this->handles, ''); // ensure correct order!
        foreach ($registered as $handle => $dependency) {
            if (Str::startsWith(glsr()->id, $handle)) {
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
    protected function registeredCss()
    {
        return wp_styles()->registered;
    }

    /**
     * @return array
     */
    protected function registeredJs()
    {
        return wp_scripts()->registered;
    }

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
     * @param string $filePath
     * @return bool
     */
    protected function store($filePath)
    {
        if ($this->abort) {
            return false;
        }
        if ($this->filesystem()->put_contents($filePath, $this->contents)) {
            return true;
        }
        glsr_log()->error('Unable to write content to optimized assets.');
        return false;
    }

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
