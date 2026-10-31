<?php

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Bricks\BricksElement;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Integrations\Elementor\Widgets\ElementorWidget;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Elementor and Bricks each adjust a class in the rendered output, and everything else in it has
 * to reach the page byte for byte.
 */

class BuilderMarkupParagraphShortcode extends SiteReviewsFormShortcode
{
    public function build(array $args = [], string $from = 'shortcode', bool $isWrapped = true): string
    {
        return '<p data-a="&#091;b&#093;">No div here</p>';
    }

    public function hasVisibleFields(array $args = []): bool
    {
        return true;
    }
}

/**
 * @param class-string $shortcodeClass
 */
function renderedElementorWidget(string $shortcodeClass): string
{
    $widget = new class($shortcodeClass) extends ElementorWidget {
        public static string $shortcode;

        public function __construct(string $shortcodeClass)
        {
            static::$shortcode = $shortcodeClass;
        }

        public function get_id()
        {
            return 'abc';
        }

        public function get_settings_for_display($setting_key = null)
        {
            return [];
        }

        public static function shortcodeClass(): string
        {
            return static::$shortcode;
        }
    };
    ob_start();
    (new ReflectionMethod($widget, 'render'))->invoke($widget);
    return (string) ob_get_clean();
}

/**
 * @param class-string $shortcodeClass
 */
function bricksElementForTest(string $shortcodeClass = SiteReviewsFormShortcode::class): BricksElement
{
    return new class($shortcodeClass) extends BricksElement {
        public static string $shortcode;

        public function __construct(string $shortcodeClass)
        {
            static::$shortcode = $shortcodeClass;
            $this->settings = ['style_button_size' => 'lg'];
            $this->theme_styles = [];
        }

        public function get_tag()
        {
            return 'div';
        }

        public function render_attributes($key, $add_custom_attributes = false, $as_array = false)
        {
            return '';
        }

        public static function shortcodeClass(): string
        {
            return static::$shortcode;
        }
    };
}

beforeEach(fn () => resetPluginState());

test('the Elementor widget adds its class and keeps the markup', function () {
    expect(renderedElementorWidget(SiteReviewsFormShortcode::class))
        ->toContain('name="site-reviews&#091;title&#093;"')
        ->toMatch('/^<div class="glsr-elementor-abc /')
        ->not->toContain('<?xml');
});

test('the Elementor widget leaves output without a div alone', function () {
    expect(renderedElementorWidget(BuilderMarkupParagraphShortcode::class))
        ->toBe('<p data-a="&#091;b&#093;">No div here</p>');
});

test('a real Elementor page shows its brackets as text', function () {
    // Elementor fetches this while registering widgets and can't handle a failed request
    set_transient('elementor_remote_info_api_data_'.ELEMENTOR_VERSION, ['pro_widgets' => []], HOUR_IN_SECONDS);
    add_shortcode('probe', fn () => 'PROBE-RAN');
    createReview(['title' => '[probe]']);
    $postId = createPost(['post_status' => 'publish', 'post_type' => 'page']);
    update_post_meta($postId, '_elementor_edit_mode', 'builder');
    update_post_meta($postId, '_elementor_version', ELEMENTOR_VERSION);
    update_post_meta($postId, '_elementor_data', wp_slash(wp_json_encode([[
        'elType' => 'section',
        'elements' => [[
            'elType' => 'column',
            'elements' => [[
                'elType' => 'widget',
                'elements' => [],
                'id' => 'ccc3333',
                'settings' => [],
                'widgetType' => 'site_reviews',
            ]],
            'id' => 'bbb2222',
            'settings' => ['_column_size' => 100],
        ]],
        'id' => 'aaa1111',
        'settings' => [],
    ]])));
    $html = do_shortcode(Elementor\Plugin::instance()->frontend->get_builder_content_for_display($postId));
    remove_shortcode('probe');
    expect($html)->toContain('&#091;probe&#093;')
        ->toContain('glsr-elementor-ccc3333')
        ->not->toContain('PROBE-RAN');
});

test('a Breakdance element shows its brackets as text', function () {
    add_shortcode('probe', fn () => 'PROBE-RAN');
    createReview(['title' => '[probe]']);
    $element = new class {
        use ElementTrait;

        public static function bdShortcode(): ShortcodeContract
        {
            return glsr(SiteReviewsShortcode::class);
        }

        public static function bdShortcodeClass(): string
        {
            return SiteReviewsShortcode::class;
        }

        public static function ssrArgs(array $propertiesData): array
        {
            return [];
        }
    };
    $html = do_shortcode($element::ssr([]));
    remove_shortcode('probe');
    expect($html)->toContain('&#091;probe&#093;')
        ->not->toContain('PROBE-RAN');
});

test('the Bricks element sets the class of its buttons and keeps the markup', function () {
    $element = bricksElementForTest();
    $source = '<form data-a="&#091;b&#093;" title="é"><button class="glsr-button old">Go</button><button class="other">No</button></form>';
    $html = (new ReflectionMethod($element, 'setButtonClass'))->invoke($element, $source);
    expect($html)->toBe('<form data-a="&#091;b&#093;" title="é"><button class="bricks-button glsr-button lg">Go</button><button class="other">No</button></form>');
});

test('the Bricks element leaves output without its buttons alone', function () {
    $element = bricksElementForTest();
    $source = '<form data-a="&#091;b&#093;" title="é"><button class="other">No</button></form>';
    expect((new ReflectionMethod($element, 'setButtonClass'))->invoke($element, $source))->toBe($source);
});

test('the Bricks element renders its shortcode with its brackets as text', function () {
    add_shortcode('probe', fn () => 'PROBE-RAN');
    createReview(['title' => '[probe]']);
    $element = bricksElementForTest(SiteReviewsShortcode::class);
    ob_start();
    $element->render();
    $html = do_shortcode((string) ob_get_clean());
    remove_shortcode('probe');
    expect($html)->toContain('&#091;probe&#093;')
        ->not->toContain('PROBE-RAN')
        ->not->toContain('<?xml');
});
