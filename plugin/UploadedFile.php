<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\UploadedFileDefaults;
use GeminiLabs\SiteReviews\Exceptions\FileException;
use GeminiLabs\SiteReviews\Exceptions\FileNotFoundException;

class UploadedFile extends \SplFileInfo
{
    private int $error;
    private string $mimeType;
    private string $originalName;
    private int $size;

    /**
     * @throws FileNotFoundException
     */
    public function __construct(array $filedata)
    {
        $data = glsr(UploadedFileDefaults::class)->restrict($filedata);
        $this->error = $data['error'] ?: \UPLOAD_ERR_OK;
        $this->mimeType = $data['type'] ?: 'application/octet-stream';
        $this->originalName = $this->getName($data['name']);
        $this->size = $data['size'];
        if (\UPLOAD_ERR_OK !== $this->error && !is_file($data['tmp_name'])) {
            throw new FileNotFoundException($data['tmp_name']);
        }
        parent::__construct($data['tmp_name']);
    }

    /**
     * Returns the file mime type extracted from the file upload request.
     * This should not be considered as a safe value.
     */
    public function getClientMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Returns the original file name extracted from the file upload request.
     * This should not be considered as a safe value to use for a file name on your servers.
     */
    public function getClientOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * Returns the original file extension extracted from the file upload request.
     * This should not be considered as a safe value to use for a file name on your servers.
     */
    public function getClientOriginalExtension(): string
    {
        return pathinfo($this->originalName, \PATHINFO_EXTENSION);
    }

    /**
     * Returns the file size extracted from the file upload request.
     * This should not be considered as a safe value.
     */
    public function getClientSize(): int
    {
        return $this->size;
    }

    /**
     * @throws FileException
     */
    public function getContent(): string
    {
        $content = file_get_contents($this->getPathname());
        if (false === $content) {
            throw new FileException(sprintf('Could not get the content of the file "%s".', $this->getPathname()));
        }
        return $content;
    }

    /**
     * If the upload was successful, the constant UPLOAD_ERR_OK is returned.
     * Otherwise one of the other UPLOAD_ERR_XXX constants is returned.
     */
    public function getError(): int
    {
        return $this->error;
    }

    public function getErrorMessage(): string
    {
        $errors = [
            \UPLOAD_ERR_INI_SIZE => _x('The file "%s" exceeds the upload_max_filesize ini directive (limit is %d KiB).', 'file error (admin-text)', 'site-reviews'),
            \UPLOAD_ERR_FORM_SIZE => _x('The file "%s" exceeds the upload limit defined in your form.', 'file error (admin-text)', 'site-reviews'),
            \UPLOAD_ERR_PARTIAL => _x('The file "%s" was only partially uploaded.', 'file error (admin-text)', 'site-reviews'),
            \UPLOAD_ERR_NO_FILE => _x('No file was uploaded.', 'file error (admin-text)', 'site-reviews'),
            \UPLOAD_ERR_CANT_WRITE => _x('The file "%s" could not be written on disk.', 'file error (admin-text)', 'site-reviews'),
            \UPLOAD_ERR_NO_TMP_DIR => _x('File could not be uploaded: missing temporary directory.', 'file error (admin-text)', 'site-reviews'),
            \UPLOAD_ERR_EXTENSION => _x('File upload was stopped by a PHP extension.', 'file error (admin-text)', 'site-reviews'),
        ];
        $errorCode = $this->error;
        $maxFilesize = \UPLOAD_ERR_INI_SIZE === $errorCode ? wp_max_upload_size() / 1024 : 0;
        $message = $errors[$errorCode] ?? 'The file "%s" was not uploaded due to an unknown error.';
        return sprintf($message, $this->getClientOriginalName(), $maxFilesize);
    }

    public function getExtensionFromMimeType(): string
    {
        $mimetypes = wp_parse_args(get_allowed_mime_types(), [
            'json' => 'application/json',
        ]);
        $extensions = explode('|', array_search($this->getMimeType(), $mimetypes, true));
        return $extensions[0] ?? $this->getExtension() ?? $this->getClientOriginalExtension();
    }

    /**
     * This should not be considered a safe value.
     */
    public function getMimeType(): string
    {
        if (function_exists('mime_content_type')) {
            if ($mimeType = mime_content_type($this->getPathname())) {
                return $mimeType;
            }
        }
        return $this->getClientMimeType();
    }

    /**
     * Checks against the file mime type extracted from the file upload request.
     * This should not be considered a safe check.
     */
    public function hasMimeType(string $mimeType): bool
    {
        $detectedMimeType = $this->getMimeType();
        if ('text/csv' === $mimeType && 'application/vnd.ms-excel' === $detectedMimeType) {
            return 'csv' === ($this->getExtension() ?? $this->getClientOriginalExtension());
        }
        $inconclusiveMimeTypes = [
            'application/octet-stream',
            'application/x-empty',
            'text/plain',
        ];
        if (in_array($detectedMimeType, $inconclusiveMimeTypes)) {
            return true;
        }
        return $mimeType === $detectedMimeType;
    }

    /**
     * Returns whether the file has been uploaded with HTTP and no error occurred.
     */
    public function isValid(): bool
    {
        $isOk = \UPLOAD_ERR_OK === $this->error;
        return $isOk && is_uploaded_file($this->getPathname());
    }

    /**
     * Returns locale independent base name of the given path.
     */
    protected function getName(string $name): string
    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');
        $originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);
        return $originalName;
    }
}
