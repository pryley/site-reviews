## Actions

    site-reviews/addon/register                             ()
    site-reviews/addon/settings/<tab>                       ()
    site-reviews/builder                                    ()
    site-reviews/customize/<style>                          ()
    site-reviews/database/sql                               (string $sql, string $handle)
    site-reviews/database/sql/<handle>                      (string $sql)
    site-reviews/defaults                                   (DefaultsAbstract $defaultsClass, string $hook, string $method)
    site-reviews/export/cleanup                             ()
    site-reviews/get/review                                 ()
    site-reviews/get/reviews                                ()
    site-reviews/personal-data/erase                        (Review $review, bool $retainReview)
    site-reviews/review/build/before                        (Review $review)
    site-reviews/review/create                              (int $postId, CreateReview $command)
    site-reviews/review/created                             (Review $review, CreateReview $command)
        site-reviews/review/reverted                            ()
    site-reviews/review/saved                               (Review $review, array $submittedValues)
    site-reviews/review/updated/post_ids                    (Review $review, array $assignedPostIds)
    site-reviews/review/updated/user_ids                    (Review $review, array $assignedUserIds)
        ?? site-reviews/review/updated
    site-reviews/route/<request_type>/<route_action>        (Request $request)
    site-reviews/whip                                       ()

## Filters

    site-reviews/addon/api-url                              (string $apiUrl): string
    site-reviews/addon/documentation                        (): array
    site-reviews/addon/documentation/tabs                   (): array
    site-reviews/addon/settings                             (): array
    site-reviews/addon/settings/tabs                        (): array
    site-reviews/addon/submenu/callback                     (): Closure
    site-reviews/addon/submenu/pages                        (): array
    site-reviews/addon/sync/enable                          (): bool
    site-reviews/addon/sync/services                        (): array
    site-reviews/addon/system-info                          (): array
    site-reviews/addon/tools/tabs                           (): array
    site-reviews/addon/types                                (): array
    site-reviews/addon/welcome/tabs                         (): array
    site-reviews/avatar/fallback                            (string $fallbackUrl, int $size): string
    site-reviews/assets/css                                 (): bool
    site-reviews/assets/js                                  (): bool
    site-reviews/assets/polyfill                            (): bool
    site-reviews/async-scripts                              (): array
    site-reviews/block/<block>/attributes                   (): array
    site-reviews/build/template/<template_path>             (): string
    site-reviews/builder/field/<field_type>                 (string $className): string
        !! site-reviews/builder/field/<field_type>/args
        !! site-reviews/builder/field/classname
    site-reviews/builder/result                             (string $result, Builder $builder): string
    site-reviews/builder/<tag>/args                         (array $args, Builder $builder): array
    site-reviews/capabilities                               (array $capabilities): array
    site-reviews/capabilities/for-roles                     (array $capabilities): array
    site-reviews/column/<column>                            (string $className): string
    site-reviews/columns/<column_slug>                      (string $value, int $postId): string
    site-reviews/columns/orderby-is-null                    (array $columns): array
    site-reviews/config                                     (string $configPath): string
    site-reviews/config/<config_id>                         (array $config): array
    site-reviews/console/level                              (int $level): int
    site-reviews/const/<constant>                           (string $constantName): string
    site-reviews/create/review-values                       (array $reviewValues, CreateReview $command): array
    site-reviews/defaults/<defaults_name>                   (): array
    site-reviews/defaults/custom/sanitize                   (array $sanitize): array
    site-reviews/defer-scripts                              (): array
    site-reviews/email/compose                              (): array
    site-reviews/email/headers                              (): array
    site-reviews/email/message                              (): string
    site-reviews/enqueue/admin/dependencies                 (): array
    site-reviews/enqueue/admin/localize                     (): array
    site-reviews/enqueue/public/dependencies                (): array
    site-reviews/enqueue/public/inline-script               (): string
    site-reviews/enqueue/public/localize                    (): array
    site-reviews/enqueue/public/localize/ajax-pagination    (): array
    site-reviews/field/<field_type>                         (): array
    site-reviews/form/build/<tag_or_field_key>              (string $field, Arguments $with, Modules\Html\Partials\SiteReviewsForm $partial): string
    site-reviews/get/defaults                               (): array
    site-reviews/interpolate/<template_path>                (): array
    site-reviews/metabox/details                            (): array
    site-reviews/notices                                    (): string
    site-reviews/notification/emails                        (): array
    site-reviews/notification/title                         (): string
    site-reviews/paginate_links                             (string $links, array $args): string
    site-reviews/partial/args/<partial_path>                (): array
    site-reviews/partial/classname                          (): string
    site-reviews/path                                       (string $path, string $file): string
    site-reviews/personal-data/erase-all                    (bool $eraseAll): bool
    site-reviews/personal-data/export                       (array $data, Review $review): array
    site-reviews/query/sql/clause/operator                  (): string
    site-reviews/query/sql/from                             (): string
    site-reviews/query/sql/group-by                         (): string
    site-reviews/query/sql/join                             (): array
    site-reviews/query/sql/limit                            (): string
    site-reviews/query/sql/offset                           (): string
    site-reviews/query/sql/order-by                         (): string
    site-reviews/query/sql/select                           (): array
    site-reviews/query/sql/where                            (): array
    site-reviews/rating/average                             (): float
    site-reviews/rating/ranking                             (): float
    site-reviews/recaptcha/language                         (string $locale): string
    site-reviews/recaptcha/timeout                          (): int
    site-reviews/render/view                                (string $view array $data): string
    site-reviews/rendered/field                             (): string
    site-reviews/rendered/field/classes                     (): array
    site-reviews/rendered/partial                           (): string
    site-reviews/rendered/partial/<partial_path>            (): string
    site-reviews/rendered/template                          (): string
    site-reviews/rendered/template/<template_path>          (): string
    site-reviews/review/build/after                         (array $templateTags, Review $review, Modules\Html\ReviewHtml $reviewHtml): array
    site-reviews/review/build/<tag_or_field_key>            (string $field, string $value, Review $review, Modules\Html\ReviewHtml $reviewHtml): string
    site-reviews/review/tag/<tag>                           (string $className): string
    site-reviews/review/redirect                            (): string
    site-reviews/review/value/<tag_or_field_key>            (string $value, Modules\Html\Tags\<Tag> $tag): string
    site-reviews/review/wrap/<tag_or_field_key>             (string $value, Review $review, string $rawValue, Modules\Html\Tags\<Tag> $tag): string
    site-reviews/reviews/fallback                           (): string
    site-reviews/route/request                              (): array
    site-reviews/router/unguarded-actions                   (): array
    site-reviews/schema/all                                 (): array
    site-reviews/schema/review                              (): array
    site-reviews/schema/<schema_type>                       (): array
    site-reviews/settings/callback                          (): array
    site-reviews/shortcode/args                             (): array
    site-reviews/shortcode/atts                             (): array
    site-reviews/shortcode/hide-options                     (): array
    site-reviews/slack/compose                              (): array
    site-reviews/slack/stars                                (): string
    site-reviews/sslverify/post                             (): bool
    site-reviews/style                                      (): string
    site-reviews/style/views                                (): array
    site-reviews/submission-form/order                      (): array
    site-reviews/summary/build/<tag_or_field_key>           (string $field, array $ratings, Modules\Html\Partials\SiteReviewsSummary $partial): string
    site-reviews/summary/counts                             (): string
    site-reviews/summary/value/<tag_or_field_key>           (string $value, Modules\Html\Tags\<Tag> $tag): string
    site-reviews/summary/wrap/<tag_or_field_key>            (string $value, array $ratings, string $rawValue, Modules\Html\Tags\<Tag> $tagClass): string
    site-reviews/support/deprecated/v4                      (): bool
    site-reviews/support/multibyte                          (): bool
    site-reviews/system/<key>                               (): array
    site-reviews/tinymce/editor-ids                         (): array
    site-reviews/translation/entries                        (): array
    site-reviews/translator/domains                         (): array
    site-reviews/trustalyze/response                        (): array
    site-reviews/trustalyze/review                          (): array
    site-reviews/url                                        (string $url, string $path): string
    site-reviews/validators                                 (array $validatorClasses): array
    site-reviews/validate/akismet                           (): bool
    site-reviews/validate/akismet/is-active                 (): bool
    site-reviews/validate/akismet/submission                (): array
    site-reviews/validate/blacklist                         (): bool
    site-reviews/validate/custom                            (): bool|string
    site-reviews/validate/honeypot                          (): bool
    site-reviews/validate/review-limits                     (): bool
    site-reviews/validation/rules                           (): array
    site-reviews/views/data                                 (): array
    site-reviews/views/file                                 (string $filePath, string $view, array $data): string
    site-reviews/whip/methods                               (): int
    site-reviews/whip/whitelist                             (): array
    site-reviews/whip/whitelist/cloudflare                  (): bool
