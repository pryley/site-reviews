<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\TemplateTagsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class TemplateTags
{
    public function __call($method, $args)
    {
        if ('tagList' === $method) { // @compat
            return call_user_func_array([$this, 'listTags'], $args);
        }
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    public function filteredTags(array $args = []): array
    {
        $exclude = Arr::consolidate(Arr::get($args, 'exclude'));
        $include = Arr::consolidate(Arr::get($args, 'include'));
        $insert = Arr::consolidate(Arr::get($args, 'insert'));
        $tags = glsr(TemplateTagsDefaults::class)->defaults();
        if (!empty($exclude)) {
            $tags = array_diff_key($tags, array_flip($exclude));
        }
        if (!empty($include)) {
            $tags = array_intersect_key($tags, array_flip($include));
        }
        if (!empty($insert)) {
            $tags = array_merge($tags, $insert);
        }
        ksort($tags);
        return $tags;
    }

    public function listTags(array $args = []): string
    {
        $tags = array_keys($this->filteredTags($args));
        array_walk($tags, function (&$tag) {
            $tag = sprintf('<li><code>{%s}</code></li>', $tag);
        });
        return sprintf('<ul>%s</ul>', implode('', $tags));
    }

    public function tagAdminEmail(): string
    {
        return Cast::toString(get_bloginfo('admin_email'));
    }

    public function tagApproveUrl(Review $review): string
    {
        return $review->approveUrl();
    }

    public function tagEditUrl(Review $review): string
    {
        return $review->editUrl();
    }

    public function tagReviewAssignedLinks(Review $review, string $format = '<a href="%s">%s</a>'): string
    {
        $links = [];
        $posts = $review->assignedPosts();
        foreach ($posts as $post) {
            $title = trim(get_the_title($post->ID));
            $title = $title ?: $post->post_name ?: $post->ID;
            $links[$post->ID] = sprintf($format, (string) get_the_permalink($post->ID), $title);
        }
        return Str::naturalJoin($links);
    }

    public function tagReviewAssignedPosts(Review $review): string
    {
        $posts = $review->assignedPosts();
        $titles = wp_list_pluck($posts, 'post_title', 'ID');
        $titles = array_map(fn ($title) => trim($title) ?: __('(no title)', 'site-reviews'), $titles);
        return Str::naturalJoin($titles);
    }

    public function tagReviewAssignedTerms(Review $review): string
    {
        $terms = $review->assignedTerms();
        $names = array_filter(wp_list_pluck($terms, 'name', 'term_taxonomy_id'));
        return Str::naturalJoin($names);
    }

    public function tagReviewAssignedUsers(Review $review): string
    {
        $users = $review->assignedUsers();
        $names = [];
        foreach ($users as $user) {
            $name = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
            $names[] = $name;
        }
        return Str::naturalJoin($names);
    }

    public function tagReviewAuthor(Review $review): string
    {
        return $review->author();
    }

    public function tagReviewCategories(Review $review): string
    {
        return $this->tagReviewAssignedTerms($review);
    }

    public function tagReviewContent(Review $review): string
    {
        return (string) $review->content;
    }

    public function tagReviewEmail(Review $review): string
    {
        return (string) $review->email;
    }

    public function tagReviewId(Review $review): string
    {
        return (string) $review->ID;
    }

    public function tagReviewIp(Review $review): string
    {
        return (string) $review->ip_address;
    }

    public function tagReviewLink(Review $review): string // @compat v6
    {
        return glsr(Builder::class)->a([
            'href' => $review->editUrl(),
            'text' => _x('View the review in WordPress &rarr;', 'admin-text', 'site-reviews'),
        ]);
    }

    public function tagReviewRating(Review $review): string
    {
        return Cast::toString($review->rating);
    }

    public function tagReviewResponse(Review $review): string
    {
        return (string) $review->response;
    }

    public function tagReviewStars(Review $review): string
    {
        $full = str_repeat('★', $review->rating);
        $empty = str_repeat('☆', Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)) - $review->rating);
        return $full.$empty;
    }

    public function tagReviewTitle(Review $review): string
    {
        return (string) $review->title;
    }

    public function tags(Review $review, array $args = []): array
    {
        $tags = $this->filteredTags($args);
        array_walk($tags, function (&$content, $tag) use ($review) {
            $content = ''; // remove the tag description first!
            $method = Helper::buildMethodName('tag', $tag);
            if (method_exists($this, $method)) {
                $content = call_user_func([$this, $method], $review);
            }
            $content = glsr()->filterString("notification/tag/{$tag}", $content, $review);
        });
        if (array_key_exists('edit_url', $tags)) {
            $tags['review_link'] = glsr(Builder::class)->a([ // @compat v6
                'href' => esc_url($tags['edit_url']),
                'text' => __('Edit Review', 'site-reviews'),
            ]);
        }
        return $tags;
    }

    public function tagSiteTitle(): string
    {
        return wp_specialchars_decode(Cast::toString(get_bloginfo('name')), ENT_QUOTES);
    }

    public function tagSiteUrl(): string
    {
        return Cast::toString(get_bloginfo('url'));
    }

    public function tagVerifiedDate(Review $review): string
    {
        $timestamp = $review->meta()->_verified_on;
        return glsr(Date::class)->isTimestamp($timestamp)
            ? glsr(Date::class)->localized($timestamp)
            : '';
    }

    public function tagVerifyUrl(Review $review): string
    {
        return $review->verifyUrl();
    }
}
