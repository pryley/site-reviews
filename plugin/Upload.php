<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Exceptions\FileException;
use GeminiLabs\SiteReviews\Exceptions\FileNotFoundException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;

trait Upload
{
    protected function getImportFile(string $expectedMimeType): ?UploadedFile
    {
        try {
            $file = $this->file();
        } catch (FileNotFoundException $e) {
            glsr(Notice::class)->addError($e->getMessage());
            return null;
        }
        if (!$file->isValid()) {
            glsr(Notice::class)->addError($file->getErrorMessage());
            return null;
        }
        if (!$file->hasMimeType($expectedMimeType)) {
            glsr(Notice::class)->addError(
                sprintf(_x('The file you uploaded is not a valid %s file.', '%s: uploaded file extension (admin-text)', 'site-reviews'),
                    strtoupper($file->getExtensionFromMimeType())
                )
            );
            return null;
        }
        return $file;
    }

    protected function getImportFileData(UploadedFile $file): array
    {
        try {
            $data = json_decode($file->getContent(), true);
            $data = Arr::consolidate($data);
        } catch (FileException $e) {
            glsr(Notice::class)->addError($e->getMessage());
            return [];
        }
        if (empty($data)) {
            glsr(Notice::class)->addWarning(
                _x('There was nothing found to import.', 'admin-text', 'site-reviews')
            );
        }
        return $data;
    }

    /**
     * @throws FileNotFoundException
     */
    protected function file(): UploadedFile
    {
        $files = Arr::get($_FILES, 'import-files', []);
        $files = $this->fixPhpFilesArray($files);
        if (!wp_is_numeric_array($files)) {
            return new UploadedFile($files);
        }
        return new UploadedFile(Arr::get($files, 0, []));
    }

    /**
     * This skips any files that don't exist and logs the error.
     *
     * @return UploadedFile[]
     */
    protected function files(): array
    {
        $files = [];
        $importFiles = Arr::get($_FILES, 'import-files', []);
        $importFiles = $this->fixPhpFilesArray($importFiles);
        if (!wp_is_numeric_array($importFiles)) {
            $importFiles = [$importFiles];
        }
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
