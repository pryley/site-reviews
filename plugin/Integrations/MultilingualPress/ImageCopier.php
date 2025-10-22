<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Addon\Images\Uploader;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use Inpsyde\MultilingualPress\Editor\Notices\ExistingAttachmentsNotice;
use Inpsyde\MultilingualPress\Framework\Filesystem;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class ImageCopier
{
    use SwitchSiteTrait;

    protected RelationshipContext $context;
    protected ExistingAttachmentsNotice $existingAttachmentsNotice;
    protected Filesystem $filesystem;

    public function __construct(
        Filesystem $filesystem,
        ExistingAttachmentsNotice $existingAttachmentsNotice
    ) {
        $this->existingAttachmentsNotice = $existingAttachmentsNotice;
        $this->filesystem = $filesystem;
    }

    /**
     * Retrieve the attachment id of the existing attachment based on the file name.
     */
    public function attachmentIdFromPath(string $attachmentPath): int
    {
        return $this->existingAttachmentId($attachmentPath);
    }

    /**
     * Copy attachments from source site to the give remote site using a list of attachment ids.
     */
    public function copyById(int $sourceSiteId, int $remoteSiteId, array $sourceAttachmentIds): array
    {
        $this->context = new RelationshipContext([
            RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
            RelationshipContext::SOURCE_SITE_ID => $sourceSiteId,
        ]);
        return $this->copyAttachmentsFromSource($sourceAttachmentIds);
    }

    /**
     * Copy attachments from source site to the give remote site using a list of attachment ids.
     */
    public function copyByIdWithContext(RelationshipContext $context, array $sourceAttachmentIds): array
    {
        $this->context = $context;
        return $this->copyAttachmentsFromSource($sourceAttachmentIds);
    }

    /**
     * Retrieve the meta by the given attachment post.
     */
    protected function attachmentMeta(\WP_Post $attachment): array
    {
        $altMeta = (string) get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
        $sourceUrl = (string) get_post_meta($attachment->ID, '_source_url', true);
        return array_filter([
            '_source_url' => sanitize_url($sourceUrl), // this will exist if the image was imported from a CSV file
            '_wp_attachment_image_alt' => sanitize_text_field($altMeta),
        ]);
    }

    protected function copyAttachmentIds(array $sourceAttachmentIds): array
    {
        $sourceAttachmentIds = $this->ensureAttachmentIds($sourceAttachmentIds);
        $sourceAttachments = $this->sourceAttachments($sourceAttachmentIds);
        return $sourceAttachments
            ? $this->copyToRemoteSite(...$sourceAttachments)
            : [];
    }

    protected function copyAttachmentsFromSource(array $sourceAttachmentIds): array
    {
        $attachmentIds = [];
        $reviewAttachmentIds = [];
        if (class_exists('GeminiLabs\SiteReviews\Addon\Images\Uploader')) {
            $reviewAttachmentIds = array_filter($sourceAttachmentIds,
                fn ($attachmentId) => str_contains(get_attached_file($attachmentId, true), 'site-reviews/')
            );
            if (!empty($reviewAttachmentIds)) {
                glsr(Uploader::class)->setUploadPath(glsr()->id);
                glsr(Uploader::class)->setIntermediateImageSizes();
                $attachmentIds = $this->copyAttachmentIds($reviewAttachmentIds);
                glsr(Uploader::class)->resetIntermediateImageSizes();
                glsr(Uploader::class)->resetUploadPath();
            }
        }
        if ($otherAttachmentIds = array_diff($sourceAttachmentIds, $reviewAttachmentIds)) {
            $copiedAttachmentIds = $this->copyAttachmentIds($otherAttachmentIds);
            return array_merge($attachmentIds, $copiedAttachmentIds);
        }
        return $attachmentIds;
    }

    /**
     * Copy the attachment meta from the source give post to the remote attachment.
     */
    protected function copyMetaFromSourceAttachment(
        AttachmentData $sourceAttachmentData,
        int $remoteAttachmentId
    ): void {
        foreach ($sourceAttachmentData->meta() as $attachmentKey => $sourceAttachmentMeta) {
            update_post_meta($remoteAttachmentId, $attachmentKey, $sourceAttachmentMeta);
        }
    }

    /**
     * Copy the attachment sizes from the given source post to the remote site.
     */
    protected function copySizesFromSourceAttachment(
        AttachmentData $sourceAttachmentData,
        int $remoteAttachmentId
    ): void {
        $remoteUploadPath = wp_upload_dir()['path'] ?? '';
        $sourceUploadPath = dirname($sourceAttachmentData->filePath());
        $sourceAttachmentBasename = wp_basename($sourceAttachmentData->filePath());
        $remoteAttachmentRealPath = "{$remoteUploadPath}/{$sourceAttachmentBasename}";
        $metadata = $sourceAttachmentData->fileMeta();
        $sizes = wp_get_registered_image_subsizes();
        $sizes = apply_filters('intermediate_image_sizes_advanced', $sizes, $metadata, $remoteAttachmentId);
        $copiedSizes = [];
        foreach (($metadata['sizes'] ?? []) as $size => $meta) {
            if (!array_key_exists($size, $sizes)) {
                continue;
            }
            $sourceFile = "{$sourceUploadPath}/{$meta['file']}";
            $remoteFile = "{$remoteUploadPath}/{$meta['file']}";
            if ($this->filesystem->pathExists($remoteFile) && $this->filesystem->isReadable($remoteFile)) {
                $copiedSizes[$size] = $meta;
                continue;
            }
            if ($this->filesystem->copy($sourceFile, $remoteFile)) {
                $copiedSizes[$size] = $meta;
                continue;
            }
            $meta = image_make_intermediate_size(
                $remoteAttachmentRealPath,
                $sizes[$size]['width'] ?? null,
                $sizes[$size]['height'] ?? null,
                $sizes[$size]['crop'] ?? false
            );
            if ($meta) {
                $copiedSizes[$size] = $meta;
            }
        }
        $metadata['sizes'] = $copiedSizes;
        if (empty($metadata['sizes'])) {
            $metadata = wp_generate_attachment_metadata($remoteAttachmentId, $remoteAttachmentRealPath);
        }
        wp_update_attachment_metadata($remoteAttachmentId, $metadata);
    }

    /**
     * Copy attachment file to the remote upload dir and create a new attachment post.
     */
    protected function copyToRemoteSite(AttachmentData ...$sourceAttachmentsData): array
    {
        $originalSite = $this->maybeSwitchSite($this->context->remoteSiteId());
        $remoteAttachments = [];
        $uploadDir = wp_upload_dir();
        $uploadPath = $uploadDir['path'] ?? '';
        $uploadUrl = $uploadDir['url'] ?? '';
        if (!$uploadPath || !$uploadUrl || !$this->filesystem->mkDirP($uploadDir['path'])) {
            $this->maybeRestoreSite($originalSite);
            return [];
        }
        foreach ($sourceAttachmentsData as $sourceAttachmentData) {
            $sourceAttachmentPath = $sourceAttachmentData->filePath();
            $sourceAttachmentBasename = wp_basename($sourceAttachmentPath);
            if ($existingAttachmentId = $this->existingAttachmentId($sourceAttachmentData->relativePath())) {
                $updatedId = $this->updateAttachment($sourceAttachmentData, $existingAttachmentId);
                if ($updatedId) {
                    $remoteAttachments[] = $updatedId;
                }
                continue;
            }
            $remoteAttachmentRealPath = "{$uploadPath}/{$sourceAttachmentBasename}";
            $remoteAttachmentUrl = "{$uploadUrl}/{$sourceAttachmentBasename}";
            if (!$this->filesystem->pathExists($sourceAttachmentPath)
                || !$this->filesystem->isReadable($sourceAttachmentPath)) {
                continue;
            }
            if (!$this->filesystem->copy($sourceAttachmentPath, $remoteAttachmentRealPath)) {
                continue;
            }
            $remoteAttachments[] = $this->createAttachmentPostByPath(
                $sourceAttachmentData,
                $remoteAttachmentRealPath,
                $remoteAttachmentUrl
            );
        }
        $this->maybeRestoreSite($originalSite);
        return array_filter($remoteAttachments);
    }

    /**
     * Create an attachment post by the attachment path.
     */
    protected function createAttachmentPostByPath(
        AttachmentData $sourceAttachmentData,
        string $remoteAttachmentRealPath,
        string $remoteAttachmentUrl
    ): int {
        $filetype = wp_check_filetype($remoteAttachmentRealPath);
        $sourceAttachment = $sourceAttachmentData->post();
        $remoteAttachmentData = [
            'guid' => $remoteAttachmentUrl,
            'menu_order' => $sourceAttachment->menu_order,
            'post_author' => get_current_user_id(),
            'post_content' => $sourceAttachment->post_content,
            'post_excerpt' => $sourceAttachment->post_excerpt,
            'post_mime_type' => $filetype['type'] ?? '',
            'post_title' => $sourceAttachment->post_title,
        ];
        $this->requireAttachmentFunctions();
        $remoteAttachmentId = wp_insert_attachment(
            $remoteAttachmentData,
            $remoteAttachmentRealPath,
            $this->context->remotePostId(), // attach to the remote post
        );
        if ($remoteAttachmentId instanceof \WP_Error) {
            return 0;
        }
        $this->copyMetaFromSourceAttachment(
            $sourceAttachmentData,
            $remoteAttachmentId
        );
        $this->copySizesFromSourceAttachment(
            $sourceAttachmentData,
            $remoteAttachmentId
        );
        return $remoteAttachmentId;
    }

    /**
     * Ensure attachment ids are valid integer values.
     */
    protected function ensureAttachmentIds(array $attachmentIds): array
    {
        return array_filter(wp_parse_id_list($attachmentIds));
    }

    /**
     * Retrieve the attachment id of the existing attachment based on the file name.
     */
    protected function existingAttachmentId(string $attachmentPath): int
    {
        $relativeAttachmentPath = Str::removePrefix(
            $attachmentPath,
            trailingslashit(wp_upload_dir()['basedir'] ?? '')
        );
        $sql = glsr(Query::class)->sql("
            SELECT post_id
            FROM table|postmeta
            WHERE meta_key = '_wp_attached_file'
            AND meta_value LIKE %s
        ", "%{$relativeAttachmentPath}");
        return Cast::toInt(glsr(Database::class)->dbGetVar($sql));
    }

    /**
     * Check if an attachment post is a valid attachment.
     */
    protected function isLocalAttachment(\WP_Post $attachment): bool
    {
        return is_local_attachment((string) get_permalink($attachment));
    }

    /**
     * Require functions to work with attachments.
     */
    protected function requireAttachmentFunctions(): void
    {
        if (!\function_exists('wp_generate_attachment_metadata')
            && !\function_exists('MultilingualPress\Vendor\wp_generate_attachment_metadata')) {
            require_once ABSPATH.'wp-admin/includes/image.php';
        }
        if (!\function_exists('get_intermediate_image_sizes')) {
            require_once ABSPATH.'wp-admin/includes/media.php';
        }
    }

    /**
     * Retrieve the attachments post and files path
     * The items contains the post and the real path of the attachment.
     *
     * @param int[] $attachmentIds
     *
     * @return AttachmentData[]
     */
    protected function sourceAttachments(array $attachmentIds): array
    {
        $sourceAttachments = [];
        foreach ($attachmentIds as $attachmentId) {
            $attachment = get_post($attachmentId);
            if (!$attachment instanceof \WP_Post || !$this->isLocalAttachment($attachment)) {
                continue;
            }
            $attachmentPath = get_attached_file($attachmentId, true); // unfiltered
            if (!$attachmentPath) {
                continue;
            }
            $relativeAttachmentPath = Str::removePrefix(
                $attachmentPath,
                trailingslashit(wp_upload_dir()['basedir'] ?? '')
            );
            $sourceAttachments[$attachmentId] = new AttachmentData(
                $attachment,
                $this->attachmentMeta($attachment),
                $attachmentPath,
                $relativeAttachmentPath
            );
        }
        return $sourceAttachments;
    }

    /**
     * Update the remote attachment post meta data with data provided by the given source attachment.
     */
    protected function updateAttachment(
        AttachmentData $sourceAttachmentData,
        int $remoteAttachmentId
    ): int {
        $data = $sourceAttachmentData->post()->to_array();
        $data['ID'] = $remoteAttachmentId;
        $data['post_parent'] = $this->context->remotePostId(); // attach to the remote post
        $attachmentId = wp_update_post($data, true);
        if (is_wp_error($attachmentId)) {
            $this->existingAttachmentsNotice->addAttachment(
                $sourceAttachmentData->post()->ID,
                $this->context->remoteSiteId()
            );
            return 0;
        }
        $this->copyMetaFromSourceAttachment(
            $sourceAttachmentData,
            $remoteAttachmentId
        );
        $this->copySizesFromSourceAttachment(
            $sourceAttachmentData,
            $remoteAttachmentId
        );
        return $attachmentId;
    }
}
