<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;

class Upload
{
    protected function file(): Arguments
    {
        return glsr()->args(Arr::get($_FILES, 'import-file', []));
    }

    protected function getUploadError(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => _x('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'admin-text', 'site-reviews'),
            UPLOAD_ERR_FORM_SIZE => _x('The uploaded file is too big.', 'admin-text', 'site-reviews'),
            UPLOAD_ERR_PARTIAL => _x('The uploaded file was only partially uploaded.', 'admin-text', 'site-reviews'),
            UPLOAD_ERR_NO_FILE => _x('No file was uploaded.', 'admin-text', 'site-reviews'),
            UPLOAD_ERR_NO_TMP_DIR => _x('Missing a temporary folder.', 'admin-text', 'site-reviews'),
            UPLOAD_ERR_CANT_WRITE => _x('Failed to write file to disk.', 'admin-text', 'site-reviews'),
            UPLOAD_ERR_EXTENSION => _x('A PHP extension stopped the file upload.', 'admin-text', 'site-reviews'),
        ];
        return Arr::get($errors, $errorCode, _x('Unknown upload error.', 'admin-text', 'site-reviews'));
    }

    protected function validateExtension(string $extension): bool
    {
        if (str_ends_with($this->file()->name, $extension)) {
            return true;
        }
        glsr(Notice::class)->addError(sprintf(
            _x('Please upload a valid %s file.', 'admin-text', 'site-reviews'),
            strtoupper($extension)
        ));
        return false;
    }

    protected function validateUpload(): bool
    {
        if (UPLOAD_ERR_OK === $this->file()->error) {
            return true;
        }
        glsr(Notice::class)->addError($this->getUploadError($this->file()->error));
        return false;
    }
}
