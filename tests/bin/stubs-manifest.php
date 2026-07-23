<?php

/**
 * Manifest for tests/bin/generate-stubs.php (run it with `make stubs:update`).
 *
 * One entry per generated stub: tests/stubs/<slug>.php. The slugs the mu-plugin
 * and phpstan.neon load are the keys here — renaming one means updating both.
 *
 * NOT in this manifest, on purpose: akismet.php, polylang.php and wp-cli.php are
 * WORKING FAKES (hand-written, with behaviour the tests read back), not generated
 * stubs. The generator refuses to touch them.
 *
 * Entry options:
 *
 *   source    Where the plugin/theme source comes from. A string or (to merge
 *             several sources into one stub) an array of strings:
 *               - "https://…/foo.zip"        remote zip. Use wp.org's
 *                                            /plugin/<slug>.latest-stable.zip:
 *                                            the bare <slug>.zip serves TRUNK,
 *                                            which for WooCommerce is a beta
 *               - "github:owner/repo"        source zipball of the latest GitHub
 *                                            release tag
 *               - "tests/bin/zips/foo.zip"   local zip, relative to the repo root
 *                                            (premium plugins/themes — drop the
 *                                            zip there and rerun; the directory
 *                                            is gitignored). Entry is SKIPPED
 *                                            with a notice while the zip is
 *                                            absent.
 *               - "vendors/foo"              local directory, relative to the
 *                                            repo root
 *
 *   symbols   Optional allowlist. When present, the stub keeps ONLY these
 *             classes/interfaces/traits/functions/constants (fully-qualified,
 *             case-insensitive) — plus every parent class, interface and trait
 *             a kept class needs to be declarable, pulled in automatically.
 *             When absent, the whole source (minus `exclude`) is stubbed.
 *
 *             The allowlist is the contract between plugin/Integrations/<X> and
 *             the third party: it is what the integration actually references.
 *             When an integration starts using a new upstream symbol, add it
 *             here and rerun. Deliberate omissions are commented inline.
 *
 *   exclude   Directories — or, when the entry ends in ".php", single files —
 *             (relative to the source root) pruned before generation. REPLACES
 *             the default list when present:
 *             vendor, vendors, node_modules, tests, Tests, test, Test, build, dist.
 *             A bare name matches a directory at any depth; a name with a
 *             slash matches that relative path at any depth. Case-sensitive.
 *
 *   append    A hand-maintained fragment (tests/bin/fragments/<slug>.php)
 *             concatenated onto the generated stub — for symbols no generator
 *             can reach, e.g. a class declared inside a function. The fragment
 *             says why each symbol is there; keep its namespaces braced.
 */
return [
    /*
     * The bundled copy is the one the plugin ships (vendors/woocommerce/…), so
     * the stub always matches it exactly. Loaded only by phpstan — the suite
     * must never load it (the real classes are already declared).
     */
    'action-scheduler' => [
        'source' => 'vendors/woocommerce/action-scheduler',
    ],
    'breakdance' => [
        'source' => 'tests/bin/zips/breakdance.zip', // premium: breakdance.com
        'symbols' => [
            'Breakdance\AJAX\get_nonce_for_ajax_requests',
            'Breakdance\AJAX\get_nonce_key_for_ajax_requests',
            'Breakdance\AJAX\register_handler',
            'Breakdance\Elements\c',
            'Breakdance\Elements\Element',
            'Breakdance\Elements\PresetSections\getPresetSection',
            'Breakdance\Elements\PresetSections\PresetSectionsController',
            'Breakdance\Elements\registerCategory',
            'Breakdance\ElementStudio\registerSaveLocation',
            'Breakdance\Lib\Vendor\League\HTMLToMarkdown\ElementInterface',
            'Breakdance\Permissions\hasMinimumPermission',
            'Breakdance\Singleton',
            'EssentialElements\Formdesignoptions',
            '__BREAKDANCE_VERSION',
        ],
    ],
    'bricks' => [
        'source' => 'tests/bin/zips/bricks.zip', // premium theme: bricksbuilder.io
    ],
    'buddyboss' => [
        'source' => 'github:buddyboss/buddyboss-platform',
        'symbols' => [
            'bp_displayed_user_id',
        ],
    ],
    'divi' => [
        'source' => 'tests/bin/zips/divi.zip', // premium theme: elegantthemes.com
        'symbols' => [
            'ET\Builder\Framework\Controllers\RESTController',
            'ET\Builder\Framework\DependencyManagement\DependencyTree',
            'ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface',
            'ET\Builder\Framework\Route\RESTRoute',
            'ET\Builder\Framework\UserRole\UserRole',
            'ET\Builder\FrontEnd\BlockParser\BlockParser',
            'ET\Builder\FrontEnd\BlockParser\BlockParserBlock',
            'ET\Builder\FrontEnd\BlockParser\BlockParserBlockRoot',
            'ET\Builder\FrontEnd\BlockParser\BlockParserFrame',
            'ET\Builder\FrontEnd\BlockParser\BlockParserStore',
            'ET\Builder\FrontEnd\BlockParser\SimpleBlock',
            'ET\Builder\FrontEnd\BlockParser\SimpleBlockParser',
            'ET\Builder\FrontEnd\BlockParser\SimpleBlockParserStore',
            'ET\Builder\FrontEnd\Module\Fonts',
            'ET\Builder\FrontEnd\Module\Script',
            'ET\Builder\FrontEnd\Module\ScriptData',
            'ET\Builder\FrontEnd\Module\Style',
            'ET\Builder\Packages\IconLibrary\IconFont\Utils',
            'ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements',
            'ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElementsAttr',
            'ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElementsUtils',
            'ET\Builder\Packages\Module\Module',
            'ET\Builder\Packages\Module\Options\Border\BorderStyle',
            'ET\Builder\Packages\Module\Options\BoxShadow\BoxShadowStyle',
            'ET\Builder\Packages\Module\Options\Css\CssStyle',
            'ET\Builder\Packages\Module\Options\Css\CssStyleUtils',
            'ET\Builder\Packages\Module\Options\Element\ElementClassnames',
            'ET\Builder\Packages\Module\Options\Element\ElementComponents',
            'ET\Builder\Packages\Module\Options\Element\ElementFilterFunctions',
            'ET\Builder\Packages\Module\Options\Element\ElementScriptData',
            'ET\Builder\Packages\Module\Options\Element\ElementStyle',
            'ET\Builder\Packages\Module\Options\Element\ElementStyleAdvancedStyles',
            'ET\Builder\Packages\Module\Options\Element\InteractionClassnames',
            'ET\Builder\Packages\Module\Options\Font\FontPresetAttrsMap',
            'ET\Builder\Packages\Module\Options\Font\FontStyle',
            'ET\Builder\Packages\Module\Options\Text\TextClassnames',
            'ET\Builder\Packages\Module\Options\Text\TextPresetAttrsMap',
            'ET\Builder\Packages\Module\Options\Text\TextStyle',
            'ET\Builder\Packages\ModuleLibrary\ModuleRegistration',
            'ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations',
            'ET\Builder\Packages\StyleLibrary\Utils\Utils',
            'ET\Builder\VisualBuilder\Assets\AssetsUtility',
            'ET\Builder\VisualBuilder\Assets\DiviPackageBuild',
            'ET\Builder\VisualBuilder\Assets\PackageBuild',
            'ET\Builder\VisualBuilder\Assets\PackageBuildManager',
            'et_builder_d5_enabled',
            'et_core_is_fb_enabled',
        ],
    ],
    'elementor' => [
        'source' => 'https://downloads.wordpress.org/plugin/elementor.latest-stable.zip',
        // includes/libraries ships global class_exists-guarded shims of
        // WP_Async_Request/WP_Background_Process; the generator drops the guard
        // and the globals would collide with action-scheduler.php under phpstan.
        // Test/Tests: the prefixed Twig in vendor_prefixed ships test scaffolding
        // extending a PHPUnit class that exists nowhere at stub load time.
        'exclude' => ['vendor', 'node_modules', 'tests', 'Tests', 'test', 'Test', 'build', 'includes/libraries'],
    ],
    'elementorpro' => [
        'source' => 'tests/bin/zips/elementor-pro.zip', // premium: elementor.com
    ],
    'flatsome' => [
        'source' => 'tests/bin/zips/flatsome.zip', // premium theme: uxthemes.com
        'symbols' => [
            'add_ux_builder_shortcode',
        ],
    ],
    'fusion-builder' => [
        'source' => 'tests/bin/zips/fusion-builder.zip', // premium: Avada's builder plugin, theme-fusion.com
        'symbols' => [
            'Fusion_Element',
            'FusionBuilder', // the class and the function
            'fusion_builder_auto_activate_element',
            'fusion_builder_frontend_data',
            'fusion_builder_map',
            'fusion_builder_shortcodes_categories',
            'is_fusion_editor',
            'FUSION_BUILDER_VERSION',
        ],
    ],
    'gamipress' => [
        'source' => 'https://downloads.wordpress.org/plugin/gamipress.latest-stable.zip',
        // GAMIPRESS_VER is deliberately absent: without it the integration's
        // version gate keeps GamiPress dark (see tests/pest/README.md). Add it
        // here to wake the integration.
        'symbols' => [
            'ct_get_object_meta',
            'gamipress_get_achievement_types_slugs',
            'gamipress_get_rank_types_slugs',
            'gamipress_get_requirement_types_slugs',
            'gamipress_trigger_event',
        ],
    ],
    'lpfw' => [
        'source' => 'tests/bin/zips/lpfw.zip', // premium: advancedcouponsplugin.com "Loyalty Program for WooCommerce"
        'symbols' => [
            'LPFW', // the class and the function
            'LPFW\Abstracts\Abstract_Main_Plugin_Class',
            'LPFW\Helpers\Helper_Functions',
            'LPFW\Helpers\Plugin_Constants',
            'LPFW\Interfaces\Initiable_Interface',
            'LPFW\Interfaces\Model_Interface',
            'LPFW\Models\Earn_Points',
            'LPFW\Models\Entries',
        ],
    ],
    'multilingualpress' => [
        'source' => 'tests/bin/zips/multilingualpress.zip', // premium: multilingualpress.org
    ],
    'mycred' => [
        'source' => 'https://downloads.wordpress.org/plugin/mycred.latest-stable.zip',
        // myCRED_Hook_WooCommerce_Reviews and MYCRED_DEFAULT_TYPE_KEY cannot be
        // generated (declared inside a function / via $this->define()) — the
        // fragment appends them by hand.
        'append' => 'tests/bin/fragments/mycred.php',
        'symbols' => [
            'myCRED_Core',
            'myCRED_Hook',
            'mycred_get_post',
            'mycred_get_user_meta',
            'mycred_update_user_meta',
        ],
    ],
    'profilepress' => [
        'source' => 'https://downloads.wordpress.org/plugin/wp-user-avatar.latest-stable.zip',
        // ElementorDisplayCondition extends an Elementor Pro class, and the
        // elementorpro stub is deliberately loaded last (see the mu-plugin) —
        // the parent does not exist yet when this stub loads.
        'exclude' => ['vendor', 'node_modules', 'tests', 'third-party',
            'src/ContentProtection/ElementorDisplayCondition.php'],
    ],
    'surecart' => [
        'source' => 'https://downloads.wordpress.org/plugin/surecart.latest-stable.zip',
        // vendor/ stays IN: the stub needs the SureCartVendors/TypistTech
        // prefixed libraries. vendor/composer would redeclare Composer's own
        // ClassLoader in the suite; vendor/woocommerce is SureCart's bundled
        // Action Scheduler, which the plugin bundles too (tests/stubs/action-scheduler.php).
        // GalleryItemMedia (as of SureCart 3.x, 2026-07): attributes() is missing
        // the `: object` return type its GalleryItem interface requires — a
        // latent upstream fatal that eager stub loading surfaces. Drop the
        // exclude when upstream fixes the signature.
        // Integrations/Elementor: classes extending Elementor Pro, whose stub is
        // deliberately loaded last (see the mu-plugin) — the parents do not
        // exist yet when this stub loads.
        'exclude' => ['vendor/composer', 'vendor/woocommerce', 'node_modules', 'tests', 'dist',
            'app/src/Models/GalleryItemMedia.php',
            'app/src/Integrations/Elementor'],
    ],
    'ultimate-member' => [
        'source' => 'https://downloads.wordpress.org/plugin/ultimate-member.latest-stable.zip',
        'symbols' => [
            'UM', // the class and the function
            'UM_Functions',
            'um\core\Member_Directory',
            'um\core\Member_Directory_Meta',
            'um\core\Query',
            'um_get_default_avatar_uri',
            'um_get_requested_user',
            'um_is_core_page',
            'um_user_profile_url',
        ],
    ],
    'wlpr' => [
        // Flycart's legacy "WooCommerce Loyalty Points and Rewards" — no longer
        // distributed (superseded by WPLoyalty), so the source is a local zip of
        // the last release.
        'source' => 'tests/bin/zips/wlpr.zip',
        'symbols' => [
            'Wlpr\App\Helpers\Base',
            'Wlpr\App\Helpers\Loyalty',
            'Wlpr\App\Helpers\Point',
            'Wlpr\App\Models\Base',
            'Wlpr\App\Models\PointAction',
        ],
    ],
    'woocommerce' => [
        'source' => 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip',
        'exclude' => ['vendor', 'node_modules', 'tests', 'packages', 'lib'],
        'symbols' => [
            'WooCommerce',
            'WC', // function
            'WC_Abstract_Legacy_Order',
            'WC_Abstract_Legacy_Product',
            'WC_Abstract_Order',
            'WC_Coupon',
            'WC_Customer',
            'WC_Data',
            'WC_Email',
            'WC_Emails',
            'WC_Item_Totals',
            'WC_Legacy_Coupon',
            'WC_Legacy_Customer',
            'WC_Order',
            'WC_Order_Item',
            'WC_Order_Item_Product',
            'WC_Product',
            'WC_Query',
            'WC_REST_Controller',
            'WC_REST_Product_Reviews_Controller',
            'WC_REST_Report_Reviews_Totals_Controller',
            'WC_REST_Reports_Controller',
            'WC_REST_Reports_V1_Controller',
            'WC_REST_Reports_V2_Controller',
            'WC_Settings_API',
            'WC_Widget',
            'WC_Widget_Rating_Filter',
            'WC_Widget_Recent_Reviews',
            'Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery',
            'Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods',
            'Automattic\WooCommerce\StoreApi\Routes\RouteInterface',
            'Automattic\WooCommerce\StoreApi\Routes\V1\AbstractRoute',
            'Automattic\WooCommerce\StoreApi\Routes\V1\ProductReviews',
            'Automattic\WooCommerce\StoreApi\Routes\V1\Products',
            'Automattic\WooCommerce\StoreApi\SchemaController',
            'Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema',
            'Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema',
            'Automattic\WooCommerce\StoreApi\Schemas\V1\ImageAttachmentSchema',
            'Automattic\WooCommerce\StoreApi\Schemas\V1\ProductReviewSchema',
            'Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema',
            'Automattic\WooCommerce\StoreApi\StoreApi',
            'Automattic\WooCommerce\StoreApi\Utilities\Pagination',
            'Automattic\WooCommerce\StoreApi\Utilities\ProductQuery',
            'is_shop',
            'is_product_taxonomy',
            'wc_customer_bought_product',
            'wc_format_datetime',
            'wc_get_coupon_id_by_code',
            'wc_get_is_paid_statuses',
            'wc_get_order',
            'wc_get_order_status_name',
            'wc_get_orders',
            'wc_get_product',
            'wc_get_products',
            'wc_get_template_html',
            'wc_price',
            'wc_rest_prepare_date_response',
            'wc_review_is_from_verified_owner',
            'wc_review_ratings_enabled',
            'wc_update_500_fix_product_review_count',
        ],
    ],
    'woorewards' => [
        'source' => 'https://downloads.wordpress.org/plugin/woorewards.latest-stable.zip',
        // \LWS_WooRewards and \LWS\WOOREWARDS\Core\Trace are deliberately
        // absent: without them the integration's isInstalled() keeps WooRewards
        // dark (see tests/pest/README.md). Add them here to wake it.
        'symbols' => [
            'LWS\WOOREWARDS\Abstracts\Event',
            'LWS\WOOREWARDS\Abstracts\ICategorisable',
            'LWS\WOOREWARDS\Abstracts\IRegistrable',
            'LWS\WOOREWARDS\Events\ProductReview',
        ],
    ],
    'wp-loyalty-rules' => [
        // WPLoyalty: the free wp.org release plus the premium add-on, merged
        // into one stub (the integration references Wlr\App\Premium\… too).
        'source' => [
            'https://downloads.wordpress.org/plugin/wployalty.latest-stable.zip',
            'tests/bin/zips/wp-loyalty-rules.zip', // premium: wployalty.net
        ],
        'symbols' => [
            'Wlr\App\Helpers\Base',
            'Wlr\App\Helpers\EarnCampaign',
            'Wlr\App\Helpers\Woocommerce',
            'Wlr\App\Premium\Helpers\ProductReview',
            'Wlr\App\Premium\Helpers\Referral',
        ],
    ],
    'wpbakery' => [
        'source' => 'tests/bin/zips/js_composer.zip', // premium: wpbakery.com
        'symbols' => [
            'WPBakeryShortCode',
            'vc_add_shortcode_param',
            'vc_map',
            'WPB_VC_VERSION',
        ],
    ],
];
