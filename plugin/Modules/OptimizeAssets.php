<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helpers\Arr;

class OptimizeAssets
{
    public $abort;
    public $after;
    public $before;
    public $contents;
    public $dependencies;
    public $handles;
    public $sources;
    public $versions;

    public function __construct()
    {
        $this->reset();
        if ('1' === filter_input(INPUT_GET, 'nocache')) {
            $this->abort = true;
        }
    }

    public function optimizeCss(array $handles = [])
    {
        if (!glsr()->filterBool('optimize/css', false) || $this->abort) {
            return;
        }
        $this->handles = $handles;
        $this->prepare(wp_styles()->registered);
        $this->combine();
        if ($url = $this->store(glsr()->id.'.css')) {
            $this->enqueueCss($url);
        }
        $this->reset();
    }

    public function optimizeJs(array $handles = [])
    {
        if (!glsr()->filterBool('optimize/js', false) || $this->abort) {
            return;
        }
        $this->handles = $handles;
        $this->prepare(wp_scripts()->registered);
        $this->combine();
        if ($url = $this->store(glsr()->id.'.js')) {
            $this->enqueueJs($url);
        }
        $this->reset();
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
     */
    protected function enqueueCss($url)
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
        wp_enqueue_style(glsr()->id, $url, $this->dependencies, $this->hash());
        if (!empty($this->after)) {
            $styles = array_reduce($this->after, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_style(glsr()->id, $styles);
        }
    }

    /**
     * @param string $url
     */
    protected function enqueueJs($url)
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }
        wp_enqueue_script(glsr()->id, $url, $this->dependencies, $this->hash(), true);
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
        return md5(serialize($this->versions));
    }

    protected function prepare(array $registered)
    {
        foreach ($this->handles as $handle) {
            if (!array_key_exists($handle, $registered)) {
                continue;
            }
            $dependency = $registered[$handle];
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
            if (!empty($dependency->ver)) {
                $filename = wp_basename($dependency->src); // @phpstan-ignore-line
                $this->versions[] = [$filename, $dependency->ver];
            }
            $this->sources[] = $dependency->src;
        }
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
        $this->versions = [];
    }

    /**
     * @param string $file
     * @return string|false
     */
    protected function store($file)
    {
        if ($this->abort) {
            return false;
        }
        require_once ABSPATH.WPINC.'/pluggable.php';
        $uploads = wp_upload_dir();
        if (!file_exists($uploads['basedir'])) {
            $uploads = wp_upload_dir(null, true, true); // maybe the site has been moved, so refresh the cached uploads path
        }
        $basedir = sprintf('%s/%s/assets', $uploads['basedir'], glsr()->id);
        $baseurl = sprintf('%s/%s/assets', $uploads['baseurl'], glsr()->id);
        if (wp_mkdir_p($basedir)) {
            $this->filesystem()->put_contents($basedir.'/'.$file, $this->contents);
            return $baseurl.'/'.$file;
        }
        return false;
    }
}
