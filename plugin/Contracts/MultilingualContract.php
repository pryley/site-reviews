<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MultilingualContract
{
    public function getPostId(int $postId): int;

    public function getPostIds(array $postIds): array;

    public function isActive(): bool;

    public function isEnabled(): bool;

    public function isSupported(): bool;
}
