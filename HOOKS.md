## Actions

    site-reviews/addon/register                             (Application $app)
    site-reviews/addon/settings/<tab>                       (string $rows)
    site-reviews/builder                                    (Modules\Html\Builder $builder)
    site-reviews/customize/<style>                          (Modules\Html\Builder $builder)
    site-reviews/database/sql                               (string $sql, string $handle)
    site-reviews/database/sql/<handle>                      (string $sql)
    site-reviews/defaults                                   (Contracts\DefaultsContract $defaults, string $hook, string $method)
    site-reviews/export/cleanup                             ()
    site-reviews/get/review                                 (Review $review, int $reviewId)
    site-reviews/get/reviews                                (array $reviews, array $args)
    site-reviews/personal-data/erase                        (Review $review, bool $retainReview)
    site-reviews/review/build/before                        (Review $review)
    site-reviews/review/create                              (int $postId, Commands\CreateReview $command)
    site-reviews/review/created                             (Review $review, Commands\CreateReview $command)
    site-reviews/review/respond                             (string $response, Review $review)
    site-reviews/review/saved                               (Review $review, array $submittedValues)
    site-reviews/review/updated/post_ids                    (Review $review, array $assignedPostIds)
    site-reviews/review/updated/user_ids                    (Review $review, array $assignedUserIds)
    site-reviews/route/<request_type>/<route_action>        (Request $request)
    site-reviews/whip                                       (Vectorface\Whip\Whip $whip)

## Filters

    site-reviews/addon/api-url                              (string $apiUrl): string
    site-reviews/addon/documentation                        (array $documentation): array
    site-reviews/addon/documentation/tabs                   (array $tabs): array
    site-reviews/addon/settings                             (array $settings): array
    site-reviews/addon/settings/tabs                        (array $tabs): array
    site-reviews/addon/submenu/callback                     (callable $callable, string $slug): callable
    site-reviews/addon/submenu/pages                        (array $args): array
    site-reviews/addon/sync/enable                          (bool $enable): bool
    site-reviews/addon/sync/services                        (array $services): array
    site-reviews/addon/system-info                          (array $settings): array
    site-reviews/addon/tools/tabs                           (array $tabs): array
    site-reviews/addon/types                                (array $types): array
    site-reviews/addon/welcome/tabs                         (array $tabs): array
    site-reviews/assets/css                                 (bool $loadCss): bool
    site-reviews/assets/js                                  (bool $loadJs): bool
    site-reviews/assets/polyfill                            (bool $loadPolyfill): bool
    site-reviews/async-scripts                              (array $scripts): array
    site-reviews/avatar/fallback                            (string $fallbackUrl, int $size): string
    site-reviews/block/<block>/attributes                   (array $attributes): array
    site-reviews/build/template/<template_path>             (string $template, array $data): string
    site-reviews/builder/<field_type>/args                  (array $args, Modules\Html\Builder $builder): array
    site-reviews/builder/<tag>/args                         (array $args, Modules\Html\Builder $builder): array
    site-reviews/builder/field/<field_type>                 (string $className): string
    site-reviews/builder/result                             (string $result, Modules\Html\Builder $builder): string
    site-reviews/capabilities                               (array $capabilities): array
    site-reviews/capabilities/for-roles                     (array $capabilities): array
    site-reviews/column/<column>                            (string $className): string
    site-reviews/columns/<column_slug>                      (string $value, int $postId): string
    site-reviews/columns/orderby-is-null                    (array $columns): array
    site-reviews/config                                     (string $path): string
    site-reviews/config/<config_id>                         (array $config): array
    site-reviews/console/level                              (int $level): int
    site-reviews/const/<constant>                           (string $constantName): string
    site-reviews/create/review-values                       (array $reviewValues, Commands\CreateReview $command): array
    site-reviews/defaults/<defaults_name>                   (array $defaults, string $method): array
    site-reviews/defaults/<defaults_name>/casts             (array $casts, string $method): array
    site-reviews/defaults/<defaults_name>/concatenated      (array $concatenated, string $method): array
    site-reviews/defaults/<defaults_name>/defaults          (array $defaults): array
    site-reviews/defaults/<defaults_name>/guarded           (array $guarded, string $method): array
    site-reviews/defaults/<defaults_name>/mapped            (array $mapped, string $method): array
    site-reviews/defaults/<defaults_name>/sanitize          (array $sanitize, string $method): array
    site-reviews/defer-scripts                              (array $scripts): array
    site-reviews/email/compose                              (array $email, Modules\Mail $mail): array
    site-reviews/email/headers                              (array $headers, Modules\Mail $mail): array
    site-reviews/email/message                              (string $message, string $type, Modules\Mail $mail): string
    site-reviews/enqueue/admin/dependencies                 (array $dependencies): array
    site-reviews/enqueue/admin/localize                     (array $variables): array
    site-reviews/enqueue/public/dependencies                (array $dependencies): array
    site-reviews/enqueue/public/inline-script               (string $optimizedScript, string $script, array $variables): string
    site-reviews/enqueue/public/localize                    (array $variables): array
    site-reviews/enqueue/public/localize/ajax-pagination    (array $selectors): array
    site-reviews/field/<field_type>                         (array $field): array
    site-reviews/form/build/<tag_or_field_key>              (string $field, Arguments $with, Modules\Html\Partials\SiteReviewsForm $partial): string
    site-reviews/get/defaults                               (array $defaults): array
    site-reviews/interpolate/<template_path>                (array $context, string $template, array $data): array
    site-reviews/metabox/fields                             (array $fields, Review $review): array
    site-reviews/metabox/fields/order                       (array $order): array
    site-reviews/notices                                    (string $notices): string
    site-reviews/notification/emails                        (array $emails, Review $review): array
    site-reviews/notification/title                         (string $title, Review $review): string
    site-reviews/paginate_links                             (string $links, array $args): string
    site-reviews/partial/args/<partial_path>                (array $args): array
    site-reviews/partial/classname                          (string $className, string $partialPath): string
    site-reviews/path                                       (string $path, string $file): string
    site-reviews/personal-data/erase-all                    (bool $eraseAll): bool
    site-reviews/personal-data/export                       (array $data, Review $review): array
    site-reviews/query/sql/and                              (array $whereAnd, Database\Sql $sql): array
    site-reviews/query/sql/clause/operator                  (string $clauseOperator, array $clauses, array $args): string
    site-reviews/query/sql/join                             (array $join, Database\Sql $sql): array
    site-reviews/query/sql/limit                            (string $limit, Database\Sql $sql): string
    site-reviews/query/sql/offset                           (string $offset, Database\Sql $sql): string
    site-reviews/query/sql/order-by                         (string $orderBy, Database\Sql $sql): string
    site-reviews/rating/average                             (float $roundedAverage, array $ratingCounts, int $average): float
    site-reviews/rating/ranking                             (float $ranking, array $ratingCounts, Modules\Rating $rating): float
    site-reviews/recaptcha/language                         (string $locale): string
    site-reviews/recaptcha/timeout                          (int $timeout): int
    site-reviews/render/view                                (string $view, array $data): string
    site-reviews/rendered/field                             (string $field, string $fieldType, array $field): string
    site-reviews/rendered/field/classes                     (array $classes, array $field): array
    site-reviews/rendered/partial                           (string $partial, string $partialPath, array $args): string
    site-reviews/rendered/partial/<partial_path>            (string $partial, array $args): string
    site-reviews/rendered/template                          (string $template, string $templatePath, array $data): string
    site-reviews/rendered/template/<template_path>          (string $template, array $data): string
    site-reviews/review-form/order                          (array $order): array
    site-reviews/review-form/referer                        (string $referer): string
    site-reviews/review/build/<tag_or_field_key>            (string $field, string $value, Review $review, Modules\Html\ReviewHtml $reviewHtml): string
    site-reviews/review/build/after                         (array $templateTags, Review $review, Modules\Html\ReviewHtml $reviewHtml): array
    site-reviews/review/redirect                            (string $redirect, Commands\CreateReview $createReview): string
    site-reviews/review/tag/<tag>                           (string $className): string
    site-reviews/review/value/<tag_or_field_key>            (string $value, Contracts\TagContract $tag): string
    site-reviews/review/wrap/<tag_or_field_key>             (string $value, Review $review, string $rawValue, Contracts\TagContract $tag): string
    site-reviews/reviews/fallback                           (string $fallback, array $args): string
    site-reviews/route/request                              (array $request, string $action, string $requestType): array
    site-reviews/router/unguarded-actions                   (array $unguardedActions): array
    site-reviews/schema/<schema_type>                       (array $schema, array $args): array
    site-reviews/schema/all                                 (array $schemas): array
    site-reviews/schema/review                              (array $schema, Review $review, array $args): array
    site-reviews/settings/callback                          (array $options, array $settings): array
    site-reviews/shortcode/args                             (array $args, string $type, string $partialName): array
    site-reviews/shortcode/atts                             (array $atts, string $type, string $partialName): array
    site-reviews/shortcode/hide-options                     (array $hideOptions, string $shortcode): array
    site-reviews/slack/compose                              (array $notification, Modules\Slack $slack): array
    site-reviews/slack/stars                                (string $stars, int $rating, int $maxRating): string
    site-reviews/sslverify/post                             (bool $sslverify): bool
    site-reviews/style                                      (string $style): string
    site-reviews/style/views                                (array $views): array
    site-reviews/summary/build/<tag_or_field_key>           (string $field, array $ratings, Modules\Html\Partials\SiteReviewsSummary $partial): string
    site-reviews/summary/counts                             (string $ratingCount, int $ratingLevel): string
    site-reviews/summary/value/<tag_or_field_key>           (string $value, Contracts\TagContract $tag): string
    site-reviews/summary/wrap/<tag_or_field_key>            (string $value, array $ratings, string $rawValue, Contracts\TagContract $tag): string
    site-reviews/support/deprecated/v5                      (bool $supportDeprecated): bool
    site-reviews/support/multibyte                          (bool $useMultibytePolyfill): bool
    site-reviews/system/<key>                               (array $details): array
    site-reviews/tinymce/editor-ids                         (array $editorIds, string $editorId): array
    site-reviews/translation/entries                        (array $entries): array
    site-reviews/translator/domains                         (array $domains): array
    site-reviews/trustalyze/response                        (array $trustalyzeResponse, Review $review): array
    site-reviews/trustalyze/review                          (array $trustalyzeReview, Review $review): array
    site-reviews/url                                        (string $url, string $path): string
    site-reviews/validate/akismet                           (bool $isValid, array $submission, array $response): bool
    site-reviews/validate/akismet/is-active                 (bool $isActive): bool
    site-reviews/validate/akismet/submission                (array $submission, Request $request): array
    site-reviews/validate/blacklist                         (bool $isValid, string $target, Request $request): bool
    site-reviews/validate/custom                            (bool $isValid, Request $request): bool|string
    site-reviews/validate/honeypot                          (bool $isValid, Request $request): bool
    site-reviews/validate/review-limits                     (bool $isValid, Reviews $reviews, Request $request, string $key): bool
    site-reviews/validation/rules                           (array $rules, Request $request): array
    site-reviews/validators                                 (array $validatorClasses): array
    site-reviews/views/data                                 (array $data, string $view): array
    site-reviews/views/file                                 (string $filePath, string $view, array $data): string
    site-reviews/whip/methods                               (int $bitwiseSeparatedConstants): int
    site-reviews/whip/whitelist                             (array $whitelist): array
    site-reviews/whip/whitelist/cloudflare                  (bool $isUsingCloudflare): bool
