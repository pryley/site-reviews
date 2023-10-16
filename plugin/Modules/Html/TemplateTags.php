<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\TemplateTagsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Rating;
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

    public function filteredTags(array $args): array
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
        foreach ($review->assigned_posts as $postId) {
            $postId = glsr(Multilingual::class)->getPostId(Helper::getPostId($postId));
            if (!empty($postId) && !array_key_exists($postId, $links)) {
                $title = get_the_title($postId);
                if (empty(trim($title))) {
                    $title = __('(no title)', 'site-reviews');
                }
                $links[$postId] = sprintf($format, (string) get_the_permalink($postId), $title);
            }
        }
        return Str::naturalJoin($links);
    }

    public function tagReviewAssignedPosts(Review $review): string
    {
        $posts = $review->assignedPosts();
        $titles = wp_list_pluck($posts, 'post_title');
        array_walk($titles, function (&$title) {
            if (empty(trim($title))) {
                $title = __('(no title)', 'site-reviews');
            }
        });
        return Str::naturalJoin($titles);
    }

    public function tagReviewAssignedTerms(Review $review): string
    {
        $terms = $review->assignedTerms();
        $termNames = array_filter(wp_list_pluck($terms, 'name'));
        return Str::naturalJoin($termNames);
    }

    public function tagReviewAssignedUsers(Review $review): string
    {
        $users = $review->assignedUsers();
        $userNames = array_filter(wp_list_pluck($users, 'display_name'));
        return Str::naturalJoin($userNames);
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

    /**
     * @compat
     */
    public function tagReviewLink(Review $review): string
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
            $method = Helper::buildMethodName($tag, 'tag');
            if (method_exists($this, $method)) {
                $content = call_user_func([$this, $method], $review);
            }
            $content = glsr()->filterString('notification/tag/'.$tag, $content, $review);
        });
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
