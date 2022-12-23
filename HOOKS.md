## Actions

    site-reviews/addon/register                                 (Application $app)
    site-reviews/addon/settings/<tab>                           (string $rows)
    site-reviews/builder                                        (Modules\Html\Builder $builder)
    site-reviews/customize/<style>                              (Modules\Html\Builder $builder)
    site-reviews/database/sql                                   (string $sql, string $handle)
    site-reviews/database/sql/<handle>                          (string $sql)
    site-reviews/defaults                                       (Contracts\DefaultsContract $defaults, string $hook, string $method, array $values)
    site-reviews/export/cleanup                                 ()
    site-reviews/get/review                                     (Review $review, int $reviewId)
    site-reviews/get/reviews                                    (array $reviews, array $args)
    site-reviews/migration/end                                  (array $migrations)
    site-reviews/migration/start                                (array $migrations)
    site-reviews/personal-data/erase                            (Review $review, bool $retainReview)
    site-reviews/review/approved                                (Review $review, string $prevStatus)
    site-reviews/review/build/before                            (Review $review, Modules\Html\ReviewHtml $reviewHtml)
    site-reviews/review/create                                  (int $postId, Commands\CreateReview $command)
    site-reviews/review/created                                 (Review $review, Commands\CreateReview $command)
    site-reviews/review/request                                 (Request $request)
    site-reviews/review/responded                               (Review $review, string $response)
    site-reviews/review/saved                                   (Review $review, array $submittedValues)
    site-reviews/review/unapproved                              (Review $review, string $prevStatus)
    site-reviews/review/updated/post_ids                        (Review $review, array $assignedPostIds)
    site-reviews/review/updated/user_ids                        (Review $review, array $assignedUserIds)
    site-reviews/route/<request_type>/<route_action>            (Request $request)
    site-reviews/settings/updated                               (array $options, array $settings)
    site-reviews/whip                                           (Vectorface\Whip\Whip $whip)
    site-reviews/woocommerce/render/loop/rating                 ()
    site-reviews/woocommerce/render/product/reviews             ()
    site-reviews/woocommerce/rest-api/delete_review             (Review $review, \WP_REST_Response $response, \WP_REST_Request $request)
    site-reviews/woocommerce/rest-api/insert_product_review     (Review $review, \WP_REST_Response $response, bool $isCreating)


## Filters

    site-reviews/addon/api-url                                  (string $apiUrl): string
    site-reviews/addon/documentation                            (array $documentation): array
    site-reviews/addon/documentation/tabs                       (array $tabs): array
    site-reviews/addon/settings                                 (array $settings): array
    site-reviews/addon/settings/tabs                            (array $tabs): array
    site-reviews/addon/submenu/callback                         (callable $callable, string $slug): callable
    site-reviews/addon/submenu/pages                            (array $args): array
    site-reviews/addon/subsubsub                                (array $subsubsub): array
    site-reviews/addon/sync/enable                              (bool $enable): bool
    site-reviews/addon/sync/services                            (array $services): array
    site-reviews/addon/system-info                              (array $settings): array
    site-reviews/addon/system-info/purge                        (array $keys): array
    site-reviews/addon/tools/tabs                               (array $tabs): array
    site-reviews/addon/types                                    (array $types): array
    site-reviews/addon/welcome/tabs                             (array $tabs): array
    site-reviews/api/base_url                                   (string $url): string
    site-reviews/assets/css                                     (bool $loadCss): bool
    site-reviews/assets/js                                      (bool $loadJs): bool
    site-reviews/assigned_posts/parent_id                       (int $postId): int
    site-reviews/assigned_posts/post_id                         (int $postId): int
    site-reviews/assigned_users/author_id                       (int $userId): int
    site-reviews/assigned_users/profile_id                      (int $userId): int
    site-reviews/assigned_users/user_id                         (int $userId): int
    site-reviews/async-scripts                                  (array $scripts): array
    site-reviews/avatar/attributes                              (array $attributes, Review $review): array
    site-reviews/avatar/colors                                  (array $colors): array
    site-reviews/avatar/fallback                                (string $fallbackUrl, int $size, Review $review): string
    site-reviews/avatar/generate                                (string $avatarUrl, int $size, Review $review): string
    site-reviews/avatar/id_or_email                             (string $id_or_email, array $reviewValues): string
    site-reviews/block/<block>/attributes                       (array $attributes): array
    site-reviews/build/template/<template_path>                 (string $template, array $data): string
    site-reviews/builder/<field_type>/args                      (array $args, Modules\Html\Builder $builder): array
    site-reviews/builder/<tag>/args                             (array $args, Modules\Html\Builder $builder): array
    site-reviews/builder/enable/optgroup                        (bool $enable): bool
    site-reviews/builder/field/<field_type>                     (string $className): string
    site-reviews/builder/result                                 (string $result, Modules\Html\Builder $builder): string
    site-reviews/capabilities                                   (array $capabilities): array
    site-reviews/column/<column>                                (string $className): string
    site-reviews/columns/<column_slug>                          (string $value, int $postId): string
    site-reviews/columns/orderby-is-null                        (array $columns): array
    site-reviews/config                                         (string $path): string
    site-reviews/config/<config_id>                             (array $config): array
    site-reviews/console/depth                                  (int $depth): int
    site-reviews/console/level                                  (int $level): int
    site-reviews/const/<constant>                               (string $constantName): string
    site-reviews/create/review-values                           (array $reviewValues, Commands\CreateReview $command): array
    site-reviews/database/sql/<handle>                          (string $sql): string
    site-reviews/deactivate/insight                             (array $insight, array $systemInfo): array
    site-reviews/deactivate/insight/display                     (array $insight, array $systemInfo): array
    site-reviews/deactivate/plugins                             (array $plugins): array
    site-reviews/deactivate/reasons                             (array $reasons): array
    site-reviews/defaults/<defaults_name>                       (array $defaults, string $method, array $args): array
    site-reviews/defaults/<defaults_name>/casts                 (array $casts, string $method): array
    site-reviews/defaults/<defaults_name>/concatenated          (array $concatenated, string $method): array
    site-reviews/defaults/<defaults_name>/defaults              (array $defaults): array
    site-reviews/defaults/<defaults_name>/guarded               (array $guarded, string $method): array
    site-reviews/defaults/<defaults_name>/mapped                (array $mapped, string $method): array
    site-reviews/defaults/<defaults_name>/sanitize              (array $sanitize, string $method): array
    site-reviews/defer-scripts                                  (array $scripts): array
    site-reviews/devmode                                        (bool $bool): bool
    site-reviews/documentation/faq                              (array $paths): array
    site-reviews/documentation/functions                        (array $paths): array
    site-reviews/documentation/hooks                            (array $paths): array
    site-reviews/documentation/shortcodes                       (array $paths): array
    site-reviews/documentation/shortcodes/<shortcode>           (array $paths): array
    site-reviews/email/compose                                  (array $email, Modules\Email $mailer): array
    site-reviews/email/headers                                  (array $headers, Modules\Email $mailer): array
    site-reviews/email/message                                  (string $message, string $type, Modules\Email $mailer): string
    site-reviews/enqueue/admin/dependencies                     (array $dependencies): array
    site-reviews/enqueue/admin/inline-script                    (string $optimizedScript, string $script, array $variables): string
    site-reviews/enqueue/admin/inline-script/after              (string $javascript): string
    site-reviews/enqueue/admin/localize                         (array $variables): array
    site-reviews/enqueue/public/dependencies                    (array $dependencies): array
    site-reviews/enqueue/public/inline-script                   (string $optimizedScript, string $script, array $variables): string
    site-reviews/enqueue/public/inline-script/after             (string $javascript): string
    site-reviews/enqueue/public/inline-styles                   (string $css): string
    site-reviews/enqueue/public/localize                        (array $variables): array
    site-reviews/enqueue/public/localize/ajax-pagination        (array $selectors): array
    site-reviews/field/<field_type>                             (array $field): array
    site-reviews/form/build/<tag_or_field_key>                  (string $field, Arguments $with, Modules\Html\Partials\SiteReviewsForm $partial): string
    site-reviews/get/defaults                                   (array $defaults): array
    site-reviews/integration/elementor/display/settings         (array $settings, \Elementor\Widget_Base $widget): array
    site-reviews/integration/elementor/register/controls        (array $controls, \Elementor\Widget_Base $widget): array
    site-reviews/interpolate/<template_path>                    (array $context, string $template, array $data): array
    site-reviews/is-local-server                                (bool $bool): bool
    site-reviews/metabox/fields                                 (array $fields, Review $review): array
    site-reviews/metabox/fields/order                           (array $order): array
    site-reviews/notices                                        (array $notices): array
    site-reviews/notification/emails                            (array $emails, Review $review): array
    site-reviews/notification/tag/<tag>                         (string $value, Review $review): string
    site-reviews/notification/title                             (string $title, Review $review): string
    site-reviews/optimize/css                                   (bool $bool): bool
    site-reviews/optimize/js                                    (bool $bool): bool
    site-reviews/optimized/scripts                              (array $handles): array
    site-reviews/optimized/styles                               (array $handles): array
    site-reviews/option/<path>                                  (mixed $value): mixed
    site-reviews/paginate_link                                  (array $link, array $args, Modules\Html\Builder $builder, Modules\Paginate $paginate): array
    site-reviews/paginate_links                                 (string $links, array $args): string
    site-reviews/partial/args/<partial_path>                    (array $args): array
    site-reviews/partial/classname                              (string $className, string $partialPath, array $args): string
    site-reviews/path                                           (string $path, string $file): string
    site-reviews/personal-data/erase-all                        (bool $eraseAll): bool
    site-reviews/personal-data/export                           (array $data, Review $review): array
    site-reviews/post/is-published                              (bool $isPublished, int|\WP_Post $postId): bool
    site-reviews/query/sql/and                                  (array $whereAnd, string $handle, Database\Query $query): array
    site-reviews/query/sql/clause/operator                      (string $clauseOperator, array $clauses, array $args): string
    site-reviews/query/sql/join                                 (array $join, string $handle, Database\Query $query): array
    site-reviews/query/sql/limit                                (string $limit, string $handle, Database\Query $query): string
    site-reviews/query/sql/offset                               (string $offset, string $handle, Database\Query $query): string
    site-reviews/query/sql/order-by                             (array $orderBy, string $handle, Database\Query $query): array
    site-reviews/rating/average                                 (float $roundedAverage, array $ratingCounts, int $average): float
    site-reviews/rating/ranking                                 (float $ranking, array $ratingCounts, Modules\Rating $rating): float
    site-reviews/rest-api/reviews/parameters                    (array $parameters): array
    site-reviews/rest-api/reviews/prepare/<key>                 (mixed $value, Controllers\Api\Version1\Response\Prepare $prepare): array
    site-reviews/rest-api/reviews/properties                    (array $properties): array
    site-reviews/rest-api/summary/parameters                    (array $parameters): array
    site-reviews/rest-api/summary/properties                    (array $properties): array
    site-reviews/roles                                          (array $roles): array
    site-reviews/captcha/language                               (string $locale): string
    site-reviews/render/view                                    (string $view, array $data): string
    site-reviews/rendered/field                                 (string $field, string $fieldType, array $field): string
    site-reviews/rendered/field/classes                         (array $classes, array $field): array
    site-reviews/rendered/partial                               (string $partial, string $partialPath, array $args): string
    site-reviews/rendered/partial/<partial_path>                (string $partial, array $args): string
    site-reviews/rendered/template                              (string $template, string $templatePath, array $data): string
    site-reviews/rendered/template/<template_path>              (string $template, array $data): string
    site-reviews/review-form/fields                             (array $fields, Arguments $args): array
    site-reviews/review-form/fields/hidden                      (array $fields, Arguments $args): array
    site-reviews/review-form/fields/normalized                  (array $fields, Arguments $args): array
    site-reviews/review-form/order                              (array $order): array
    site-reviews/review-form/referer                            (string $referer): string
    site-reviews/review-table/clauses                           (array $clauses, string $ratingTable, \WP_Query $query): array
    site-reviews/review/build/tag/<tag_or_field_key>            (string $field, string $value, Review $review, Modules\Html\ReviewHtml $reviewHtml): string
    site-reviews/review/build/tag/response/by                   (string $responseBy, Review $review): string
    site-reviews/review/build/after                             (array $templateTags, Review $review, Modules\Html\ReviewHtml $reviewHtml): array
    site-reviews/review/build/context                           (array $context, Review $review, Modules\Html\ReviewHtml $reviewHtml): array
    site-reviews/review/call/<methodName>                       (Review $review, ...$args): void|mixed
    site-reviews/review/redirect                                (string $redirect, Commands\CreateReview $createReview, Review $review): string
    site-reviews/review/tag/<tag>                               (string $className, Modules\Html\ReviewHtml $reviewHtml): string
    site-reviews/review/value/<tag_or_field_key>                (string $value, Contracts\TagContract $tag): string
    site-reviews/review/wrapped                                 (string $value, string $rawValue, Contracts\TagContract $tag): string
    site-reviews/review/wrap/<tag_or_field_key>                 (string $value, string $rawValue, Contracts\TagContract $tag): string
    site-reviews/reviews/fallback                               (string $fallback, array $args): string
    site-reviews/reviews/html/<property>                        (mixed $value, Modules\Html\ReviewHtml $reviewHtml): mixed
    site-reviews/route/request                                  (array $request, string $action, string $requestType): array
    site-reviews/router/admin/unguarded-actions                 (array $unguardedActions): array
    site-reviews/router/public/unguarded-actions                (array $unguardedActions): array
    site-reviews/sanitize/allowed-html                          (array $allowedHtml, Modules\Sanitizer $sanitizer): array
    site-reviews/scheduler/per-page                             (int $perPage): int
    site-reviews/schema/<schema_type>                           (array $schema, array $args): array
    site-reviews/schema/all                                     (array $schemas): array
    site-reviews/schema/is-empty                                (bool $isEmpty): bool
    site-reviews/schema/review                                  (array $schema, Review $review, array $args): array
    site-reviews/search/posts/post_status                       (array $postStatuses, string $searchType): array
    site-reviews/search/posts/post_type                         (array $postTypes): array
    site-reviews/settings/sanitize                              (array $options, array $settings): array
    site-reviews/shortcode/<shortcode>/attributes               (array $attributes, Shortcodes\Shortcode $shortcode): array
    site-reviews/shortcode/args                                 (array $args, string $type, string $partialName): array
    site-reviews/shortcode/atts                                 (array $atts, string $type, string $partialName): array
    site-reviews/shortcode/display-options                      (array $displayOptions, string $shortcode): array
    site-reviews/shortcode/hide-options                         (array $hideOptions, string $shortcode): array
    site-reviews/slack/compose                                  (array $notification, Modules\Slack $slack): array
    site-reviews/slack/stars                                    (string $stars, int $rating, int $maxRating): string
    site-reviews/style                                          (string $style): string
    site-reviews/style/views                                    (array $views): array
    site-reviews/summary/build/<tag_or_field_key>               (string $field, array $ratings, Modules\Html\Partials\SiteReviewsSummary $partial): string
    site-reviews/summary/counts                                 (string $ratingCount, int $ratingLevel): string
    site-reviews/summary/tag/<tag>                              (string $className, Shortcodes\SiteReviewsSummaryShortcode $shortcode): string
    site-reviews/summary/value/<tag_or_field_key>               (string $value, Contracts\TagContract $tag): string
    site-reviews/summary/wrap/<tag_or_field_key>                (string $value, string $rawValue, Contracts\TagContract $tag): string
    site-reviews/support/deprecated/v5                          (bool $supportDeprecated): bool
    site-reviews/system/<key>                                   (array $details): array
    site-reviews/tinymce/editor-ids                             (array $editorIds, string $editorId): array
    site-reviews/tools/general                                  (array $paths): array
    site-reviews/translation/entries                            (array $entries): array
    site-reviews/translator/domains                             (array $domains): array
    site-reviews/updater/force-check                            (bool $force): bool
    site-reviews/url                                            (string $url, string $path): string
    site-reviews/validate/akismet                               (bool $isValid, array $submission, array $response): bool
    site-reviews/validate/akismet/is-active                     (bool $isActive): bool
    site-reviews/validate/akismet/submission                    (array $submission, Request $request): array
    site-reviews/validate/blacklist                             (bool $isValid, string $target, Request $request): bool
    site-reviews/validate/custom                                (bool $isValid, Request $request): bool|string
    site-reviews/validate/duplicate                             (bool $isValid, Request $request): bool
    site-reviews/validate/honeypot                              (bool $isValid, Request $request): bool
    site-reviews/validate/review-limits                         (bool $isValid, Reviews $reviews, Request $request, string $key): bool
    site-reviews/validation/rules                               (array $rules, Request $request): array
    site-reviews/validation/rules/normalized                    (array $rules, Request $request, array $defaults): array
    site-reviews/validators                                     (array $validatorClasses): array
    site-reviews/views/data                                     (array $data, string $view): array
    site-reviews/views/file                                     (string $filePath, string $view, array $data): string
    site-reviews/whip/methods                                   (int $bitwiseSeparatedConstants): int
    site-reviews/whip/whitelist                                 (array $whitelist): array
    site-reviews/whip/whitelist/cloudflare                      (bool $isUsingCloudflare): bool
    site-reviews/woocommerce/rest-api/prepare_product_review    (\WP_REST_Response $response, Review $review, \WP_REST_Request $request)
