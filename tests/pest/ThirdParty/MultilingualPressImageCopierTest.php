<?php

use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ImageCopier;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;

/*
 * The copier hands each batch to copyAttachmentIds(); this one records the upload folder
 * WordPress gives at that moment instead of copying files.
 */
function folderRecordingImageCopier(): ImageCopier
{
    return new class() extends ImageCopier {
        public array $batches = [];

        public function __construct()
        {
        }

        protected function copyAttachmentIds(array $sourceAttachmentIds): array
        {
            $this->batches[] = [array_values($sourceAttachmentIds), wp_upload_dir()['subdir']];
            return array_values($sourceAttachmentIds);
        }
    };
}

function copyFromSource(ImageCopier $copier, array $attachmentIds): void
{
    protectedMethod($copier::class, 'copyAttachmentsFromSource')->invoke($copier, $attachmentIds);
}

function storedAttachment(string $file): int
{
    return wp_insert_attachment(['post_mime_type' => 'image/jpeg', 'post_title' => wp_basename($file)], $file);
}

function datedFolder(): string
{
    $time = current_time('mysql');
    return '/'.substr($time, 0, 4).'/'.substr($time, 5, 2);
}

it('copies a review image into the site-reviews folder and any other file where WordPress puts it', function () {
    update_option('uploads_use_yearmonth_folders', 1);
    $review = storedAttachment('site-reviews/2025/03/photo.jpg');
    $other = storedAttachment('2025/03/team.jpg');
    $copier = folderRecordingImageCopier();
    copyFromSource($copier, [$review, $other]);
    expect($copier->batches)->toBe([
        [[$review], '/site-reviews'.datedFolder()],
        [[$other], wp_upload_dir()['subdir']],
    ]);
});

it('copies a review image into the undated site-reviews folder when the site does not use dated folders', function () {
    update_option('uploads_use_yearmonth_folders', 0);
    $review = storedAttachment('site-reviews/photo.jpg');
    $copier = folderRecordingImageCopier();
    copyFromSource($copier, [$review]);
    expect($copier->batches)->toBe([[[$review], '/site-reviews']]);
});

it('treats only a file stored under the uploads site-reviews folder as a review image', function () {
    update_option('uploads_use_yearmonth_folders', 0);
    $nested = storedAttachment('brand/site-reviews/logo.jpg');
    $copier = folderRecordingImageCopier();
    copyFromSource($copier, [$nested]);
    expect($copier->batches)->toBe([[[$nested], wp_upload_dir()['subdir']]]);
});

it('stops filtering the upload folder once the review images are copied', function () {
    $review = storedAttachment('site-reviews/photo.jpg');
    $copier = folderRecordingImageCopier();
    copyFromSource($copier, [$review]);
    expect(has_filter('upload_dir', [$copier, 'filterUploadDir']))->toBeFalse()
        ->and(wp_upload_dir()['subdir'])->not->toStartWith('/site-reviews');
});
