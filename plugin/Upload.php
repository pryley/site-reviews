<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\UploadedFile;
use GeminiLabs\SiteReviews\Exceptions\FileNotFoundException;

trait Upload
{
    /**
     * @throws FileNotFoundException
     */
    protected function file(): UploadedFile
    {
        $files = Arr::get($_FILES, 'import-files', []);
        $files = $this->fixPhpFilesArray($files);
        return new UploadedFile(Arr::get($files, 0, []));
    }

    /**
     * This skips any files that don't exist and logs the error.
     * @return UploadedFile[]
     */
    protected function files(): array
    {
        $files = [];
        $importFiles = Arr::get($_FILES, 'import-files', []);
        $importFiles = $this->fixPhpFilesArray($importFiles);
        foreach ($importFiles as $data) {
            try {
                $files[] = new UploadedFile($data);
            } catch (FileNotFoundException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
        return $files;
    }

    protected function fixPhpFilesArray(array $data): array
    {
        unset($data['full_path']); // Remove extra key added by PHP 8.1.
        $fileKeys = ['error', 'name', 'size', 'tmp_name', 'type'];
        $keys = array_keys($data);
        sort($keys);
        if ($fileKeys !== $keys || !isset($data['name']) || !is_array($data['name'])) {
            return $data;
        }
        $files = $data;
        foreach ($fileKeys as $k) {
            unset($files[$k]);
        }
        foreach ($data['name'] as $key => $name) {
            $files[$key] = $this->fixPhpFilesArray([
                'error' => $data['error'][$key],
                'name' => $name,
                'type' => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size' => $data['size'][$key],
            ]);
        }
        return $files;
    }
}
