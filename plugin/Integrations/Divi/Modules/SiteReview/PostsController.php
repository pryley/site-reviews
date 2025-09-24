<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReview;

use ET\Builder\Framework\Controllers\RESTController;
use ET\Builder\Framework\UserRole\UserRole;
use ET\Builder\Framework\Utility\PostUtility;

class PostsController extends RESTController
{
    public static function index(\WP_REST_Request $request): \WP_REST_Response
    {
        $posts = [];
        $args = [
            'post_type' => $request->get_param('postType'),
            'posts_per_page' => $request->get_param('postsPerPage'),
            'paged' => $request->get_param('paged'),
            'categories' => $request->get_param('categories'),
            'fullwidth' => $request->get_param('fullwidth'),
            'date_format' => $request->get_param('dateFormat'),
            'excerpt_content' => $request->get_param('excerptContent'),
            'excerpt_length' => $request->get_param('excerptLength'),
            'show_excerpt' => $request->get_param('showExcerpt'),
            'manual_excerpt' => $request->get_param('manualExcerpt'),
            'offset' => $request->get_param('offset'),
            'orderby' => $request->get_param('orderby'),
        ];
        $query_args = [
            'posts_per_page' => $args['posts_per_page'],
            'post_status' => ['publish', 'private', 'inherit'],
            'perm' => 'readable',
            'post_type' => $args['post_type'],
            'paged' => $args['paged'],
        ];
        if ('date_desc' !== $args['orderby']) {
            switch ($args['orderby']) {
                case 'date_asc':
                    $query_args['orderby'] = 'date';
                    $query_args['order'] = 'ASC';
                    break;
                case 'title_asc':
                    $query_args['orderby'] = 'title';
                    $query_args['order'] = 'ASC';
                    break;
                case 'title_desc':
                    $query_args['orderby'] = 'title';
                    $query_args['order'] = 'DESC';
                    break;
                case 'rand':
                    $query_args['orderby'] = 'rand';
                    break;
            }
        }
        if (!empty($args['categories'])) {
            $query_args['cat'] = $args['categories'];
        } else {
            // WP_Query doesn't return sticky posts when it performed via Ajax.
            // This happens because `is_home` is false in this case, but on FE it's true if no category set for the query.
            // Set `is_home` = true to emulate the FE behavior with sticky posts in VB.
            add_action('pre_get_posts', function ($query) {
                if (true === $query->get('et_is_home')) {
                    $query->is_home = true;
                }
            }
            );
            $query_args['et_is_home'] = true;
        }
        if ('' !== $args['offset'] && !empty($args['offset'])) {
            // Offset + pagination don't play well. Manual offset calculation required.
            // @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination.
            if ($args['paged'] > 1) {
                $query_args['offset'] = (($args['paged'] - 1) * $args['posts_per_page']) + $args['offset'];
            } else {
                $query_args['offset'] = $args['offset'];
            }
        }
        // Get query.
        $query = new \WP_Query($query_args);
        $sticky_posts = get_option('sticky_posts');
        if ($query->have_posts()) {
            // Display sticky posts first.
            if (!empty($sticky_posts)) {
                $sticky_args = [
                    'post_type' => 'post',
                    'post__in' => $sticky_posts,
                    'posts_per_page' => -1,
                    'cat' => $args['categories'],
                ];
                $sticky_query = new \WP_Query($sticky_args);
                while ($sticky_query->have_posts()) {
                    $sticky_query->the_post();
                    $posts[] = static::process_post_data($sticky_query->post, $args);
                }
                wp_reset_postdata();
            }
            // Display non-sticky posts.
            while ($query->have_posts()) {
                $query->the_post();
                if (!in_array(get_the_ID(), $sticky_posts, true)) {
                    $posts[] = static::process_post_data($query->post, $args);
                }
            }
        }
        $metadata = [
            'maxNumPages' => $query->max_num_pages,
        ];
        wp_reset_postdata();
        $response = [
            'posts' => $posts,
            'metadata' => $metadata,
        ];
        return static::response_success($response);
    }

    public static function index_args(): array
    {
        return [
            'postType' => [
                'type' => 'string',
                'default' => 'post',
                'validate_callback' => fn ($param) => is_string($param),
            ],
            'postsPerPage' => [
                'type' => 'string',
                'default' => '10',
                'sanitize_callback' => fn ($value) => (int) $value,
                'validate_callback' => fn ($param) => is_numeric($param),
            ],
            'paged' => [
                'type' => 'string',
                'default' => '1',
                'sanitize_callback' => fn ($value) => (int) $value,
                'validate_callback' => fn ($param) => is_numeric($param),
            ],
            'categories' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($value) => explode(',', $value),
            ],
            'fullwidth' => [
                'type' => 'string',
                'default' => 'on',
                'validate_callback' => fn ($param) => 'on' === $param || 'off' === $param,
            ],
            'dateFormat' => [
                'type' => 'string',
                'default' => 'M j, Y',
                'validate_callback' => fn ($param) => is_string($param),
            ],
            'excerptContent' => [
                'type' => 'string',
                'default' => 'off',
                'validate_callback' => fn ($param) => 'on' === $param || 'off' === $param,
            ],
            'excerptLength' => [
                'type' => 'string',
                'default' => '270',
                'sanitize_callback' => fn ($value) => (int) $value,
                'validate_callback' => fn ($param) => is_numeric($param),
            ],
            'showExcerpt' => [
                'type' => 'string',
                'default' => 'on',
                'validate_callback' => fn ($param) => 'on' === $param || 'off' === $param,
            ],
            'manualExcerpt' => [
                'type' => 'string',
                'default' => 'on',
                'validate_callback' => fn ($param) => 'on' === $param || 'off' === $param,
            ],
            'offset' => [
                'type' => 'string',
                'default' => '0',
                'sanitize_callback' => fn ($value) => (int) $value,
                'validate_callback' => fn ($param) => is_numeric($param),
            ],
            'orderby' => [
                'type' => 'string',
                'default' => 'date_desc',
                'validate_callback' => fn ($param) => is_string($param),
            ],
        ];
    }

    public static function index_permission(): bool
    {
        return UserRole::can_current_user_use_visual_builder();
    }

    public static function process_post_data(\WP_Post $post, array $args): array
    {
        global $et_theme_image_sizes;
        $title = get_the_title($post);
        $thumb = '';
        $thumbnail = [];
        if (has_post_thumbnail($post) || 'attachment' === get_post_type($post)) {
            $thumb = '';
            $width = 'on' === $args['fullwidth'] ? 1080 : 400;
            $width = (int) apply_filters('et_pb_blog_image_width', $width);
            $height = 'on' === $args['fullwidth'] ? 675 : 250;
            $height = (int) apply_filters('et_pb_blog_image_height', $height);
            $class = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
            $alt = get_post_meta(get_post_thumbnail_id($post), '_wp_attachment_image_alt', true);
            $thumbnail_data = get_thumbnail($width, $height, $class, $alt, $title, false, 'Blogimage');
            $thumb = $thumbnail_data['thumb'];
            $alt_text = get_post_meta(get_post_thumbnail_id($post), '_wp_attachment_image_alt', true);
            // Get thubmnail with size.
            $image_size_name = $width.'x'.$height;
            $et_size = isset($et_theme_image_sizes) && array_key_exists($image_size_name, $et_theme_image_sizes) ? $et_theme_image_sizes[$image_size_name] : [$width, $height];
            $et_attachment_image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($post), $et_size);
            $thumbnail_with_size = !empty($et_attachment_image_attributes[0]) ? $et_attachment_image_attributes[0] : '';
            $thumbnail = [
                'alt' => !empty($alt_text) ? $alt_text : esc_attr(get_the_title($post)),
                'class' => $class,
                'height' => $height,
                'src' => $thumb,
                'width' => $width,
            ];
            if ($width < 480 && et_is_responsive_images_enabled()) {
                $thumbnail['srcset'] = $thumb.' 479w, '.$thumbnail_with_size.' 480w';
                $thumbnail['sizes'] = '(max-width:479px) 479px, 100vw';
            }
        }
        $taxonomy = et_builder_get_category_taxonomy(get_post_type($post));
        $terms = get_the_terms($post, $taxonomy);
        $categories = [];
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $categories[] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'link' => get_term_link($term, $taxonomy),
                ];
            }
        }
        $content = Module::render_content([
            'excerpt_content' => $args['excerpt_content'],
            'show_excerpt' => $args['show_excerpt'],
            'excerpt_manual' => $args['manual_excerpt'],
            'excerpt_length' => $args['excerpt_length'],
            'post_id' => $post->ID,
        ]);
        ob_start();
        et_pb_gallery_images('slider');
        $post_gallery = ob_get_clean();
        // Post background color.
        $post_use_background_color = get_post_meta($post->ID, '_et_post_use_bg_color', true) ? true : false;
        $background_color = get_post_meta($post->ID, '_et_post_bg_color', true);
        $post_background_color = $background_color && '' !== $background_color ? $background_color : '#ffffff';
        return [
            'id' => $post->ID,
            'classNames' => get_post_class('', $post->ID),
            /*
             * Process the data with the html_entity_decode function.
             *
             * This function is used to decode HTML entities in the given data.
             * It is specifically required for data consumed by VB REST requests.
             * HTML entities are special characters that are represented in HTML using entity references,
             * such as `&amp;` for the ampersand character (&).
             * By decoding these entities, the original characters are restored,
             * ensuring that the data is correctly interpreted by the VB REST requests.
             */
            'title' => html_entity_decode(get_the_title($post)),
            'isPasswordRequired' => post_password_required($post),
            'permalink' => get_permalink($post),
            'thumbnail' => !empty($thumb) ? $thumbnail : null,
            'content' => $content,
            'date' => get_the_date($args['date_format'], $post),
            'comment' => sprintf(esc_html(_nx('%s Comment', '%s Comments', get_comments_number($post), 'number of comments', 'et_builder')), number_format_i18n(get_comments_number($post))),
            'author' => [
                'name' => get_the_author_meta('display_name', $post->post_author),
                'link' => get_author_posts_url($post->post_author),
            ],
            'categories' => $categories,
            'postFormat' => [
                'type' => et_pb_post_format(),
                'video' => PostUtility::get_first_video(),
                'gallery' => $post_gallery,
                'audio' => et_core_intentionally_unescaped(et_pb_get_audio_player(), 'html'),
                'quote' => et_core_intentionally_unescaped(et_get_blockquote_in_content(), 'html'),
                'link' => esc_html(et_get_link_url()),
                'textColorClass' => et_divi_get_post_text_color(),
                'backgroundColor' => $post_use_background_color ? $post_background_color : '',
            ],
        ];
    }
}
