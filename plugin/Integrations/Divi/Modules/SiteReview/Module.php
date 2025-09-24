<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReview;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\Framework\Utility\PostUtility;
use ET\Builder\Framework\Utility\SanitizerUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\FrontEnd\Module\Script;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\IconLibrary\IconFont\Utils;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Layout\Components\MultiView\MultiViewScriptData;
use ET\Builder\Packages\Module\Layout\Components\MultiView\MultiViewUtils;
use ET\Builder\Packages\Module\Layout\Components\StyleCommon\CommonStyle;
use ET\Builder\Packages\Module\Module as DiviModule;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\Module\Options\Text\TextClassnames;
use ET\Builder\Packages\Module\Options\Text\TextStyle;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use ET\Builder\Packages\ModuleUtils\ModuleUtils;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleClassnamesDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleScriptDataDefaults;
use GeminiLabs\SiteReviews\Integrations\Divi\Defaults\ModuleStylesDefaults;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class Module implements DependencyInterface
{
    /**
     * Track rendering to prevent unnecessary rendering and recursion.
     */
    protected static bool $_rendering = false;
    protected static bool $_rendering_content = false;

    public function load(): void
    {
        add_filter('divi_conversion_presets_attrs_map', [PresetAttrsMap::class, 'get_map'], 10, 2);
        add_filter('divi_option_group_preset_resolver_attr_name', [PresetAttrsResolver::class, 'resolve'], 10, 2);
        add_action('init', function () {
            $modulePath = glsr()->path('assets/divi/modules-json/site_review/');
            ModuleRegistration::register_module($modulePath, [
                'render_callback' => [static::class, 'render_callback'],
            ]);
        });
    }

    /**
     * This method is equivalent to "custom-css.ts".
     */
    public static function custom_css(): array
    {
        return \WP_Block_Type_Registry::get_instance()->get_registered('glsr-divi/blog')->customCssFields;
    }

    /**
     * Filter the pagination url to add a flag so it can be filtered to avoid pagination clashes with the main query.
     *
     * @filter get_pagenum_link
     */
    public static function filter_pagination_url(string $result): string
    {
        return add_query_arg('et_blog', '', $result);
    }

    /**
     * This method is equivalent to "module-classnames.ts".
     */
    public static function module_classnames(array $args): void
    {
        $args = glsr(ModuleClassnamesDefaults::class)->merge($args);
        $fullwidth = $args['attrs']['fullwidth']['advanced']['enable']['desktop']['value'] ?? 'on';
        $args['classnamesInstance']->add(
            TextClassnames::text_options_classnames($args['attrs']['module']['advanced']['text'] ?? []),
            true
        );
        if ('on' === $fullwidth) {
            $args['classnamesInstance']->add('et_pb_posts', true);
        } else {
            $args['classnamesInstance']->add('et_pb_blog_grid_wrapper', true);
        }
        // Module
        $args['classnamesInstance']->add(
            ElementClassnames::classnames([
                'attrs' => array_merge(
                    $args['attrs']['module']['decoration'] ?? [],
                    [
                        'border' => $args['attrs']['post']['decoration']['border'] ?? $args['attrs']['fullwidth']['decoration']['border'] ?? [],
                        'link' => $args['attrs']['module']['advanced']['link'] ?? [],
                    ]
                ),
            ])
        );
    }

    /**
     * This method is equivalent to "module-script-data.tsx".
     */
    public static function module_script_data(array $args): void
    {
        $args = glsr(ModuleScriptDataDefaults::class)->merge($args);
        $dateFormat = $attrs['post']['advanced']['dateFormat']['desktop']['value'] ?? '';
        $decoration = $args['attrs']['module']['decoration'] ?? [];
        $postIds = Arr::consolidate($args['post_ids'] ?? []);
        // Element Script Data Options
        $elements->script_data([
            'attrName' => 'module',
        ]);
        // Post meta set content
        $setContent = [];
        foreach ($postIds as $postId) {
            $setContent[] = [
                'data' => MultiViewUtils::merge_values([
                    'showAuthor' => $attrs['meta']['advanced']['showAuthor'] ?? [],
                    'showCategories' => $attrs['meta']['advanced']['showCategories'] ?? [],
                    'showComments' => $attrs['meta']['advanced']['showComments'] ?? [],
                    'showDate' => $attrs['meta']['advanced']['showDate'] ?? [],
                ]),
                'sanitizer' => 'et_core_esc_previously',
                'selector' => "{$args['selector']} .et_pb_post_id_{$postId} .post-meta",
                'valueResolver' => function ($value) use ($dateFormat, $postId) {
                    return static::render_meta([
                        'dateFormat' => $dateFormat,
                        'post_id' => $postId,
                        'show_author' => 'on' === ($value['showAuthor'] ?? ''),
                        'show_categories' => 'on' === ($value['showCategories'] ?? ''),
                        'show_comments' => 'on' === ($value['showComments'] ?? ''),
                        'show_date' => 'on' === ($value['showDate'] ?? ''),
                    ]);
                },
            ];
            // Post excerpt
            $setContent[] = [
                'data' => MultiViewUtils::merge_values([
                    'excerptContent' => $attrs['post']['advanced']['excerptContent'] ?? [],
                    'excerptLength' => $attrs['post']['advanced']['excerptLength'] ?? [],
                    'excerptManual' => $attrs['post']['advanced']['excerptManual'] ?? [],
                    'showExcerpt' => $attrs['post']['advanced']['showExcerpt'] ?? [],
                ]),
                'sanitizer' => 'wp_kses_post',
                'selector' => "{$args['selector']} .et_pb_post_id_{$postId} .post-content-inner",
                'valueResolver' => function ($value) use ($postId) {
                    return static::render_content([
                        'excerpt_content' => $value['excerptContent'] ?? '',
                        'excerpt_length' => $value['excerptLength'] ?? '',
                        'excerpt_manual' => $value['excerptManual'] ?? '',
                        'post_id' => $postId,
                        'show_excerpt' => $value['showExcerpt'] ?? '',
                    ]);
                },
            ];
        }
        MultiViewScriptData::set([
            'hoverSelector' => $args['selector'],
            'id' => $args['id'],
            'name' => $args['name'],
            'setContent' => $setContent,
            'setVisibility' => [
                [
                    'data' => $attrs['image']['advanced']['enable'] ?? [],
                    'selector' => "{$args['selector']} .entry-featured-image-url",
                    'valueResolver' => fn ($value) => 'on' === $value ? 'visible' : 'hidden',
                ],
                [
                    'data' => $attrs['readMore']['advanced']['enable'] ?? [],
                    'selector' => "{$args['selector']} .more-link",
                    'valueResolver' => fn ($value) => 'on' === $value ? 'visible' : 'hidden',
                ],
                [
                    'data' => $attrs['pagination']['advanced']['enable'] ?? [],
                    'selector' => "{$args['selector']} .pagination",
                    'valueResolver' => fn ($value) => 'on' === $value ? 'visible' : 'hidden',
                ],
            ],
            'storeInstance' => $args['storeInstance'],
        ]);
    }

    /**
     * This method is equivalent to "module-styles.tsx".
     */
    public static function module_styles(array $args): void
    {
        $args = glsr(ModuleStylesDefaults::class)->merge($args);
        $attrs = $args['attrs'];
        $elements = $args['elements'];
        $settings = $args['settings'];
        Style::add([
            'id' => $args['id'],
            'name' => $args['name'],
            'orderIndex' => $args['orderIndex'],
            'storeInstance' => $args['storeInstance'],
            'styles' => [
                // Module
                $elements->style([
                    'attrName' => 'module',
                    'styleProps' => [
                        'defaultPrintedStyleAttrs' => $args['defaultPrintedStyleAttrs']['module']['decoration'] ?? [],
                        'disabledOn' => [
                            'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                        ],
                    ],
                ]),
                TextStyle::style([
                    'attr' => $attrs['module']['advanced']['text'] ?? [],
                    'orderClass' => $args['orderClass'],
                    'selector' => $args['orderClass'],
                ]),
                // Image
                $elements->style([
                    'attrName' => 'image',
                ]),
                CommonStyle::style([
                    'attr' => $attrs['image']['decoration']['border'] ?? [],
                    'declarationFunction' => [static::class, 'overflow_style_declaration'],
                    'orderClass' => $args['orderClass'],
                    'selector' => "{$args['orderClass']} .et_pb_post .entry-featured-image-url, {$args['orderClass']} .et_pb_post .et_pb_slides, {$args['orderClass']} .et_pb_post .et_pb_video_overlay",
                ]),
                // Title
                $elements->style([
                    'attrName' => 'title',
                ]),
                // Meta
                $elements->style([
                    'attrName' => 'meta',
                ]),
                // Content
                $elements->style([
                    'attrName' => 'content',
                ]),
                // Read more
                $elements->style([
                    'attrName' => 'readMore',
                ]),
                // Post Item
                $elements->style([
                    'attrName' => 'post',
                ]),
                CommonStyle::style([
                    'attr' => $attrs['post']['decoration']['border'] ?? [],
                    'declarationFunction' => [static::class, 'overflow_style_declaration'],
                    'orderClass' => $args['orderClass'],
                    'selector' => "{$args['orderClass']} .et_pb_post",
                ]),
                // Fullwidth
                $elements->style([
                    'attrName' => 'fullwidth',
                ]),
                CommonStyle::style([
                    'attr' => $attrs['fullwidth']['decoration']['border'] ?? [],
                    'declarationFunction' => [static::class, 'overflow_style_declaration'],
                    'orderClass' => $args['orderClass'],
                    'selector' => "{$args['orderClass']}:not(.et_pb_blog_grid_wrapper) .et_pb_post",
                ]),
                // Overlay
                $elements->style([
                    'attrName' => 'overlay',
                ]),
                // Overlay Icon
                $elements->style([
                    'attrName' => 'overlayIcon',
                ]),
                // Masonry
                $elements->style([
                    'attrName' => 'masonry',
                ]),
                // Pagination
                $elements->style([
                    'attrName' => 'pagination',
                ]),
                // Placed the very end only for custom css
                CssStyle::style([
                    'attr' => $attrs['css'] ?? [],
                    'cssFields' => static::custom_css(),
                    'selector' => $args['orderClass'],
                ]),
            ],
        ]);
    }

    /**
     * This method is equivalent to "style-declarations/border/index.ts".
     */
    public static function overflow_style_declaration(array $params): string
    {
        $radius = $params['attrValue']['radius'] ?? [];
        $style = new StyleDeclarations([
            'important' => false,
            'returnType' => 'string',
        ]);
        if (empty($radius)) {
            return $style->value();
        }
        $allCornersZero = true;
        foreach ($radius as $corner => $value) {
            if ('sync' === $corner) {
                continue;
            }
            $corner_value = SanitizerUtility::numeric_parse_value($value ?? '');
            if (0.0 !== ($corner_value['valueNumber'] ?? 0.0)) {
                $allCornersZero = false;
                break;
            }
        }
        if (!$allCornersZero) {
            $style->add('overflow', 'hidden');
        }
        return $style->value();
    }

    /**
     * Processes the data for a single post and returns the HTML for that post.
     * This function is responsible for generating the HTML for a single post. It retrieves the post's ID, checks if the post has a thumbnail, and if the thumbnail should be shown. It then generates the HTML for the thumbnail, the post title, the post meta, and the post content. It also checks if a "Read More" link should be added to the post content. Finally, it generates the HTML for the entire post and returns it.
     *
     * @param \WP_Post $post        the post-object
     * @param array    $attrs       the attributes for the post
     * @param int      $order_index the order index of the post
     * @param int      $item_index  the items index of the post
     *
     * @return string the HTML for the post
     */
    public static function process_post_data(\WP_Post $post, array $attrs, int $order_index, int $item_index): string
    {
        $fullwidth = $attrs['fullwidth']['advanced']['enable']['desktop']['value'] ?? 'on';
        $date_format = $attrs['post']['advanced']['dateFormat']['desktop']['value'] ?? '';
        $excerpt_content = $attrs['post']['advanced']['excerptContent']['desktop']['value'] ?? 'off';
        $excerpt_length = $attrs['post']['advanced']['excerptLength']['desktop']['value'] ?? '270';
        $excerpt_manual = $attrs['post']['advanced']['excerptManual']['desktop']['value'] ?? 'on';
        $icon_value = Utils::process_font_icon($attrs['overlayIcon']['decoration']['icon']['desktop']['value'] ?? []);
        $show_excerpt = $attrs['post']['advanced']['showExcerpt']['desktop']['value'] ?? 'on';
        $show_overlay = 'on' === ($attrs['overlay']['advanced']['enable']['desktop']['value'] ?? 'off');
        $post_format = et_pb_post_format();
        $show_title_meta_content = 'off' === $fullwidth || !in_array($post_format, ['link', 'audio', 'quote'], true) || post_password_required($post);
        $show_thumbnail = ModuleUtils::has_value($attrs['image']['advanced']['enable'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        $show_read_more = ModuleUtils::has_value($attrs['readMore']['advanced']['enable'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        $show_author = ModuleUtils::has_value($attrs['meta']['advanced']['showAuthor'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        $show_date = ModuleUtils::has_value($attrs['meta']['advanced']['showDate'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        $show_categories = ModuleUtils::has_value($attrs['meta']['advanced']['showCategories'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        $show_comments = ModuleUtils::has_value($attrs['meta']['advanced']['showComments'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        $heading_level = $attrs['title']['decoration']['font']['font']['desktop']['value']['headingLevel'] ?? 'h2';
        $has_thumbnail = has_post_thumbnail() || 'attachment' === get_post_type();
        $post_thumb = '';
        if (!in_array($post_format, ['link', 'audio', 'quote'], true) || post_password_required($post)) {
            $thumb = '';
            $width = 'on' === $fullwidth ? 1080 : 400;
            $width = (int) apply_filters('et_pb_blog_image_width', $width);
            $height = 'on' === $fullwidth ? 675 : 250;
            $height = (int) apply_filters('et_pb_blog_image_height', $height);
            $class = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
            $alt = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
            $thumbnail_data = get_thumbnail($width, $height, $class, $alt, get_the_title(), false, 'Blogimage');
            $thumb = $thumbnail_data['thumb'];
            $first_video = PostUtility::get_first_video();
            if ('video' === $post_format && false !== $first_video) {
                $video_overlay = !empty($thumb)
                    ? HTMLUtility::render([
                        'attributes' => [
                            'class' => 'et_pb_video_overlay',
                            'style' => "background-image: url({$thumb}); background-size: cover;",
                        ],
                        'children' => HTMLUtility::render([
                            'attributes' => [
                                'class' => 'et_pb_video_overlay_hover',
                            ],
                            'children' => HTMLUtility::render([
                                'attributes' => [
                                    'class' => 'et_pb_video_play',
                                    'href' => '#',
                                ],
                                'tag' => 'a',
                            ]),
                            'tag' => 'div',
                        ]),
                        'childrenSanitizer' => 'et_core_esc_previously',
                        'tag' => 'div',
                    ])
                    : '';
                $post_thumb = HTMLUtility::render([
                    'attributes' => [
                        'class' => 'et_main_video_container',
                    ],
                    'children' => [
                        $video_overlay,
                        $first_video,
                    ],
                    'childrenSanitizer' => 'et_core_esc_previously',
                    'tag' => 'div',
                ]);
            } elseif ('gallery' === $post_format) {
                ob_start();
                et_pb_gallery_images('slider');
                $post_thumb = ob_get_clean();
            } elseif ($has_thumbnail && $show_thumbnail) {
                $post_thumbnail = HTMLUtility::render([
                    'attributes' => [
                        'href' => get_permalink(),
                        'class' => 'entry-featured-image-url',
                    ],
                    'childrenSanitizer' => 'et_core_esc_previously',
                    'children' => [
                        print_thumbnail($thumb, $thumbnail_data['use_timthumb'], get_the_title(), $width, $height, '', false),
                        HTMLUtility::render([
                            'attributes' => [
                                'class' => 'et_overlay et_pb_inline_icon',
                                'data-icon' => $icon_value,
                            ],
                            'tag' => 'span',
                        ]),
                    ],
                    'tag' => 'a',
                ]);
                $post_thumb = 'off' === $fullwidth
                    ? HTMLUtility::render([
                        'attributes' => [
                            'class' => 'et_pb_image_container',
                        ],
                        'children' => $post_thumbnail,
                        'childrenSanitizer' => 'et_core_esc_previously',
                        'tag' => 'div',
                    ])
                    : $post_thumbnail;
            }
        }
        $title = $show_title_meta_content && (!in_array($post_format, ['link', 'audio'], true) || post_password_required($post))
            ? HTMLUtility::render([
                'attributes' => [
                    'class' => 'entry-title',
                ],
                'children' => HTMLUtility::render([
                    'tag' => 'a',
                    'attributes' => [
                        'href' => get_the_permalink(),
                    ],
                    'children' => get_the_title(),
                ]),
                'childrenSanitizer' => 'et_core_esc_previously',
                'tag' => $heading_level,
            ])
            : '';
        $meta = $show_title_meta_content
            ? HTMLUtility::render([
                'attributes' => [
                    'class' => 'post-meta',
                ],
                'children' => static::render_meta([
                    'show_author' => $show_author,
                    'show_date' => $show_date,
                    'show_categories' => $show_categories,
                    'show_comments' => $show_comments,
                    'post_id' => $post->ID,
                    'date_format' => $date_format,
                ]),
                'childrenSanitizer' => 'et_core_esc_previously',
                'tag' => 'p',
            ])
            : '';
        $post_content_render = $show_title_meta_content
            ? HTMLUtility::render([
                'attributes' => [
                    'class' => 'post-content-inner',
                ],
                'children' => static::render_content([
                    'excerpt_content' => $excerpt_content,
                    'show_excerpt' => $show_excerpt,
                    'excerpt_manual' => $excerpt_manual,
                    'excerpt_length' => $excerpt_length,
                    'post_id' => $post->ID,
                ]),
                'childrenSanitizer' => 'wp_kses_post',
                'tag' => 'div',
            ])
            : '';
        $read_more = $show_read_more
            ? HTMLUtility::render([
                'attributes' => [
                    'href' => get_permalink(),
                    'class' => 'more-link',
                ],
                'children' => esc_html__('Read More', 'et_builder'),
                'tag' => 'a',
            ])
            : '';
        $content = HTMLUtility::render([
            'attributes' => [
                'class' => 'post-content',
            ],
            'children' => [
                $post_content_render,
                $read_more,
            ],
            'childrenSanitizer' => 'et_core_esc_previously',
            'tag' => 'div',
        ]);
        $post_id_class = 'et_pb_post_id_'.$post->ID;
        // add item order index class.
        $item_class = sprintf(' et_pb_blog_item_%1$s_%2$s', (int) $order_index, (int) $item_index);
        // Post format content.
        ob_start();
        et_divi_post_format_content();
        $post_format_content = ob_get_clean();
        return HTMLUtility::render([
            'attributes' => [
                'class' => HTMLUtility::classnames(
                    [
                        $item_class => true,
                        $post_id_class => true,
                        'clearfix' => true,
                        'et_pb_has_overlay' => $show_overlay,
                        'et_pb_no_thumb' => $show_thumbnail && !$has_thumbnail,
                        'et_pb_post' => true,
                    ],
                    get_post_class()
                ),
            ],
            'children' => [
                $post_format_content,
                $post_thumb,
                $title,
                $meta,
                $content,
            ],
            'childrenSanitizer' => 'et_core_esc_previously',
            'tag' => 'article',
        ]);
    }

    /**
     * This method is equivalent to "edit.tsx".
     *
     * @param array          $attrs    block attributes that were saved by Divi Builder
     * @param string         $content  the block's content
     * @param \WP_Block      $block    parsed block object that is being rendered
     * @param ModuleElements $elements an instance of the ModuleElements class
     */
    public static function render_callback(array $attrs, string $content, \WP_Block $block, ModuleElements $elements): string
    {
        global $post, $paged, $wp_query, $wp_the_query, $wp_filter, $__et_blog_module_paged;
        if (static::$_rendering) {
            return '';
        }
        static::$_rendering = true;
        // Fallback $__et_blog_module_paged; sometime it could be null
        $blogPaged = $__et_blog_module_paged > 1 ? $__et_blog_module_paged : absint(get_query_var('page'));
        $blogPaged = max(1, $blogPaged);
        // Keep a reference to the real main query to restore from later
        $mainQuery = $wp_the_query;
        $categories = $attrs['post']['advanced']['categories']['desktop']['value'] ?? [];
        $fullwidth = $attrs['fullwidth']['advanced']['enable']['desktop']['value'] ?? 'on';
        $offset = $attrs['post']['advanced']['offset']['desktop']['value'] ?? '';
        $postsPerPage = $attrs['post']['advanced']['number']['desktop']['value'] ?? '';
        $postType = $attrs['post']['advanced']['type']['desktop']['value'] ?? '';
        $queryArgs = [
            'perm' => 'readable',
            'post_status' => ['publish', 'private', 'inherit'],
            'post_type' => $postType,
            'posts_per_page' => $postsPerPage,
        ];
        if ($blogPaged > 1) {
            $et_paged = $blogPaged;
            $paged = $blogPaged;
            $queryArgs['paged'] = $blogPaged;
        }
        if (!empty($categories)) {
            $queryArgs['cat'] = $categories;
        } else {
            // \WP_Query doesn't return sticky posts when it performed via Ajax.
            // This happens because `is_home` is false in this case, but on FE it's true if no category set for the query.
            // Set `is_home` = true to emulate the FE behavior with sticky posts in VB.
            add_action('pre_get_posts', function ($query) {
                if (true === $query->get('et_is_home')) {
                    $query->is_home = true;
                }
            });
            $queryArgs['et_is_home'] = true;
        }
        if (!empty($offset)) {
            $queryArgs['offset'] = $offset;
        }
        // Backup properties that will not be the same after wp_reset_query().
        $originalQueryProps = [
            'current_post' => $wp_query->current_post,
            'in_the_loop' => $wp_query->in_the_loop,
        ];
        query_posts($queryArgs);
        $wp_query = apply_filters('et_builder_blog_query', $wp_query, $attrs);
        $output = '';
        $pagination = '';
        $postIds = [];
        $itemsCount = 0;
        if ($wp_query->have_posts()) {
            $stickyPosts = get_option('sticky_posts');
            if (!empty($stickyPosts)) {
                $stickyArgs = [
                    'cat' => $categories,
                    'orderby' => 'post__in',
                    'post__in' => $stickyPosts,
                    'post_type' => 'post',
                    'posts_per_page' => -1,
                ];
                $stickyQuery = new \WP_Query($stickyArgs);
                while ($stickyQuery->have_posts()) {
                    $stickyQuery->the_post();
                    $postIds[] = get_the_ID();
                    $output .= static::process_post_data($stickyQuery->post, $attrs, $block->parsed_block['orderIndex'], $itemsCount);
                    ++$itemsCount;
                }
                wp_reset_postdata();
            }
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                if (!in_array(get_the_ID(), $sticky_posts, true)) {
                    $postIds[] = get_the_ID();
                    $output .= static::process_post_data($wp_query->post, $attrs, $block->parsed_block['orderIndex'], $itemsCount);
                    ++$itemsCount;
                }
            }
            $pagination .= static::render_pagination($attrs);
            wp_reset_postdata();
        }
        unset($wp_query->et_pb_blog_query);
        $wp_the_query = $wp_query = $mainQuery;
        wp_reset_query();
        // Restore properties
        foreach ($originalQueryProps as $prop => $value) {
            $wp_query->{$prop} = $value;
        }
        $noPostsHtml = '';
        ob_start();
        get_template_part('includes/no-results', 'index');
        if (ob_get_length() > 0) {
            $noPostsHtml = ob_get_clean();
        }
        $postsHtml = 'on' === $fullwidth
            ? HTMLUtility::render([
                'attributes' => [
                    'class' => 'et_pb_ajax_pagination_container',
                ],
                'children' => [
                    $output,
                    $pagination,
                ],
                'childrenSanitizer' => 'et_core_esc_previously',
                'tag' => 'div',
            ])
            : HTMLUtility::render([
                'attributes' => [
                    'class' => 'et_pb_blog_grid clearfix',
                ],
                'tag' => 'div',
                'children' => HTMLUtility::render([
                    'attributes' => [
                        'class' => 'et_pb_ajax_pagination_container',
                    ],
                    'tag' => 'div',
                    'children' => [
                        HTMLUtility::render([
                            'attributes' => [
                                'class' => 'et_pb_salvattore_content',
                                'data-columns' => '',
                            ],
                            'children' => [
                                $output,
                            ],
                            'childrenSanitizer' => 'et_core_esc_previously',
                            'tag' => 'div',
                        ]),
                        $pagination,
                    ],
                    'childrenSanitizer' => 'et_core_esc_previously',
                ]),
                'childrenSanitizer' => 'et_core_esc_previously',
            ]);
        if (empty($postIds)) {
            $postsHtml = $noPostsHtml;
        }
        $parent = BlockParserStore::get_parent(
            $block->parsed_block['id'],
            $block->parsed_block['storeInstance']
        );
        $moduleHtml = DiviModule::render([
            'attrs' => $attrs,
            'children' => [
                $elements->style_components([
                    'attrName' => 'module',
                ]),
                $postsHtml,
            ],
            'classnamesFunction' => [static::class, 'module_classnames'],
            'elements' => $elements,
            'id' => $block->parsed_block['id'],
            'moduleCategory' => $block->block_type->category,
            'name' => $block->block_type->name,
            'orderIndex' => $block->parsed_block['orderIndex'], // FE only
            'parentAttrs' => $parent->attrs ?? [],
            'parentId' => $parent->id ?? '',
            'parentName' => $parent->blockName ?? '',
            'scriptDataComponent' => function ($args) use ($postIds) {
                static::module_script_data(array_merge($args, [
                    'post_ids' => $postIds,
                ]));
            },
            'storeInstance' => $block->parsed_block['storeInstance'], // FE only
            'stylesComponent' => [static::class, 'module_styles'],
        ]);
        static::$_rendering = false;
        return $moduleHtml;
    }

    public static function render_content(array $args): string
    {
        if (static::$_rendering_content) {
            return '';
        }
        static::$_rendering_content = true;
        $excerpt_content = $args['excerpt_content'] ?? 'off';
        $show_excerpt = $args['show_excerpt'] ?? 'on';
        $excerpt_manual = $args['excerpt_manual'] ?? 'on';
        $excerpt_length = (int) $args['excerpt_length'] ?? 270;
        $post_id = (int) $args['post_id'] ?? 0;
        $post_content = et_strip_shortcodes(PostUtility::delete_post_first_video(get_the_content(null, false, $post_id)), true);
        $content = '';
        if ('on' === $excerpt_content) {
            global $more;
            if (et_pb_is_pagebuilder_used($post_id)) {
                $content = et_core_intentionally_unescaped(apply_filters('the_content', $post_content), 'html');
                $more = 1;
            } else {
                $content = et_core_intentionally_unescaped(apply_filters('the_content', PostUtility::delete_post_first_video(get_the_content(esc_html__('read more...', 'et_builder'), false, $post_id))), 'html');
                $more = null;
            }
        } elseif ('on' === $show_excerpt) {
            if (has_excerpt($post_id) && 'off' !== $excerpt_manual) {
                $content = apply_filters('the_excerpt', get_the_excerpt($post_id));
            } elseif ('' !== $post_content) {
                $content = et_core_intentionally_unescaped(wpautop(PostUtility::delete_post_first_video(strip_shortcodes(PostUtility::truncate_post($excerpt_length, false, get_post($post_id), true)))), 'html');
            }
        }
        static::$_rendering_content = false;
        return $content;
    }

    public static function render_meta(array $args): string
    {
        $show_author = $args['show_author'] ?? '';
        $show_date = $args['show_date'] ?? '';
        $show_categories = $args['show_categories'] ?? '';
        $show_comments = $args['show_comments'] ?? '';
        $post_id = $args['post_id'] ?? 0;
        $date_format = $args['date_format'] ?? '';
        $post_meta = [];
        $author = sprintf(__('by %s', 'et_builder'), HTMLUtility::render([
            'attributes' => [
                'class' => 'author vcard',
            ],
            'children' => HTMLUtility::render([
                'attributes' => [
                    'href' => get_author_posts_url(get_the_author_meta('ID')),
                    'title' => sprintf(__('Posts by %s', 'et_builder'), get_the_author()),
                    'rel' => 'author',
                ],
                'children' => get_the_author(),
                'tag' => 'a',
            ]),
            'childrenSanitizer' => 'et_core_esc_previously',
            'tag' => 'span',
        ]));
        if ($show_author) {
            $post_meta[] = $author;
        }
        $date = HTMLUtility::render([
            'attributes' => [
                'class' => 'published',
            ],
            'children' => get_the_date($date_format, $post_id),
            'tag' => 'span',
        ]);
        if ($show_date) {
            $post_meta[] = $date;
        }
        $taxonomy = et_builder_get_category_taxonomy(get_post_type($post_id));
        $terms = get_the_terms($post_id, $taxonomy);
        $categories = [];
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $categories[] = HTMLUtility::render([
                    'attributes' => [
                        'href' => get_term_link($term, $taxonomy),
                        'rel' => 'tag',
                    ],
                    'children' => $term->name,
                    'tag' => 'a',
                ]);
            }
        }
        if ($show_categories) {
            $post_meta[] = HTMLUtility::render([
                'attributes' => [
                    'class' => 'entry-categories',
                ],
                'children' => implode(', ', $categories),
                'childrenSanitizer' => 'et_core_esc_previously',
                'tag' => 'span',
            ]);
        }
        $comments = sprintf(
            esc_html(_nx('%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder')),
            number_format_i18n(get_comments_number($post_id))
        );
        if ($show_comments) {
            $post_meta[] = HTMLUtility::render([
                'attributes' => [
                    'class' => 'entry-comments',
                ],
                'children' => $comments,
                'childrenSanitizer' => 'et_core_esc_previously',
                'tag' => 'span',
            ]);
        }
        return implode(' | ', $post_meta);
    }

    public static function render_pagination(array $attrs): string
    {
        // Check if pagination is enabled across all breakpoints and states.
        $show_pagination = ModuleUtils::has_value($attrs['pagination']['advanced']['enable'] ?? [], [
            'valueResolver' => fn ($value) => 'on' === $value,
        ]);
        if (!$show_pagination) {
            return '';
        }
        ob_start();
        add_filter('get_pagenum_link', [static::class, 'filter_pagination_url']);
        if (function_exists('wp_pagenavi')) {
            wp_pagenavi();
        } elseif (et_is_builder_plugin_active()) {
            include ET_BUILDER_PLUGIN_DIR.'includes/navigation.php';
        } else {
            get_template_part('includes/navigation', 'index');
        }
        remove_filter('get_pagenum_link', [static::class, 'filter_pagination_url']);
        $output = ob_get_contents();
        ob_end_clean();
        $is_hidden_onload = 'on' !== ($attrs['pagination']['advanced']['enable']['desktop']['value'] ?? 'on');
        if ($is_hidden_onload) {
            $class_attributes = strpos($output, 'class="');
            if (false !== $class_attributes) {
                $output = substr_replace($output, 'class="et_multi_view_hidden ', $class_attributes, strlen('class="'));
            }
        }
        return $output;
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    public static function shortcodeInstance(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(static::shortcodeClass());
        }
        return $shortcode;
    }
}
