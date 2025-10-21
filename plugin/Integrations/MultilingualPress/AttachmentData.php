<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

class AttachmentData
{
    protected string $filePath;
    protected array $fileMeta;
    protected array $meta;
    protected \WP_Post $post;
    protected string $relativePath;

    public function __construct(\WP_Post $post, array $meta, string $filePath, string $relativePath)
    {
        $this->post = $post;
        $this->meta = $meta;
        $this->fileMeta = wp_get_attachment_metadata($post->ID, true) ?: []; // unfiltered
        $this->filePath = $filePath;
        $this->relativePath = $relativePath;
    }

    public function post(): \WP_Post
    {
        return $this->post;
    }

    public function meta(): array
    {
        return $this->meta;
    }

    public function fileMeta(): array
    {
        return $this->fileMeta;
    }

    public function filePath(): string
    {
        return $this->filePath;
    }

    public function relativePath(): string
    {
        return $this->relativePath;
    }
}
