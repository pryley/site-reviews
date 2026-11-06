<?php

namespace GeminiLabs\SiteReviews\Addons;

class Compat
{
    public function register(string $addon): void
    {
        try {
            $reflection = new \ReflectionClass($addon); // make sure that the class exists
        } catch (\ReflectionException $e) {
            return;
        }
        $addonId = $reflection->getConstant('ID');
        $file = dirname(dirname($reflection->getFileName()));
        $file = trailingslashit($file).$addonId.'.php';
        if (!file_exists($file)) {
            return;
        }
        $pluginData = get_file_data($file, ['update_url' => 'Update URI'], 'plugin');
        if (empty($pluginData['update_url'])) {
            glsr()->append('compat', $file, $addonId); // this addon needs updating in compatibility mode.
        }
        if (true === $reflection->getConstant('LICENSED')) {
            glsr()->append('licensed', $addon, $addonId);
        }
    }
}
