<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Rating;
use ReflectionClass;

abstract class Shortcode implements ShortcodeContract
{
    /**
     * @var string
     */
    protected $partialName;

    /**
     * @var string
     */
    protected $shortcodeName;

    public function __construct()
    {
        $this->partialName = $this->getShortcodePartialName();
        $this->shortcodeName = $this->getShortcodeName();
    }

    /**
     * @param string|array $atts
     * @param string $type
     * @return string
     */
    public function build($atts, array $args = [], $type = 'shortcode')
    {
        $args = $this->normalizeArgs($args, $type);
        $atts = $this->normalizeAtts($atts, $type);
        $partial = glsr(Partial::class)->build($this->partialName, $atts);
        $title = !empty($atts['title'])
            ? $args['before_title'].$atts['title'].$args['after_title']
            : '';
        $debug = sprintf('<glsr-%1$s hidden data-atts=\'%2$s\'></glsr-%1$s>', $type, $atts['json']);
        return $args['before_widget'].$title.$partial.$debug.$args['after_widget'];
    }

    /**
     * @param string|array $atts
     * @return string
     */
    public function buildShortcode($atts = [])
    {
        return $this->build($atts);
    }

    /**
     * @return array
     */
    public function getDefaults($atts)
    {
        return glsr($this->getShortcodeDefaultsClassName())->restrict(wp_parse_args($atts));
    }

    /**
     * @return array
     */
    public function getHideOptions()
    {
        $options = $this->hideOptions();
        return apply_filters('site-reviews/shortcode/hide-options', $options, $this->shortcodeName);
    }

    /**
     * @return string
     */
    public function getShortcodeClassName($replace = '', $search = 'Shortcode')
    {
        return str_replace($search, $replace, (new ReflectionClass($this))->getShortName());
    }

    /**
     * @return string
     */
    public function getShortcodeDefaultsClassName()
    {
        return Helper::buildClassName(
            $this->getShortcodeClassName('Defaults'),
            'Defaults'
        );
    }

    /**
     * @return string
     */
    public function getShortcodeName()
    {
        return Str::snakeCase($this->getShortcodeClassName());
    }

    /**
     * @return string
     */
    public function getShortcodePartialName()
    {
        return Str::dashCase($this->getShortcodeClassName());
    }

    /**
     * @param array|string $args
     * @param string $type
     * @return array
     */
    public function normalizeArgs($args, $type = 'shortcode')
    {
        $args = wp_parse_args($args, [
            'before_widget' => '<div class="glsr-'.$type.' '.$type.'-'.$this->partialName.'">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="glsr-'.$type.'-title">',
            'after_title' => '</h3>',
        ]);
        return apply_filters('site-reviews/shortcode/args', $args, $type, $this->partialName);
    }

    /**
     * @param array|string $atts
     * @param string $type
     * @return array
     */
    public function normalizeAtts($atts, $type = 'shortcode')
    {
        $atts = apply_filters('site-reviews/shortcode/atts', $atts, $type, $this->partialName);
        $atts = $this->getDefaults($atts);
        array_walk($atts, function (&$value, $key) {
            $methodName = Helper::buildMethodName($key, 'normalize');
            if (!method_exists($this, $methodName)) {
                return;
            }
            $value = $this->$methodName($value);
        });
        $this->setId($atts);
        return $atts;
    }

    /**
     * @return array
     */
    abstract protected function hideOptions();

    /**
     * @param string $postId
     * @return int|string
     */
    protected function normalizeAssignedTo($postId)
    {
        if ('parent_id' == $postId) {
            $postId = intval(wp_get_post_parent_id(intval(get_the_ID())));
        } elseif ('post_id' == $postId) {
            $postId = intval(get_the_ID());
        }
        return $postId;
    }

    /**
     * @param string $postId
     * @return int|string
     */
    protected function normalizeAssignTo($postId)
    {
        return $this->normalizeAssignedTo($postId);
    }

    /**
     * @param string|array $hide
     * @return array
     */
    protected function normalizeHide($hide)
    {
        if (is_string($hide)) {
            $hide = explode(',', $hide);
        }
        $hideKeys = array_keys($this->getHideOptions());
        return array_filter(array_map('trim', $hide), function ($value) use ($hideKeys) {
            return in_array($value, $hideKeys);
        });
    }

    /**
     * @param string $id
     * @return string
     */
    protected function normalizeId($id)
    {
        return sanitize_title($id);
    }

    /**
     * @param string $labels
     * @return array
     */
    protected function normalizeLabels($labels)
    {
        $defaults = [
            __('Excellent', 'site-reviews'),
            __('Very good', 'site-reviews'),
            __('Average', 'site-reviews'),
            __('Poor', 'site-reviews'),
            __('Terrible', 'site-reviews'),
        ];
        $maxRating = (int) glsr()->constant('MAX_RATING', Rating::class);
        $defaults = array_pad(array_slice($defaults, 0, $maxRating), $maxRating, '');
        $labels = array_map('trim', explode(',', $labels));
        foreach ($defaults as $i => $label) {
            if (empty($labels[$i])) {
                continue;
            }
            $defaults[$i] = $labels[$i];
        }
        return array_combine(range($maxRating, 1), $defaults);
    }

    /**
     * @param string $schema
     * @return bool
     */
    protected function normalizeSchema($schema)
    {
        return wp_validate_boolean($schema);
    }

    /**
     * @param string $text
     * @return string
     */
    protected function normalizeText($text)
    {
        return trim($text);
    }

    /**
     * @return void
     */
    protected function setId(array &$atts)
    {
        if (empty($atts['id'])) {
            $atts['id'] = Application::PREFIX.substr(md5(serialize($atts)), 0, 8);
        }
    }
}
