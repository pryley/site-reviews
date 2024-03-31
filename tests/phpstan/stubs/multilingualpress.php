<?php // phpcs:ignoreFile

namespace Inpsyde\MultilingualPress\Framework\Setting\Site {
    /**
     * Interface for all site setting view model implementations.
     */
    interface SiteSettingViewModel
    {
        /**
         * Renders the markup for the site setting.
         *
         * @param int $siteId
         */
        public function render(int $siteId);
        /**
         * Returns the title of the site setting.
         *
         * @return string
         */
        public function title() : string;
    }
}
namespace Inpsyde\MultilingualPress\SiteFlags\Core\Admin {
    /**
     * MultilingualPress "Site Custom Flag Url" site setting
     */
    final class SiteFlagUrlSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-site-flag-url';
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @param SiteSettingsRepository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Class SiteMenuLanguageStyleSetting
     */
    final class SiteMenuLanguageStyleSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        const FLAG_AND_LANGUAGES = 'flag_and_text';
        const ONLY_FLAGS = 'only_flag';
        const ONLY_LANGUAGES = 'only_language';
        /**
         * @var string
         */
        private $id = 'mlp-site-menu-language-style';
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @param SiteSettingsRepository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * @return array
         */
        private static function options() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * Trait SiteSettingsRepositoryTrait
     */
    trait SiteSettingsRepositoryTrait
    {
        /**
         * Return an array where keys are site IDs and values are the setting value for the given key.
         *
         * @param string $settingKey
         * @return array
         */
        public function allSitesSetting(string $settingKey) : array
        {
        }
        /**
         * Returns the complete settings data.
         *
         * @return array
         */
        public function allSettings() : array
        {
        }
        /**
         * Sets the given settings data.
         *
         * @param array $settings
         * @return bool
         */
        public function updateSettings(array $settings) : bool
        {
        }
        /**
         * Updates the given setting for the site with the given ID, or the current site.
         *
         * @param string $key
         * @param mixed $value
         * @param int|null $siteId
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        private function updateSetting(string $key, $value, int $siteId = null) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteFlags\Core\Admin {
    /**
     * Class SiteSettingsRepository
     */
    class SiteSettingsRepository
    {
        use \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepositoryTrait;
        const KEY_SITE_FLAG_URL = 'flag_url';
        const KEY_SITE_MENU_LANGUAGE_STYLE = 'menu_flag_style';
        /**
         * @param int|null $siteId
         * @return string
         */
        public function siteFlagUrl(int $siteId = null) : string
        {
        }
        /**
         * @param string $url
         * @param int|null $siteId
         * @return bool
         */
        public function updateSiteFlagUrl(string $url, int $siteId = null) : bool
        {
        }
        /**
         * @param int|null $siteId
         * @return string
         */
        public function siteMenuLanguageStyle(int $siteId = null) : string
        {
        }
        /**
         * @param string $style
         * @param int|null $siteId
         * @return bool
         */
        public function updateMenuLanguageStyle(string $style, int $siteId = null) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting {
    interface SiteSettingsUpdatable
    {
        /**
         * Defines the initial settings of a new site.
         *
         * @param int $siteId
         */
        public function defineInitialSettings(int $siteId);
        /**
         * Updates the settings of an existing site.
         *
         * @param int $siteId
         */
        public function updateSettings(int $siteId);
    }
}
namespace Inpsyde\MultilingualPress\SiteFlags\Core\Admin {
    /**
     * Class SiteSettingsUpdater
     */
    final class SiteSettingsUpdater implements \Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable
    {
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param SiteSettingsRepository $repository
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @inheritdoc
         */
        public function defineInitialSettings(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(int $siteId)
        {
        }
        /**
         * @param int $siteId
         */
        private function updateSiteFlagUrl(int $siteId)
        {
        }
        /**
         * @param int $siteId
         */
        private function updateSiteMenuLanguageStyle(int $siteId)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteFlags\Flag {
    class Factory
    {
        /**
         * @var SiteSettingsRepository
         */
        private $settingsRepository;
        /**
         * @var string
         */
        protected $pathToFlagsFolder;
        /**
         * @var string
         */
        protected $flagImageExtension;
        /**
         * @var string
         */
        protected $pluginPath;
        /**
         * @var string
         */
        protected $pluginUrl;
        public function __construct(\Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository $settingsRepository, string $pathToFlagsFolder, string $flagImageExtension, string $pluginPath, string $pluginUrl)
        {
        }
        /**
         * @param int $siteId
         * @return Flag
         */
        public function create(int $siteId) : \Inpsyde\MultilingualPress\SiteFlags\Flag\Flag
        {
        }
        /**
         * @param int $siteId
         * @return string
         */
        private function flagUrlBySetting(int $siteId) : string
        {
        }
        /**
         * Will return the flag url of a given language.
         *
         * @param Language $language
         * @return string The flag url.
         */
        protected function flag(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : string
        {
        }
    }
    /**
     * Interface Flag
     */
    interface Flag
    {
        /**
         * @return string
         */
        public function url() : string;
        /**
         * @return string
         */
        public function markup() : string;
    }
    /**
     * Class Raster
     *
     * @package Inpsyde\MultilingualPress\SiteFlags\Flag
     */
    final class Raster implements \Inpsyde\MultilingualPress\SiteFlags\Flag\Flag
    {
        /**
         * @var Language
         */
        private $language;
        /**
         * @var string
         */
        private $url;
        /**
         * Raster constructor
         * @param Language $language
         * @param string $url
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Language\Language $language, string $url)
        {
        }
        /**
         * @inheritdoc
         */
        public function url() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function markup() : string
        {
        }
    }
    /**
     * Class Svg
     *
     * @todo Convert to SVG markup
     */
    final class Svg implements \Inpsyde\MultilingualPress\SiteFlags\Flag\Flag
    {
        /**
         * @var string
         */
        private $url;
        /**
         * Svg constructor
         * @param string $url
         */
        public function __construct(string $url)
        {
        }
        /**
         * @inheritdoc
         */
        public function url() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function markup() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteFlags {
    /**
     * Class FlagFilter
     */
    class FlagFilter
    {
        /**
         * @var SiteSettingsRepository
         */
        private $settingsRepository;
        /**
         * @var Factory
         */
        private $flagFactory;
        /**
         * @var string
         */
        private $flagsPath;
        /**
         * NavMenuLanguageStyleFilter constructor
         * @param SiteSettingsRepository $settingsRepository
         * @param Factory $flagFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository $settingsRepository, \Inpsyde\MultilingualPress\SiteFlags\Flag\Factory $flagFactory, string $flagsPath)
        {
        }
        /**
         * Show the flags on nav menu items based on site settings
         *
         * @param string $title
         * @param \WP_Post $item
         * @return string
         */
        public function navMenuItems(string $title, \WP_Post $item) : string
        {
        }
        /**
         * Filter the Language Switcher item flag url
         *
         * @param string $flagUrl The Language Switcher item flag Url
         * @param int $siteId The Language Switcher item site id
         * @return string The filtered Language Switcher item flag Url
         */
        public function languageSwitcherItemFlagUrl(string $flagUrl, int $siteId) : string
        {
        }
        /**
         * Show flags in the table list columns for translated content
         *
         * @param string $languageTag
         * @param int $siteId
         * @return string
         */
        public function tableListPostsRelations(string $languageTag, int $siteId) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service {
    /**
     * Interface for all service provider implementations to be used for dependency management.
     */
    interface ServiceProvider
    {
        /**
         * Registers the provided services on the given container.
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container);
    }
}
namespace Inpsyde\MultilingualPress\Framework\Module {
    /**
     * Interface for all module service provider implementations to be used for dependency management.
     */
    interface ModuleServiceProvider extends \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * Registers the module at the module manager.
         *
         * @param ModuleManager $moduleManager
         * @return bool
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool;
        /**
         * Performs various tasks on module activation.
         *
         * @param Container $container
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container);
    }
}
namespace Inpsyde\MultilingualPress\SiteFlags {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'multilingualpress-site-flags';
        protected const OLD_FLAGS_ADDON_PATH = 'multilingualpress-site-flags/multilingualpress-site-flags.php';
        /**
         * Registers the module at the module manager.
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * Registers the provided services on the given container.
         *
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        public function bootstrapAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        public function bootstrapFrontend(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        public function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @return string
         */
        protected function description() : string
        {
        }
        /**
         * @return bool
         */
        protected function isSiteFlagsAddonActive() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WooCommerce\Brands {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'multilingualpress-woocommerce-brands';
        /**
         * Registers the module at the module manager.
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws \Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * Performs various tasks on module activation.
         *
         * @param Container $container
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Registers the provided services on the given container.
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Get the permalinks structure by WordPress option
         *
         * @return string
         */
        private function permalinksStructure() : string
        {
        }
        /**
         * @return bool
         */
        private function isWooCommerceBrandsActive() : bool
        {
        }
        /**
         * @return mixed
         */
        private function description()
        {
        }
        /**
         * @return string
         */
        private function disabledDescription() : string
        {
        }
        /**
         * Perform necessary actions when the module is not active
         *
         * @param Container $container
         */
        protected function whenModuleIsInactive(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin {
    /**
     * Model for an admin notice.
     */
    class AdminNotice
    {
        const DISMISSIBLE = 8192;
        const IN_ALL_SCREENS = 128;
        const IN_NETWORK_SCREENS = 256;
        const IN_USER_SCREENS = 512;
        const IN_DEFAULT_SCREENS = 1024;
        const TYPE_SUCCESS = 1;
        const TYPE_ERROR = 2;
        const TYPE_INFO = 4;
        const TYPE_WARNING = 8;
        const TYPE_MULTILINGUALPRESS = 16;
        const HOOKS = [self::IN_ALL_SCREENS => 'all_admin_notices', self::IN_DEFAULT_SCREENS => 'admin_notices', self::IN_NETWORK_SCREENS => 'network_admin_notices', self::IN_USER_SCREENS => 'user_admin_notices'];
        const CLASSES = [self::TYPE_ERROR => 'notice-error', self::TYPE_WARNING => 'notice-warning', self::TYPE_SUCCESS => 'notice-success', self::TYPE_INFO => 'notice-info', self::TYPE_MULTILINGUALPRESS => 'notice-multilingualpress'];
        const KSES_ALLOWED = ['a' => ['href' => [], 'title' => [], 'class' => [], 'style' => [], 'target' => []], 'br' => [], 'em' => [], 'strong' => [], 'div' => ['style' => [], 'class' => []]];
        /**
         * @var string[]
         */
        private $content;
        /**
         * @var int
         */
        private $flags;
        /**
         * @var string
         */
        private $title;
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function error(string ...$content) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function info(string ...$content) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function success(string ...$content) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function warning(string ...$content) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function multilingualpress(string ...$content) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param int|null $flags
         * @param string|null $title
         * @param string[] $content
         */
        public function __construct(int $flags = null, string $title = null, string ...$content)
        {
        }
        /**
         * @return AdminNotice
         */
        public function makeDismissible() : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inAllScreens() : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inDefaultScreens() : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inNetworkScreens() : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inUserScreens() : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string $title
         * @return AdminNotice
         */
        public function withTitle(string $title) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return void
         */
        public function render()
        {
        }
        /**
         * @return void
         */
        public function renderNow()
        {
        }
        /**
         * @return string
         */
        public function action() : string
        {
        }
        /**
         * @return string
         */
        private function classes() : string
        {
        }
        /**
         * @param int $screenFlag
         * @return AdminNotice
         */
        private function updateScreen(int $screenFlag) : \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param int|null $flags
         * @return int
         */
        private function normalizeFlags(int $flags = null) : int
        {
        }
        /**
         * @param int $flags
         * @return int
         */
        private function normalizeTypeFlag(int $flags) : int
        {
        }
        /**
         * @param int $flags
         * @return bool
         */
        private function isDismissible(int $flags) : bool
        {
        }
        /**
         * @param int $flags
         * @return int
         */
        private function normalizeScreensFlag(int $flags) : int
        {
        }
        /**
         * @param string $message
         * @return string
         */
        private function kses(string $message) : string
        {
        }
        /**
         * @param array $classes
         * @return array
         */
        private function sanitizeHtmlClasses(array $classes) : array
        {
        }
        /**
         * @param string $classes
         * @return string
         */
        private function sanitizeHtmlClassesByString(string $classes) : string
        {
        }
    }
    /**
     * Tab for all Edit Site pages.
     */
    class EditSiteTab
    {
        /**
         * @var SettingsPageTabData
         */
        private $tabData;
        /**
         * @var SettingsPage
         */
        private $settingsPage;
        /**
         * @param SettingsPageTab $tab
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab $tab)
        {
        }
        /**
         * Registers both the tab and the settings page for the tab.
         *
         * @return bool
         */
        public function register() : bool
        {
        }
        /**
         * @return bool
         */
        private function registerSettingPage() : bool
        {
        }
        /**
         * @return void
         */
        private function removeSubmenuPage()
        {
        }
        /**
         * @return void
         */
        private function fillGlobals()
        {
        }
        /**
         * @return void
         */
        private function filterNetworkAdminLinks()
        {
        }
        /**
         * @return array
         */
        private function tabLinkData() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox {
    interface Action
    {
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool;
    }
    final class Info implements \ArrayAccess
    {
        const PRIORITY_HIGH = 'high';
        const PRIORITY_SORTED = 'sorted';
        const PRIORITY_CORE = 'core';
        const PRIORITY_NORMAL = 'default';
        const PRIORITY_ADVANCED = 'low';
        const PRIORITIES = [self::PRIORITY_HIGH, self::PRIORITY_SORTED, self::PRIORITY_CORE, self::PRIORITY_NORMAL, self::PRIORITY_ADVANCED];
        const CONTEXT_SIDE = 'side';
        const CONTEXT_NORMAL = 'normal';
        const CONTEXT_ADVANCED = 'advanced';
        const CONTEXTS = [self::CONTEXT_SIDE, self::CONTEXT_NORMAL, self::CONTEXT_ADVANCED];
        /**
         * @var array
         */
        private $storage;
        /**
         * @var array
         */
        private $meta = [];
        /**
         * @param string $title
         * @param string $id
         * @param string $context
         * @param string $priority
         */
        public function __construct(string $title, string $id = '', string $context = '', string $priority = '')
        {
        }
        /**
         * @return string
         */
        public function id() : string
        {
        }
        /**
         * @return string
         */
        public function title() : string
        {
        }
        /**
         * @return string
         */
        public function context() : string
        {
        }
        /**
         * @return string
         */
        public function priority() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetExists($offset) : bool
        {
        }
        /**
         * @inheritdoc
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * @inheritdoc
         */
        #[\ReturnTypeWillChange]
        public function offsetSet($offset, $value)
        {
        }
        /**
         * @inheritdoc
         */
        #[\ReturnTypeWillChange]
        public function offsetUnset($offset)
        {
        }
    }
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    interface Metabox
    {
        const SAVE = 'save';
        const SHOW = 'show';
        /**
         * @param string $showOrSave
         * @param Entity $entity
         * @return Info
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info;
        /**
         * Returns the site ID for the meta box.
         * @return int
         */
        public function siteId() : int;
        /**
         * Create an instance of Action for the given entity.
         *
         * @param Entity $entity
         * @return Action
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action;
        /**
         * Check if the given entity is a valid one to be in the metabox.
         *
         * @param Entity $entity
         * @return bool true if is valid, otherwise false.
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity) : bool;
        /**
         * Create the metabox view for a given entity.
         *
         * @param Entity $entity
         * @return View
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View;
    }
    /**
     * Class MetaboxAuthFactory
     * @package Inpsyde\MultilingualPress\Framework\Admin\Metabox
     */
    class MetaboxAuthFactory
    {
        /**
         * @var EntityAuthFactory
         */
        private $entityAuthFactory;
        /**
         * @var NonceFactory
         */
        private $nonceFactory;
        /**
         * MetaboxAuthFactory constructor.
         * @param EntityAuthFactory $entityAuthFactory
         * @param NonceFactory $nonceFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Auth\EntityAuthFactory $entityAuthFactory, \Inpsyde\MultilingualPress\Framework\Factory\NonceFactory $nonceFactory)
        {
        }
        /**
         * Create an instance of Auth by the given Entity
         *
         * @param Entity $entity
         * @param string $metaboxId
         * @param int $metaboxSiteId
         * @return Auth
         * @throws AuthFactoryException
         */
        public function create(\Inpsyde\MultilingualPress\Framework\Entity $entity, string $metaboxId, int $metaboxSiteId) : \Inpsyde\MultilingualPress\Framework\Auth\Auth
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Trait SwitchSiteHelper
     * @package Inpsyde\MultilingualPress\Framework
     */
    trait SwitchSiteTrait
    {
        /**
         * @param int $targetSiteId
         * @return int
         */
        protected function maybeSwitchSite(int $targetSiteId) : int
        {
        }
        /**
         * @param int $originalSiteId
         * @return bool
         */
        protected function maybeRestoreSite(int $originalSiteId) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox {
    /**
     * Class MetaboxUpdater
     * @package Inpsyde\MultilingualPress\Framework\Admin\Metabox
     */
    class MetaboxUpdater
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        const ACTION_UNAUTHORIZED_METABOX_SAVE = 'multilingualpress.unauthorized_box_save';
        const ACTION_SAVE_METABOX = 'multilingualpress.save_metabox';
        const ACTION_SAVED_METABOX = 'multilingualpress.saved_metabox';
        /**
         * @var Request
         */
        private $request;
        /**
         * @var PersistentAdminNotices
         */
        private $persistentAdminNotice;
        /**
         * @var MetaboxAuthFactory
         */
        private $metaboxAuthFactory;
        /**
         * MetaboxUpdater constructor.
         * @param Request $request
         * @param PersistentAdminNotices $notices
         * @param MetaboxAuthFactory $metaboxAuthFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices, \Inpsyde\MultilingualPress\Framework\Admin\Metabox\MetaboxAuthFactory $metaboxAuthFactory)
        {
        }
        /**
         * Save Metabox Data for the given Entity
         *
         * @param Metabox|PostMetabox|TermMetabox $metabox
         * @param string $metaboxId
         * @param Entity $entity
         * @throws AuthFactoryException
         * @throws DomainException
         */
        public function saveMetabox(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox $metabox, string $metaboxId, \Inpsyde\MultilingualPress\Framework\Entity $entity)
        {
        }
        /**
         * Create instance of Action by the given metabox for the given Entity
         *
         * @param Entity $entity
         * @param Metabox $metabox
         * @return Action
         * @throws DomainException
         */
        protected function actionFactory(\Inpsyde\MultilingualPress\Framework\Entity $entity, \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox $metabox) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
    }
    class Metaboxes
    {
        const REGISTER_METABOXES = 'multilingualpress.register_metaboxes';
        const ACTION_INSIDE_METABOX_AFTER = 'multilingualpress.inside_box_after';
        const ACTION_INSIDE_METABOX_BEFORE = 'multilingualpress.inside_box_before';
        const ACTION_SHOW_METABOXES = 'multilingualpress.show_metaboxes';
        const ACTION_SHOWED_METABOXES = 'multilingualpress.showed_metaboxes';
        const ACTION_SAVE_METABOXES = 'multilingualpress.save_metaboxes';
        const ACTION_SAVED_METABOXES = 'multilingualpress.saved_metaboxes';
        const FILTER_SAVE_METABOX_ON_EMPTY_POST = 'multilingualpress.metabox_save_on_empty_post';
        const FILTER_METABOX_ENABLED = 'multilingualpress.metabox_enabled';
        protected const FILTER_TAXONOMY_METABOXES_ORDER = 'multilingualpress.taxonomy_metaboxes_order';
        /**
         * @var RequestGlobalsManipulator
         */
        private $globalsManipulator;
        /**
         * @var PersistentAdminNotices
         */
        private $notices;
        /**
         * @var Metabox[]
         */
        private $boxes = [];
        /**
         * @var bool
         */
        private $locked = true;
        /**
         * @var Entity
         */
        private $entity;
        /**
         * @var string
         */
        private $registeringFor = '';
        /**
         * @var string
         */
        private $saving = '';
        /**
         * @var MetaboxUpdater
         */
        private $metaboxUpdater;
        /**
         * @var PostTypeRepository
         */
        protected $postTypeRepository;
        /**
         * @var ModuleManager
         */
        protected $moduleManager;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\RequestGlobalsManipulator $globalsManipulator, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices, \Inpsyde\MultilingualPress\Framework\Admin\Metabox\MetaboxUpdater $metaboxUpdater, \Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        /**
         * @return void
         */
        public function init()
        {
        }
        /**
         * @param Metabox[] $boxes
         *
         * @return Metaboxes
         */
        public function addBox(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox ...$boxes) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metaboxes
        {
        }
        /**
         * WordPress does not print metaboxes for terms, let's fix this.
         *
         * @param WP_Term $term
         */
        public function printTermBoxes(\WP_Term $term)
        {
        }
        /**
         * @return bool
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        private function initForPost() : bool
        {
        }
        /**
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        private function initForComment() : void
        {
        }
        /**
         * @param string $taxonomy
         *
         * @return bool
         */
        private function initForTerm(string $taxonomy) : bool
        {
        }
        /**
         * @param Entity $entity
         * @param string $showOrSave
         */
        private function prepareTarget(\Inpsyde\MultilingualPress\Framework\Entity $entity, string $showOrSave)
        {
        }
        /**
         * @param Metabox $box
         * @return bool
         */
        private function isBoxEnabled(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox $box) : bool
        {
        }
        /**
         * @param Metabox $box
         * @param string $boxId
         */
        private function addMetabox(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox $box, string $boxId)
        {
        }
        /**
         * @param WP_Post $post
         */
        private function onPostSave(\WP_Post $post)
        {
        }
        /**
         * @param WP_Comment $comment
         */
        protected function onCommentSave(\WP_Comment $comment)
        {
        }
        /**
         * @return void
         * @throws AuthFactoryException
         */
        private function saveMetaBoxes()
        {
        }
        /**
         * Clean up state.
         */
        private function releaseTarget()
        {
        }
        /**
         * Add the metaboxes for given entity.
         *
         * @param Entity $entity
         */
        protected function addMetaBoxes(\Inpsyde\MultilingualPress\Framework\Entity $entity) : void
        {
        }
        /**
         * Perform metabox saving actions for given entity.
         *
         * @param Entity $entity
         */
        protected function saveMetaboxesActions(\Inpsyde\MultilingualPress\Framework\Entity $entity) : void
        {
        }
    }
    final class NoopAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
    }
    interface View
    {
        /**
         * @param Info $info
         * @return void
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info);
    }
    final class NoopView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info)
        {
        }
    }
    /**
     * Can render post metaboxes.
     */
    interface PostMetaboxRendererInterface
    {
        /**
         * Renders the markup for given post ID.
         *
         * @param int $postId The post ID.
         */
        public function render(int $postId) : void;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin {
    class PersistentAdminNotices
    {
        const OPTION_NAME = 'multilingualpress_notices_';
        const DEFAULT_TTL = 300;
        const FILTER_ADMIN_NOTICE_TTL = 'multilingualpress.admin_notice_ttl';
        /**
         * @var array
         */
        private $messages = [];
        /**
         * @var bool[]
         */
        private $printed = [];
        /**
         * @var bool
         */
        private $recorded = false;
        /**
         * @return void
         */
        public function init()
        {
        }
        /**
         * @param AdminNotice $notice
         * @param string|null $onlyOnScreen
         * @return PersistentAdminNotices
         */
        public function add(\Inpsyde\MultilingualPress\Framework\Admin\AdminNotice $notice, string $onlyOnScreen = null) : \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices
        {
        }
        /**
         * @wp-hook admin_notices
         */
        public function doDefaultNotices()
        {
        }
        /**
         * @wp-hook network_admin_notices
         */
        public function doNetworkNotices()
        {
        }
        /**
         * @wp-hook user_admin_notices
         */
        public function doUserNotices()
        {
        }
        /**
         * @wp-hook all_admin_notices
         */
        public function doAllNotices()
        {
        }
        /**
         * Store (or delete) messages on shutdown.
         *
         * @return bool
         */
        public function record() : bool
        {
        }
        /**
         * @param string $action
         * @return bool
         */
        private function doNotices(string $action) : bool
        {
        }
        /**
         * @param array $adminNoticesData
         */
        private function printMessages(array $adminNoticesData)
        {
        }
    }
    class SettingsPage
    {
        const ADMIN_NETWORK = 1;
        const ADMIN_SITE = 0;
        const ADMIN_USER = 2;
        const PARENT_APPEARANCE = 'themes.php';
        const PARENT_COMMENTS = 'edit-comments.php';
        const PARENT_DASHBOARD = 'index.php';
        const PARENT_LINKS = 'link-manager.php';
        const PARENT_MEDIA = 'upload.php';
        const PARENT_NETWORK_SETTINGS = 'settings.php';
        const PARENT_PAGES = 'edit.php?post_type=page';
        const PARENT_PLUGINS = 'plugins.php';
        const PARENT_POSTS = 'edit.php';
        const PARENT_SETTINGS = 'options-general.php';
        const PARENT_SITES = 'sites.php';
        const PARENT_THEMES = 'themes.php';
        const PARENT_TOOLS = 'tools.php';
        const PARENT_USER_PROFILE = 'profile.php';
        const PARENT_USERS = 'users.php';
        const PARENT_MULTILINGUALPRESS = 'multilingualpress';
        /**
         * @var \stdClass
         */
        private $args;
        /**
         * @var string
         */
        private $hookName = '';
        /**
         * @var string
         */
        private $parent = '';
        /**
         * @var string
         */
        private $url;
        /**
         * @param int $admin
         * @param string $pageTitle
         * @param string $menuTitle
         * @param string $capability
         * @param string $menuSlug
         * @param SettingsPageView $view
         * @param string $iconUrl
         * @param int|null $position
         */
        public function __construct(int $admin, string $pageTitle, string $menuTitle, string $capability, string $menuSlug, \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView $view, string $iconUrl = '', int $position = null)
        {
        }
        /**
         * @param int $admin
         * @param string $parent
         * @param string $pageTitle
         * @param string $menuTitle
         * @param string $capability
         * @param string $menuSlug
         * @param SettingsPageView $view
         * @return SettingsPage
         */
        public static function withParent(int $admin, string $parent, string $pageTitle, string $menuTitle, string $capability, string $menuSlug, \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView $view) : \Inpsyde\MultilingualPress\Framework\Admin\SettingsPage
        {
        }
        /**
         * @return string
         */
        public function capability() : string
        {
        }
        /**
         * @return string
         */
        public function hookName() : string
        {
        }
        /**
         * @return bool
         */
        public function register() : bool
        {
        }
        /**
         * @return string
         */
        public function menuSlug() : string
        {
        }
        /**
         * @return string
         */
        public function pageTitle() : string
        {
        }
        /**
         * Returns the full URL.
         *
         * @return string
         */
        public function url() : string
        {
        }
        /**
         * Returns the action for registering the page.
         *
         * @return string
         */
        private function action() : string
        {
        }
        /**
         * Returns the callback for adding the page to the admin menu.
         *
         * @return callable
         */
        private function callback() : callable
        {
        }
    }
    /**
     * Interface for all settings page tab data implementations.
     */
    interface SettingsPageTabDataAccess
    {
        /**
         * Returns the capability.
         *
         * @return string
         */
        public function capability() : string;
        /**
         * Returns the ID.
         *
         * @return string
         */
        public function id() : string;
        /**
         * Returns the slug.
         *
         * @return string
         */
        public function slug() : string;
        /**
         * Returns the title.
         *
         * @return string
         */
        public function title() : string;
    }
    /**
     * Settings page tab.
     */
    class SettingsPageTab implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess
    {
        /**
         * @var SettingsPageTabDataAccess
         */
        private $data;
        /**
         * @var SettingsPageView
         */
        private $view;
        /**
         * @param SettingsPageTabDataAccess $data
         * @param SettingsPageView $view
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess $data, \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView $view)
        {
        }
        /**
         * @inheritdoc
         */
        public function capability() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function data() : \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function slug() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * Returns the view object.
         *
         * @return SettingsPageView
         */
        public function view() : \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
        {
        }
    }
    /**
     * Settings page tab data structure.
     */
    final class SettingsPageTabData implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess
    {
        /**
         * @var string
         */
        private $capability;
        /**
         * @var string
         */
        private $id;
        /**
         * @var string
         */
        private $slug;
        /**
         * @var string
         */
        private $title;
        /**
         * @param string $id
         * @param string $title
         * @param string $slug
         * @param string $capability
         */
        public function __construct(string $id, string $title, string $slug, string $capability = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function capability() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function slug() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Interface for all settings page view implementations.
     */
    interface SettingsPageView
    {
        /**
         * Renders the markup.
         */
        public function render();
    }
    /**
     * Model for a custom column in the Sites list table in the Network Admin.
     */
    class SitesListTableColumn
    {
        /**
         * @var string
         */
        private $columnName;
        /**
         * @var string
         */
        private $columnLabel;
        /**
         * @var callable
         */
        private $renderCallback;
        /**
         * @param string $columnName
         * @param string $columnLabel
         * @param callable $renderCallback
         */
        public function __construct(string $columnName, string $columnLabel, callable $renderCallback)
        {
        }
        /**
         * Registers the column methods by using the appropriate WordPress hooks.
         */
        public function register()
        {
        }
        /**
         * Renders the column content.
         *
         * @param string $column
         * @param int $siteId
         * @return void
         */
        public function renderContent(string $column, int $siteId)
        {
        }
    }
    /**
     * Represents the custom column for MultilingualPress in admin entity list view.
     */
    interface TranslationColumnInterface
    {
        /**
         * The name of the column.
         *
         * @return string
         */
        public function name() : string;
        /**
         * The title of the column.
         *
         * @return string
         */
        public function title() : string;
        /**
         * The value of the column for given entity ID.
         *
         * @param int $id The entity ID.
         * @return string
         */
        public function value(int $id) : string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Api {
    /**
     * Interface for all content relations API implementations.
     */
    interface ContentRelations
    {
        const CONTENT_IDS_CACHE_KEY = 'contentIds';
        const RELATIONS_CACHE_KEY = 'relations';
        const HAS_SITE_RELATIONS_CACHE_KEY = 'hasSiteRelations';
        const CONTENT_TYPE_POST = 'post';
        const CONTENT_TYPE_TERM = 'term';
        public const CONTENT_TYPE_COMMENT = 'comment';
        const FILTER_POST_TYPE = 'multilingualpress.content_relations_post_type';
        const FILTER_POST_STATUS = 'multilingualpress.content_relations_post_status';
        const FILTER_TAXONOMY = 'multilingualpress.content_relations_taxonomy';
        /**
         * Creates a relationship for the given content ids provided as an array with site IDs as keys
         * and content IDs as values.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return int
         * @throws NonexistentTable
         */
        public function createRelationship(array $contentIds, string $type) : int;
        /**
         * Deletes all relations for content elements that don't exist (anymore).
         *
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForInvalidContent(string $type) : bool;
        /**
         * Deletes all relations for sites that don't exist (anymore).
         *
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForInvalidSites() : bool;
        /**
         * Deletes all relations for the site with the given ID.
         *
         * @param int $siteId
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForSite(int $siteId) : bool;
        /**
         * Deletes a relation according to the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteRelation(array $contentIds, string $type) : bool;
        /**
         * Copies all relations of the given (or any) content type from the given source site to the
         * given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same content IDs.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         * @return int
         * @throws NonexistentTable
         */
        public function duplicateRelations(int $sourceSiteId, int $targetSiteId) : int;
        /**
         * Returns the content ID for the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @return int
         * @throws NonexistentTable
         */
        public function contentId(int $relationshipId, int $siteId) : int;
        /**
         * Returns the content ID in the given target site for the given content element.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @param int $targetSiteId
         * @return int
         * @throws NonexistentTable
         */
        public function contentIdForSite(int $siteId, int $contentId, string $type, int $targetSiteId) : int;
        /**
         * Returns the content IDs for the given relationship ID.
         *
         * @param int $relationshipId
         * @return int[]
         * @throws NonexistentTable
         */
        public function contentIds(int $relationshipId) : array;
        /**
         * Returns all relations for the given content element.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @return int[]
         * @throws NonexistentTable
         */
        public function relations(int $siteId, int $contentId, string $type) : array;
        /**
         * Returns the relationship ID for the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @param bool $create
         * @return int
         * @throws NonexistentTable
         */
        public function relationshipId(array $contentIds, string $type, bool $create = false) : int;
        /**
         * Checks if the site with the given ID has any relations of the given (or any) content type.
         *
         * @param int $siteId
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function hasSiteRelations(int $siteId, string $type = '') : bool;
        /**
         * Relates all posts between the given source site and the given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same post IDs.
         * Furthermore, the current site is assumed to be either the source site or the destination site.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return bool
         * @throws NonexistentTable
         */
        public function relateAllPosts(int $sourceSite, int $targetSite) : bool;
        /**
         * Relates all terms between the given source site and the given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same term taxonomy IDs.
         * Furthermore, the current site is assumed to be either the source site or the destination site.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return bool
         * @throws NonexistentTable
         */
        public function relateAllTerms(int $sourceSite, int $targetSite) : bool;
        /**
         * Sets a relation according to the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @param int $contentId
         * @return bool
         * @throws NonexistentTable
         */
        public function saveRelation(int $relationshipId, int $siteId, int $contentId) : bool;
        /**
         * Relates all comments between the given source site and the given destination site.
         *
         * @param int $sourceSite The source site ID.
         * @param int $targetSite The Target site ID.
         * @return bool true if the comments are related, false if not.
         * @throws RuntimeException if problem relating.
         */
        public function relateAllComments(int $sourceSite, int $targetSite) : bool;
    }
    /**
     * Interface for all content relationship meta API implementations.
     */
    interface ContentRelationshipMetaInterface
    {
        /**
         * Updates or creates(if doesn't exist) the relationship meta with given arguments.
         *
         * @param int $relationshipId The Relationship ID.
         * @param string $metaKey The meta key.
         * @param string $metaValue The meta value.
         * @throws RuntimeException if problem creating.
         */
        public function updateRelationshipMeta(int $relationshipId, string $metaKey, string $metaValue) : void;
        /**
         * Gets the relationship meta value with given ID and meta key.
         *
         * @param int $relationshipId The Relationship ID.
         * @param string $metaKey The meta key.
         * @return string The meta value.
         */
        public function relationshipMetaValue(int $relationshipId, string $metaKey) : string;
        /**
         * Gets the relationship meta value for given post ID and meta key.
         *
         * @param int $postId The post ID.
         * @param string $metaKey The meta key.
         * @return string The meta value.
         * @throws RuntimeException if problem getting.
         */
        public function relationshipMetaValueByPostId(int $postId, string $metaKey) : string;
        /**
         * Deletes the relationship meta by given relationship ID.
         *
         * @param int $relationshipId The Relationship ID.
         * @return bool true if the relationship meta is deleted, otherwise false.
         * @throws RuntimeException if problem deleting.
         */
        public function deleteRelationshipMeta(int $relationshipId) : bool;
    }
    /**
     * Interface for all languages API implementations.
     */
    interface Languages
    {
        /**
         * Deletes the language with the given ID.
         *
         * @param int $id
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteLanguage(int $id) : bool;
        /**
         * Returns an array with objects of all available languages.
         *
         * @return Language[]
         * @throws NonexistentTable
         */
        public function allLanguages() : array;
        /**
         * Returns the complete language data of all sites.
         *
         * @return Language[]
         * @throws NonexistentTable
         */
        public function allAssignedLanguages() : array;
        /**
         * Returns the language for the given arguments.
         *
         * @param string $column
         * @param string|int $value
         * @return Language
         * @throws NonexistentTable
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function languageBy(string $column, $value) : \Inpsyde\MultilingualPress\Framework\Language\Language;
        /**
         * Creates a new language entry according to the given data.
         *
         * @param array $languageData
         * @return int
         * @throws NonexistentTable
         */
        public function insertLanguage(array $languageData) : int;
        /**
         * Updates the language with the given ID according to the given data.
         *
         * @param int $id
         * @param array $data
         * @return bool
         * @throws NonexistentTable
         */
        public function updateLanguage(int $id, array $data) : bool;
    }
    /**
     * Interface for all site relations API implementations.
     */
    interface SiteRelations
    {
        const RELATED_SITE_IDS_CACHE_KEY = 'relatedSiteIds';
        const ALL_RELATIONS_CACHE_KEY = 'allRelations';
        /**
         * Deletes the relationship between the given sites. If only one site is given, all its relations
         * will be deleted.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return int
         * @throws NonexistentTable
         */
        public function deleteRelation(int $sourceSite, int $targetSite = 0) : int;
        /**
         * Returns an array with site IDs as keys and arrays with the IDs of all related sites as values.
         *
         * @return int[]
         * @throws NonexistentTable
         */
        public function allRelations() : array;
        /**
         * Returns an array holding the IDs of all sites related to the site with the given ID.
         *
         * @param int $siteId
         * @param bool $includeSite
         * @return int[]
         * @throws NonexistentTable
         */
        public function relatedSiteIds(int $siteId, bool $includeSite = false) : array;
        /**
         * Creates relations between one site and one or more other sites.
         *
         * @param int $baseSiteId
         * @param int[] $siteIds
         * @return int
         * @throws NonexistentTable
         */
        public function insertRelations(int $baseSiteId, array $siteIds) : int;
        /**
         * Sets the relations for the site with the given ID.
         *
         * @param int $baseSiteId
         * @param int[] $siteIds
         * @return int
         * @throws NonexistentTable
         */
        public function relateSites(int $baseSiteId, array $siteIds) : int;
    }
    class Translation
    {
        const FILTER_URL = 'multilingualpress.translation_url';
        const REMOTE_TITLE = 'remote_title';
        const REMOTE_URL = 'remote_url';
        const REMOTE_CONTENT_ID = 'target_content_id';
        const REMOTE_SITE_ID = 'target_site_id';
        const SOURCE_SITE_ID = 'source_site_id';
        const TYPE = 'type';
        const KEYS = [self::REMOTE_TITLE => 'is_string', self::REMOTE_URL => 'is_string', self::REMOTE_CONTENT_ID => 'is_int', self::REMOTE_SITE_ID => 'is_int', self::SOURCE_SITE_ID => 'is_int', self::TYPE => 'is_string'];
        /**
         * @var array
         */
        private $data = [];
        /**
         * @var Language|null
         */
        private $language;
        /**
         * @param Language|null $language
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Language\Language $language = null)
        {
        }
        /**
         * @param Translation $translation
         * @return Translation
         */
        public function merge(\Inpsyde\MultilingualPress\Framework\Api\Translation $translation) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return Language
         */
        public function language() : \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * @return string
         */
        public function remoteTitle() : string
        {
        }
        /**
         * @param string $title
         * @return Translation
         */
        public function withRemoteTitle(string $title) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return string
         */
        public function remoteUrl() : string
        {
        }
        /**
         * @param Url $url
         * @return Translation
         */
        public function withRemoteUrl(\Inpsyde\MultilingualPress\Framework\Url\Url $url) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return int
         */
        public function remoteContentId() : int
        {
        }
        /**
         * @param int $contentId
         * @return Translation
         */
        public function withRemoteContentId(int $contentId) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return int
         */
        public function remoteSiteId() : int
        {
        }
        /**
         * @param int $siteId
         * @return Translation
         */
        public function withRemoteSiteId(int $siteId) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return int
         */
        public function sourceSiteId() : int
        {
        }
        /**
         * @param int $siteId
         * @return Translation
         */
        public function withSourceSiteId(int $siteId) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return string
         */
        public function type() : string
        {
        }
        /**
         * @param string $type
         * @return Translation
         */
        public function withType(string $type) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @param string $key
         * @return string|int|bool|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        private function property(string $key)
        {
        }
    }
    class TranslationSearchArgs
    {
        const CONTENT_ID = 'content_id';
        const INCLUDE_BASE = 'include_base';
        const POST_STATUS = 'post_status';
        const POST_TYPE = 'post_type';
        const SEARCH_TERM = 'search_term';
        const SITE_ID = 'site_id';
        const STRICT = 'strict';
        const TYPE = 'type';
        const KEYS = [self::CONTENT_ID => 'is_int', self::INCLUDE_BASE => null, self::POST_STATUS => 'is_array', self::POST_TYPE => 'is_string', self::SEARCH_TERM => 'is_string', self::SITE_ID => 'is_int', self::STRICT => null, self::TYPE => 'is_string'];
        /**
         * @var array
         */
        private $data;
        /**
         * @param WordpressContext $context
         * @param array $data
         * @return static
         */
        public static function forContext(\Inpsyde\MultilingualPress\Framework\WordpressContext $context, array $data = []) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @param array $data
         */
        public function __construct(array $data = [])
        {
        }
        /**
         * @return int|null
         */
        public function contentId()
        {
        }
        /**
         * @param int $contentId
         * @return static
         */
        public function forContentId(int $contentId) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return bool
         */
        public function shouldIncludeBase() : bool
        {
        }
        /**
         * @return static
         */
        public function includeBase() : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return static
         */
        public function dontIncludeBase() : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return array
         */
        public function postStatus() : array
        {
        }
        /**
         * @param string[] $postStatus
         * @return static
         */
        public function forPostStatus(string ...$postStatus) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return null|string
         */
        public function postType()
        {
        }
        /**
         * @param string $postType
         * @return static
         */
        public function forPostType(string $postType) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return null|string
         */
        public function searchTerm()
        {
        }
        /**
         * @param string $searchTerm
         * @return static
         */
        public function searchFor(string $searchTerm) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return int|null
         */
        public function siteId()
        {
        }
        /**
         * @param int $siteId
         * @return static
         */
        public function forSiteId(int $siteId) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return bool
         */
        public function isStrict() : bool
        {
        }
        /**
         * @return static
         */
        public function makeStrictSearch() : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return static
         */
        public function makeNotStrictSearch() : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return string
         */
        public function type() : string
        {
        }
        /**
         * @param string $type
         * @return static
         */
        public function forType(string $type) : \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return array
         */
        public function toArray() : array
        {
        }
        /**
         * @param string $key
         * @return string|int|bool|array|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        private function property(string $key)
        {
        }
    }
    /**
     * Interface for all translations API implementations.
     */
    interface Translations
    {
        /**
         * Returns all translations according to the given arguments.
         *
         * @param TranslationSearchArgs $args
         * @return Translation[]
         */
        public function searchTranslations(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : array;
        /**
         * Registers the given translator for the given type.
         *
         * @param Translator $translator
         * @param string $type
         * @return bool
         */
        public function registerTranslator(\Inpsyde\MultilingualPress\Framework\Translator\Translator $translator, string $type) : bool;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Asset {
    /**
     * Interface for all asset implementations.
     */
    interface Asset
    {
        /**
         * Returns the dependencies.
         *
         * @return string[]
         */
        public function dependencies() : array;
        /**
         * Returns the handle.
         *
         * @return string
         */
        public function handle() : string;
        /**
         * Returns the file URL.
         *
         * @return AssetLocation
         */
        public function location() : \Inpsyde\MultilingualPress\Framework\Asset\AssetLocation;
        /**
         * Returns the file version.
         *
         * @return string|null
         */
        public function version();
        /**
         * Returns the handle.
         *
         * @return string
         */
        public function __toString() : string;
    }
    /**
     * Class AssetException
     * @package Inpsyde\MultilingualPress\Framework\Asset
     */
    class AssetException extends \RuntimeException
    {
        /**
         * When trying to enqueue a script not yet registered
         *
         * @param string $handle
         * @return AssetException
         */
        public static function forWhenEnqueuingScriptNotRegistered(string $handle) : self
        {
        }
        /**
         * Add script data for Script which not exists
         *
         * @param string $handle
         * @return AssetException
         */
        public static function addingScriptDataWhenScriptDoesNotExists(string $handle) : self
        {
        }
    }
    /**
     * Asset location data type.
     */
    class AssetLocation
    {
        /**
         * @var string
         */
        private $file;
        /**
         * @var string
         */
        private $path;
        /**
         * @var string
         */
        private $url;
        /**
         * @param string $file
         * @param string $path
         * @param string $url
         */
        public function __construct(string $file, string $path, string $url)
        {
        }
        /**
         * Returns the relative file name (or path).
         *
         * @return string
         */
        public function file() : string
        {
        }
        /**
         * Returns the local path to the directory containing the file.
         *
         * @return string
         */
        public function path() : string
        {
        }
        /**
         * Returns the public URL for the directory containing the file.
         *
         * @return string
         */
        public function url() : string
        {
        }
    }
    /**
     * Managing instance for all asset-specific tasks.
     */
    class AssetManager
    {
        /**
         * Store the asset handle to prevent to add data multiple times
         *
         * @var array
         */
        private static $dataAddedFor = [];
        /**
         * @var Script[]
         */
        private $scripts = [];
        /**
         * @var Style[]
         */
        private $styles = [];
        /**
         * Register the given script.
         *
         * @param Script $script
         * @return static
         */
        public function registerScript(\Inpsyde\MultilingualPress\Framework\Asset\Script $script) : \Inpsyde\MultilingualPress\Framework\Asset\AssetManager
        {
        }
        /**
         * Register the given style.
         *
         * @param Style $style
         * @return AssetManager
         */
        public function registerStyle(\Inpsyde\MultilingualPress\Framework\Asset\Style $style) : \Inpsyde\MultilingualPress\Framework\Asset\AssetManager
        {
        }
        /**
         * Enqueues the script with the given handle.
         *
         * @param string $handle
         * @param bool $inFooter
         * @param string $enqueueAction
         * @return AssetManager
         * @throws AssetException
         */
        public function enqueueScript(string $handle, bool $inFooter = true, string $enqueueAction = null) : self
        {
        }
        /**
         * Enqueues the script with the given handle.
         *
         * @param string $handle
         * @param string $objectName
         * @param array $data
         * @param bool $inFooter
         * @return AssetManager
         * @throws AssetException
         */
        public function enqueueScriptWithData(string $handle, string $objectName, array $data, bool $inFooter = true) : self
        {
        }
        /**
         * Enqueues the style with the given handle.
         *
         * @param string $handle
         * @param string $enqueueAction
         * @return AssetManager
         * @throws AssetException
         */
        public function enqueueStyle(string $handle, string $enqueueAction = null) : self
        {
        }
        /**
         * Adds the given data to the given script, and handles it in case the script
         * has been enqueued already.
         *
         * @param string $handle
         * @param string $objectName
         * @param array $data
         * @return AssetManager
         * @throws AssetException
         */
        private function addScriptData(string $handle, string $objectName, array $data) : \Inpsyde\MultilingualPress\Framework\Asset\AssetManager
        {
        }
        /**
         * Handles potential data that has been added to the script after it was
         * enqueued, and then clears the data.
         *
         * @param Script $script
         */
        private function handleScriptData(\Inpsyde\MultilingualPress\Framework\Asset\Script $script)
        {
        }
        /**
         * Either executes the given callback or hooks it to the appropriate enqueue
         * action, depending on the context.
         *
         * @param callable $callback
         * @param string $enqueueAction
         */
        private function enqueue(callable $callback, string $enqueueAction = null)
        {
        }
        /**
         * Register assets
         *
         * @param callable $callback
         */
        protected function register(callable $callback)
        {
        }
        /**
         * Returns the appropriate action for enqueueing assets.
         *
         * @return string
         */
        private function enqueueAction() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Interface Stringable
     * @package Inpsyde\MultilingualPress\Framework
     */
    interface Stringable
    {
        /**
         * Retrieve String
         *
         * @return string
         */
        public function __toString() : string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Url {
    /**
     * Interface for all URL data type implementations.
     */
    interface Url extends \Inpsyde\MultilingualPress\Framework\Stringable
    {
    }
}
namespace Inpsyde\MultilingualPress\Framework\Asset {
    /**
     * Interface for all asset URL data type implementations, providing a file version.
     */
    interface AssetUrl extends \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * Returns the file version.
         *
         * @return string
         */
        public function version() : string;
    }
    /**
     * Asset URL data type implementation aware of debug mode and thus potentially
     * minified asset files.
     */
    final class MaybeMinifiedAssetUrl implements \Inpsyde\MultilingualPress\Framework\Asset\AssetUrl
    {
        /**
         * @var string
         */
        private $url = '';
        /**
         * @var string
         */
        private $version = '';
        /**
         * Returns a new URL object, instantiated according to the given location object.
         *
         * @param AssetLocation $location
         * @return MaybeMinifiedAssetUrl
         */
        public static function fromLocation(\Inpsyde\MultilingualPress\Framework\Asset\AssetLocation $location) : \Inpsyde\MultilingualPress\Framework\Asset\MaybeMinifiedAssetUrl
        {
        }
        /**
         * @param string $filename
         * @param string $dirPath
         * @param string $dirUrl
         */
        public function __construct(string $filename, string $dirPath, string $dirUrl)
        {
        }
        /**
         * @inheritdoc
         */
        public function __toString() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function version() : string
        {
        }
        /**
         * Returns the name of the minified version of the given file if it exists
         * and not debugging, otherwise the unmodified file.
         *
         * @param string $filename
         * @param string $dirPath
         * @return string
         */
        private function filename(string $filename, string $dirPath) : string
        {
        }
        /**
         * Returns the given file with ".min" infix, if not there already.
         *
         * @param string $file
         * @return string
         */
        private function minified(string $file) : string
        {
        }
    }
    /**
     * Interface for all script data type implementations.
     */
    interface Script extends \Inpsyde\MultilingualPress\Framework\Asset\Asset
    {
        /**
         * Makes the given data available for the script.
         *
         * @param string $jsObjectName
         * @param array $jsObjectData
         * @return Script
         */
        public function addData(string $jsObjectName, array $jsObjectData) : \Inpsyde\MultilingualPress\Framework\Asset\Script;
        /**
         * Returns all data to be made available for the script.
         *
         * @return array[]
         */
        public function data() : array;
    }
    /**
     * Interface for all style data type implementations.
     */
    interface Style extends \Inpsyde\MultilingualPress\Framework\Asset\Asset
    {
        /**
         * @param string $conditional
         * @return Style
         */
        public function addConditional(string $conditional) : \Inpsyde\MultilingualPress\Framework\Asset\Style;
        /**
         * @return string
         */
        public function media() : string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Auth {
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    interface Auth
    {
        /**
         * Check if the current http request is authorized
         *
         * @return bool
         */
        public function isAuthorized() : bool;
    }
    /**
     * Class AuthFactory
     * @package Inpsyde\MultilingualPress\Framework\Auth
     */
    class AuthFactory
    {
        /**
         * Create and Auth instance
         *
         * @param Nonce $nonce
         * @param Capability $capability
         * @return Auth
         */
        public function create(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Auth\Capability $capability) : \Inpsyde\MultilingualPress\Framework\Auth\Auth
        {
        }
    }
    /**
     * Class AuthFactoryException
     * @package Inpsyde\MultilingualPress\Framework\Auth
     */
    class AuthFactoryException extends \RuntimeException
    {
        /**
         * Create a new Exception because Entity is not valid
         *
         * @return AuthFactoryException
         */
        public static function becauseEntityIsInvalid() : self
        {
        }
    }
    /**
     * Interface Capability
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    interface Capability
    {
        /**
         * Check if a capability is valid
         *
         * @return bool True if valid, false otherwise
         */
        public function isValid() : bool;
    }
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    final class CommentAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @var WP_Comment
         */
        private $comment;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param WP_Comment $comment
         * @param Nonce $nonce
         */
        public function __construct(\WP_Comment $comment, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function isAuthorized() : bool
        {
        }
    }
    /**
     * Class EntityAuthFactory
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    class EntityAuthFactory
    {
        /**
         * @param Entity $entity
         * @param Nonce $nonce
         * @return Auth
         * @throws AuthFactoryException
         */
        public function create(\Inpsyde\MultilingualPress\Framework\Entity $entity, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce) : \Inpsyde\MultilingualPress\Framework\Auth\Auth
        {
        }
    }
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    final class PostAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @var WP_Post
         */
        private $post;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param WP_Post $post
         * @param Nonce $nonce
         */
        public function __construct(\WP_Post $post, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function isAuthorized() : bool
        {
        }
    }
    /**
     * Class RequestAuth
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    final class RequestAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var string
         */
        private $capability;
        /**
         * Validator constructor
         * @param Nonce $nonce
         * @param $capability
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Auth\Capability $capability)
        {
        }
        /**
         * @inheritDoc
         */
        public function isAuthorized() : bool
        {
        }
    }
    /**
     * Class TermAuth
     * @package Inpsyde\MultilingualPress\Framework\Auth
     */
    final class TermAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @var WP_Term
         */
        private $term;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param WP_Term $term
         * @param Nonce $nonce
         */
        public function __construct(\WP_Term $term, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function isAuthorized() : bool
        {
        }
    }
    /**
     * Class WpUserCapability
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    class WpUserCapability implements \Inpsyde\MultilingualPress\Framework\Auth\Capability
    {
        /**
         * @var WP_User
         */
        private $user;
        /**
         * @var string
         */
        private $capability;
        /**
         * @var int
         */
        private $id;
        /**
         * WpCurrentUserCapability constructor
         * @param WP_User $user
         * @param string $capability
         * @param int $id
         */
        public function __construct(\WP_User $user, string $capability, int $id)
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Provides access to the correct basedir and baseurl paths of the current site's
     * uploads folder.
     */
    class BasePathAdapter
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        /**
         * @var string[][]
         */
        private $uploadsDirs = [];
        /**
         * Returns the correct basedir path of the current site's uploads folder.
         *
         * @return string
         */
        public function basedir() : string
        {
        }
        /**
         * Returns the correct base url path for the give site
         *
         * @param int $siteId
         * @return string
         */
        public function basedirForSite(int $siteId) : string
        {
        }
        /**
         * Returns the correct baseurl path of the current site's uploads folder.
         *
         * @return string
         */
        public function baseurl() : string
        {
        }
        /**
         * Returns the correct baseurl path for the given site
         *
         * @param int $siteId
         * @return string
         */
        public function baseurlForSite(int $siteId) : string
        {
        }
        /**
         * Returns the current site's uploads folder paths.
         *
         * @return string[]
         */
        private function uploadsDir() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache {
    /**
     * A factory for Cache pool objects.
     */
    class CacheFactory
    {
        /**
         * @var string
         */
        private $prefix;
        /**
         * @var ClassResolver
         */
        private $classResolver;
        /**
         * @param string $prefix
         * @param string $poolDefaultClass
         */
        public function __construct(string $prefix = '', string $poolDefaultClass = \Inpsyde\MultilingualPress\Framework\Cache\Pool\WpCachePool::class)
        {
        }
        /**
         * @return string
         */
        public function prefix() : string
        {
        }
        /**
         * @param string $namespace
         * @param CacheDriver|null $driver
         * @return CachePool
         */
        public function create(string $namespace, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver = null) : \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * @param string $namespace
         * @param CacheDriver|null $driver
         * @return CachePool
         * @throws InvalidCacheDriver If a site-specific is used instead of a network one.
         */
        public function createForNetwork(string $namespace, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver = null) : \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * @param string $namespace
         * @return CachePool
         */
        public function createEthereal(string $namespace) : \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * @param string $namespace
         * @return CachePool
         */
        public function createEtherealForNetwork(string $namespace) : \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Driver {
    interface CacheDriver
    {
        const FOR_NETWORK = 32;
        /**
         * @return bool
         */
        public function isNetwork() : bool;
        /**
         * Reads a value from the cache.
         *
         * @param string $namespace
         * @param string $name
         * @return Value
         */
        public function read(string $namespace, string $name) : \Inpsyde\MultilingualPress\Framework\Cache\Item\Value;
        /**
         * Write a value to the cache.
         *
         * @param string $namespace
         * @param string $name
         * @param mixed $value
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value) : bool;
        /**
         * Delete a value from the cache.
         *
         * @param string $namespace
         * @param string $name
         * @return bool
         *
         * phpcs:enable
         */
        public function delete(string $namespace, string $name) : bool;
    }
    /**
     * Cache driver implementation that vanish with request.
     * Useful in tests or to share things that should never survive a single request
     * without polluting classes with many static variables.
     */
    final class EphemeralCacheDriver implements \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver
    {
        const NOOP = 8192;
        /**
         * @var array
         */
        private static $cache = [];
        /**
         * @var bool
         */
        private $isNetwork;
        /**
         * @var bool
         */
        private $noop;
        /**
         * @param int $flags
         */
        public function __construct(int $flags = 0)
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function read(string $namespace, string $name) : \Inpsyde\MultilingualPress\Framework\Cache\Item\Value
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $namespace, string $name) : bool
        {
        }
        /**
         * @param string $namespace
         * @param string $name
         * @return string
         */
        private function buildKey(string $namespace, string $name) : string
        {
        }
    }
    final class WpObjectCacheDriver implements \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver
    {
        /**
         * @var string[]
         */
        private static $globalNamespaces = [];
        /**
         * @var bool
         */
        private $isNetwork;
        /**
         * @param int $flags
         */
        public function __construct(int $flags = 0)
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function read(string $namespace, string $name) : \Inpsyde\MultilingualPress\Framework\Cache\Item\Value
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $namespace, string $name) : bool
        {
        }
        /**
         * @param string $namespace
         */
        private function maybeGlobal(string $namespace)
        {
        }
    }
    /**
     * A driver implementation that uses WordPress transient functions.
     *
     * Two gotchas:
     * 1) when using external object cache, prefer object cache driver, WP will use object cache anyway;
     * 2) avoid to store `false` using this driver because it can't be disguised from a cache miss.
     */
    final class WpTransientDriver implements \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver
    {
        /**
         * @var bool
         */
        private $isNetwork;
        /**
         * @param int $flags
         */
        public function __construct(int $flags = 0)
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function read(string $namespace, string $name) : \Inpsyde\MultilingualPress\Framework\Cache\Item\Value
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $namespace, string $name) : bool
        {
        }
        /**
         * Site transients limits key to 40 characters or less.
         * This method builds a key that is unique per namespace and key and is 39
         * characters or less.
         *
         * @param string $namespace
         * @param string $name
         * @return string
         */
        private function buildKey(string $namespace, string $name) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Exception {
    class Exception extends \Exception
    {
    }
    class BadCacheItemRegistration extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        /**
         * @return BadCacheItemRegistration
         */
        public static function forWrongTiming() : \Inpsyde\MultilingualPress\Framework\Cache\Exception\BadCacheItemRegistration
        {
        }
        /**
         * @param string $key
         * @return BadCacheItemRegistration
         */
        public static function forKeyUsedForNetwork(string $key) : \Inpsyde\MultilingualPress\Framework\Cache\Exception\BadCacheItemRegistration
        {
        }
        /**
         * @param string $key
         * @return BadCacheItemRegistration
         */
        public static function forKeyUsedForSite(string $key) : \Inpsyde\MultilingualPress\Framework\Cache\Exception\BadCacheItemRegistration
        {
        }
    }
    class InvalidCacheArgument extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        /**
         * @param string $namespace
         * @param string $key
         * @return InvalidCacheArgument
         */
        public static function forNamespaceAndKey(string $namespace, string $key) : \Inpsyde\MultilingualPress\Framework\Cache\Exception\InvalidCacheArgument
        {
        }
    }
    class InvalidCacheDriver extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        const SITE_DRIVER_AS_NETWORK = 1;
        /**
         * @param CacheDriver $driver
         * @return InvalidCacheDriver
         */
        public static function forSiteDriverAsNetwork(\Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver) : \Inpsyde\MultilingualPress\Framework\Cache\Exception\InvalidCacheDriver
        {
        }
    }
    class NotRegisteredCacheItem extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        /**
         * @param string $namespace
         * @param string $key
         * @return NotRegisteredCacheItem
         */
        public static function forNamespaceAndKey(string $namespace, string $key) : \Inpsyde\MultilingualPress\Framework\Cache\Exception\NotRegisteredCacheItem
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Item {
    interface CacheItem
    {
        const LIFETIME_IN_SECONDS = HOUR_IN_SECONDS;
        /**
         * Cache item key.
         *
         * @return string
         */
        public function key() : string;
        /**
         * Cache item value.
         *
         * @return mixed
         */
        public function value();
        /**
         * Check if the cache item was a hit. Necessary to disguise null values stored in cache.
         *
         * @return bool
         */
        public function isHit() : bool;
        /**
         * Check if the cache item is expired.
         *
         * @return bool
         */
        public function isExpired() : bool;
        /**
         * Sets the value for the cache item.
         *
         * @param mixed $value
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function fillWith($value) : bool;
        /**
         * Delete the cache item from its storage and ensure that next value() call return null.
         *
         * @return bool
         *
         * phpcs:enable
         */
        public function delete() : bool;
        /**
         * Sets a specific time to live for the item.
         *
         * @param int $ttl
         * @return CacheItem
         */
        public function liveFor(int $ttl) : \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
        /**
         * Push values to storage driver.
         *
         * @return bool
         */
        public function syncToStorage() : bool;
        /**
         * Load values from storage driver.
         *
         * @return bool
         */
        public function syncFromStorage() : bool;
    }
    final class Value
    {
        /**
         * @var bool
         */
        private $hit;
        /**
         * @var mixed|null
         */
        private $value;
        /**
         * @param null $value
         * @param bool $hit
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function __construct($value = null, bool $hit = false)
        {
        }
        /**
         * @return bool
         */
        public function isHit() : bool
        {
        }
        /**
         * @return mixed|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function value()
        {
        }
    }
    /**
     * A complete multi-driver cache item.
     */
    final class WpCacheItem implements \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
    {
        const DIRTY = 'dirty';
        const DIRTY_SHALLOW = 'shallow';
        const DELETED = 'deleted';
        const CLEAN = '';
        /**
         * @var CacheDriver
         */
        private $driver;
        /**
         * @var string
         */
        private $key;
        /**
         * @var string
         */
        private $group;
        /**
         * @var mixed
         */
        private $value;
        /**
         * @var bool
         */
        private $isHit = false;
        /**
         * @var string
         */
        private $dirtyStatus = self::CLEAN;
        /**
         * @var bool|null
         */
        private $isExpired;
        /**
         * @var int
         */
        private $timeToLive;
        /**
         * @var \DateTimeImmutable|null
         */
        private $lastSave;
        /**
         * @var bool
         */
        private $shallowUpdate = false;
        /**
         * @param CacheDriver $driver
         * @param string $key
         * @param string $group
         * @param int|null $timeToLive
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver, string $key, string $group = '', int $timeToLive = null)
        {
        }
        /**
         * Before the object vanishes its storage its updated if needs to.
         */
        public function __destruct()
        {
        }
        /**
         * @inheritdoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function fillWith($value) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function isHit() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function isExpired() : bool
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function value()
        {
        }
        /**
         * @inheritdoc
         */
        public function delete() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function liveFor(int $ttl) : \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
        {
        }
        /**
         * @inheritdoc
         */
        public function syncToStorage() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function syncFromStorage() : bool
        {
        }
        /**
         * Initialize (or update) the internal status of the item.
         */
        private function calculateStatus()
        {
        }
        /**
         * @return bool
         */
        private function update() : bool
        {
        }
        /**
         * @return \DateTimeImmutable
         */
        private function now() : \DateTimeInterface
        {
        }
        /**
         * Compact to and explode from storage a value.
         *
         * @param array|null $compactValue
         * @return array
         */
        private function prepareValue(array $compactValue = null) : array
        {
        }
        /**
         * @param \DateTimeInterface|null $date
         * @return string
         */
        private function serializeDate(\DateTimeInterface $date = null) : string
        {
        }
        /**
         * @param string $date
         * @return \DateTimeImmutable|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        private function unserializeDate(string $date)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Pool {
    /**
     * Interface for all cache pool implementations.
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    interface CachePool
    {
        /**
         * Return pool namespace.
         *
         * @return string
         */
        public function namespace() : string;
        /**
         * Check if the cache pool is for network.
         *
         * @return bool
         */
        public function isNetwork() : bool;
        /**
         * Fetches a value from the cache.
         *
         * @param string $key
         * @return CacheItem
         */
        public function item(string $key) : \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
        /**
         * Fetches a value from the cache.
         *
         * The difference between `$pool->get($key)` and `$pool->item($key)->value()` is that the latter
         * could return a cached value even if expired, and it is responsibility of the caller check for
         * that if necessary. Moreover, `get()` also has a default param that is returned in case cache
         * is a miss or is expired.
         *
         * @param string $key
         * @param mixed|null $default
         * @return mixed
         */
        public function valueOfKey(string $key, $default = null);
        /**
         * Fetches a value from the cache.
         *
         * The "bulk" version of `get()`.
         *
         * @param string[] $keys
         * @param mixed|null $default
         * @return array
         */
        public function valuesOfKeys(array $keys, $default = null) : array;
        /**
         * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
         *
         * @param string $key
         * @param mixed $value
         * @param null|int $ttl
         * @return CacheItem
         */
        public function cache(string $key, $value = null, int $ttl = null) : \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
        /**
         * Delete an item from the cache by its unique key.
         *
         * @param string $key
         * @return bool
         */
        public function delete(string $key) : bool;
        /**
         * Determines whether an item is present in the cache.
         *
         * A true outcome does not provide warranty the value is not expired.
         *
         * @param string $key
         * @return bool
         */
        public function has(string $key) : bool;
    }
    final class WpCachePool implements \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
    {
        /**
         * @var string
         */
        private $namespace;
        /**
         * @var CacheDriver
         */
        private $driver;
        /**
         * @var CacheItem[]
         */
        private $items = [];
        /**
         * @param string $namespace
         * @param CacheDriver $driver
         */
        public function __construct(string $namespace, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver)
        {
        }
        /**
         * @inheritdoc
         */
        public function namespace() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function item(string $key) : \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function valueOfKey(string $key, $default = null)
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function valuesOfKeys(array $keys, $default = null) : array
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function cache(string $key, $value = null, int $ttl = null) : \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $key) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function has(string $key) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Server {
    class Facade
    {
        /**
         * @var Server
         */
        private $server;
        /**
         * @var string
         */
        private $namespace;
        /**
         * @var bool
         */
        private $claiming = false;
        /**
         * @param Server $server
         * @param string $namespace
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Cache\Server\Server $server, string $namespace)
        {
        }
        /**
         * Wrapper for server get.
         *
         * @param string $key
         * @param mixed $args
         * @return mixed
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         *
         * @see Server::claim()
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function claim(string $key, ...$args)
        {
        }
        /**
         * Wrapper for server flush.
         *
         * @param string $key
         * @param array|null $args
         * @return bool
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheDriver
         *
         * @see Server::flush()
         */
        public function flush(string $key, array $args = null) : bool
        {
        }
    }
    class ItemLogic
    {
        /**
         * @var string
         */
        private $namespace;
        /**
         * @var string
         */
        private $key;
        /**
         * @var callable|null
         */
        private $updater;
        /**
         * @var int
         */
        private $timeToLive = 0;
        /**
         * @var int
         */
        private $extensionOnFailure = 0;
        /**
         * @var callable|null
         */
        private $keyGenerator;
        /**
         * @param string $namespace
         * @param string $key
         */
        public function __construct(string $namespace, string $key)
        {
        }
        /**
         * @return string
         */
        public function namespace() : string
        {
        }
        /**
         * @return string
         */
        public function key() : string
        {
        }
        /**
         * @return callable
         */
        public function updater() : callable
        {
        }
        /**
         * @param array|null $args
         * @return callable
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function generateItemKey(...$args) : string
        {
        }
        /**
         * @return int
         */
        public function timeToLive() : int
        {
        }
        /**
         * @return int
         */
        public function extensionOnFailure() : int
        {
        }
        /**
         * Set the callback that will be used to both generate and update the cache item value.
         *
         * The callback should throw an exception when the generation of the value fails.
         *
         * @param callable $callback
         * @return ItemLogic
         */
        public function updateWith(callable $callback) : \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
        /**
         * Set the callback that will be used to generate an unique key for updater arguments.
         *
         * The callback will receive the "base" key as 1st argument, plus variadically all the updater
         * arguments, and has to return a string that's unique per key (likely should start with it)
         * and per arguments.
         *
         * Please note: the second argument passed to callback could be null.
         *
         * @see ItemLogic::generateItemKey()
         *
         * @param callable $callback
         * @return ItemLogic
         */
        public function generateKeyWith(callable $callback) : \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
        /**
         * Set the time to live for the cached value.
         *
         * @param int $timeToLive
         * @return ItemLogic
         */
        public function liveFor(int $timeToLive) : \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
        /**
         * @param int $extension
         * @return ItemLogic
         */
        public function onFailureExtendFor(int $extension) : \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
    }
    class Server
    {
        const UPDATING_KEYS_TRANSIENT = 'mlp_cache_server_updating_keys';
        const SPAWNING_KEYS_TRANSIENT = 'mlp_cache_server_spawning_keys_';
        const HEADER_KEY = 'Mlp-Cache-Update-Key';
        const HEADER_TTL = 'Mlp-Cache-Update-TTL';
        const VALID_ARG_TYPES = ['boolean', 'integer', 'double', 'string'];
        /**
         * @var string[]
         */
        private static $networkKeys = [];
        /**
         * @var string[]
         */
        private static $siteKeys = [];
        /**
         * @var CacheFactory
         */
        private $factory;
        /**
         * @var CacheDriver
         */
        private $driver;
        /**
         * @var CacheDriver
         */
        private $networkDriver;
        /**
         * @var array[]
         */
        private $registered = [];
        /**
         * @var string[]
         */
        private $spawnQueue = [];
        /**
         * @var bool
         */
        private $inSpawnQueue = false;
        /**
         * @var ServerRequest
         */
        private $request;
        /**
         * @param CacheFactory $factory
         * @param CacheDriver $driver
         * @param CacheDriver $networkDriver
         * @param ServerRequest $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Cache\CacheFactory $factory, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $networkDriver, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * On regular requests it is possible to register a callback to generates same
         * value to cache and associate it with an unique key in the also given pool.
         * This should be called early, because the values can only be then "claimed"
         * (which is retrieved for actual use) after registration.
         *
         * The value generated will be valid for the given TTL or for the default (1 hour).
         * When value is expired it will be returned anyway, but an update will be scheduled.
         * The scheduled updates happens in separate HEAD requests.
         *
         * It means that once cached for first time a value will be served always from
         * cache (unless manually flushed) and updated automatically on expiration
         * without affecting user request time.
         *
         * @param ItemLogic $itemLogic
         * @return Server
         * @throws Exception\BadCacheItemRegistration
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic $itemLogic) : self
        {
        }
        /**
         * @param ItemLogic $itemLogic
         * @return Server
         * @throws Exception\BadCacheItemRegistration
         */
        public function registerForNetwork(\Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic $itemLogic) : self
        {
        }
        /**
         * Check whether the given pair of namespace and key is registered.
         *
         * @param string $namespace
         * @param string $logicKey
         * @return bool
         */
        public function isRegistered(string $namespace, string $logicKey) : bool
        {
        }
        /**
         * @param string $namespace
         * @param string $logicKey
         * @return CachePool
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheDriver
         */
        public function poolForLogic(string $namespace, string $logicKey) : \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * On regular requests returns the cached (or just newly generated) value for
         * a registered couple of namespace and key.
         * In case the value is expired, it will be returned anyway, but an updating
         * will be scheduled and will happen in a separate HEAD request and the
         * expired cached value will continue to be served until the value is
         * successfully updated.
         *
         * @param string $namespace
         * @param string $key
         * @param array|null $args
         * @return mixed
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function claim(string $namespace, string $key, array $args = null)
        {
        }
        /**
         * Once cached for first time values continue to be served from cache
         * (automatically updated on expiration) unless this method is called to
         * force flush of a specific namespace / key pair or of a whole namespace.
         *
         * @param string $namespace
         * @param string $key
         * @param array|null $args
         * @return bool
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheDriver
         */
        public function flush(string $namespace, string $key, array $args = null) : bool
        {
        }
        /**
         * When an expired value is requested, it is returned to claiming code, and
         * an HTTP HEAD request is sent to home page containing headers with
         * information about key and the TTL.
         * This methods check them and if the request fits criteria update the value
         * using the registered callable.
         */
        public function listenSpawn()
        {
        }
        /**
         * @param string $namespace
         * @param string $key
         * @param array|null $args
         * @return bool
         */
        public function isQueuedForUpdate(string $namespace, string $key, array $args = null) : bool
        {
        }
        /**
         * @param string $namespace
         * @param string $key
         * @return string
         */
        private function registeredKey(string $namespace, string $key) : string
        {
        }
        /**
         * Adds the actions that will cause item flushing.
         *
         * @param ItemLogic $logic
         * @param bool $forNetwork
         * @return Server
         * @throws Exception\BadCacheItemRegistration If called during shutdown on in a update request.
         */
        private function doRegister(\Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic $logic, bool $forNetwork) : self
        {
        }
        /**
         * Check the HTTP request to see if it is a cache update request.
         * If so return the key and the data plus a flag set to true.
         *
         * @return array
         */
        private function updatingRequestData() : array
        {
        }
        /**
         * Use transients to mark the given key as currently being updated in a
         * update request, to prevent multiple concurrent updates.
         *
         * @param string $key
         * @param bool $isNetwork
         * @return bool
         */
        private function markUpdating(string $key, bool $isNetwork) : bool
        {
        }
        /**
         * Remove the given key from transient storage to mark given key again
         * available for updates.
         *
         * @param string $key
         * @param bool $isNetwork
         * @return bool
         */
        private function markNotUpdating(string $key, bool $isNetwork) : bool
        {
        }
        /**
         * Use transients to check if the given key is currently being updated in a
         * update request, to prevent multiple concurrent updates.
         *
         * @param string $key
         * @param bool $isNetwork
         * @return bool
         */
        private function isUpdating(string $key, bool $isNetwork) : bool
        {
        }
        /**
         * Queue given key to be updated in a HTTP request.
         * The first time it is called adds an action on shutdown that will actually
         * process the queue and send updating HTTP requests.
         *
         * @param string $registeredKey
         * @param string $fullKey
         * @param int $timeToLive
         * @param bool $isNetwork
         * @param array $args
         */
        private function queueUpdate(string $registeredKey, string $fullKey, int $timeToLive, bool $isNetwork, array $args = null)
        {
        }
        /**
         * Send multiple HTTP request to refresh registered cache items.
         */
        private function spawnQueue() : array
        {
        }
        /**
         * Use transients to mark the given key as currently being sent via an update
         * request, to prevent multiple concurrent request.
         * Transients will not be deleted manually, but are set with a very short
         * expiration so they will expire and vanish in few seconds when (hopefully)
         * all the parallel-executing updating requests finished.
         *
         * @param bool $isNetwork
         * @param string[] $fullKeys
         */
        private function markSpawning(bool $isNetwork, string ...$fullKeys)
        {
        }
        /**
         * @param string $fullKey
         * @param bool $isNetwork
         * @return bool
         */
        private function isSpawning(string $fullKey, bool $isNetwork) : bool
        {
        }
        /**
         * @param callable $updater
         * @param array $args
         * @param mixed $currentValue
         * @return array
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        private function fetchUpdatedValue(callable $updater, array $args = null, $currentValue = null) : array
        {
        }
        /**
         * @param string $namespace
         * @param string $key
         * @throws Exception\NotRegisteredCacheItem When required item is not registered.
         */
        private function bailIfNotRegistered(string $namespace, string $key)
        {
        }
        /**
         * @param array|null $args
         * @param string $namespace
         * @param string $key
         * @throws Exception\InvalidCacheArgument When not accepted types are included in cache args
         */
        private function bailIfBadArgs(array $args = null, string $namespace, string $key)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Database\Exception {
    /**
     * Exception to be thrown when an action is to be performed on an invalid table.
     */
    class InvalidTable extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $action
         * @return InvalidTable
         */
        public static function forAction(string $action = 'install') : \Inpsyde\MultilingualPress\Framework\Database\Exception\InvalidTable
        {
        }
    }
    /**
     * Exception to be thrown when an action is to be performed on an table that doesn't exists.
     */
    class NonexistentTable extends \Exception
    {
        /**
         * NonexistentTable constructor.
         * @param string $action
         * @param string $table
         */
        public function __construct(string $action, string $table)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Database {
    /**
     * Interface for all tables.
     */
    interface Table
    {
        /**
         * Returns an array with all columns that do not have any default content.
         *
         * @return string[]
         */
        public function columnsWithoutDefaultContent() : array;
        /**
         * Returns the SQL string for the default content.
         *
         * @return string
         */
        public function defaultContentSql() : string;
        /**
         * Returns the SQL string for all (unique) keys.
         *
         * @return string
         */
        public function keysSql() : string;
        /**
         * Returns the table name.
         *
         * @return string
         */
        public function name() : string;
        /**
         * Check if table exists or not
         *
         * @return bool
         */
        public function exists() : bool;
        /**
         * Returns the primary key.
         *
         * @return string
         */
        public function primaryKey() : string;
        /**
         * Returns the table schema as an array with column names as keys and SQL definitions as values.
         *
         * @return string[]
         */
        public function schema() : array;
    }
    /**
     * Table duplicator implementation using the WordPress database object.
     */
    class TableDuplicator
    {
        /**
         * @var \wpdb
         */
        private $db;
        /**
         * @param \wpdb $db
         */
        public function __construct(\wpdb $db)
        {
        }
        /**
         * Creates a new table that is an exact duplicate of an existing table.
         *
         * @param string $existingTableName
         * @param string $newTableName
         * @return bool
         */
        public function duplicate(string $existingTableName, string $newTableName) : bool
        {
        }
    }
    /**
     * Table installer implementation using the WordPress database object.
     */
    class TableInstaller
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var string
         */
        private $options;
        /**
         * @var Table
         */
        private $table;
        /**
         * @param \wpdb $wpdb
         * @param Table|null $table
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table = null)
        {
        }
        /**
         * Installs the given table.
         *
         * @param Table|null $table
         * @return bool
         * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
         */
        public function install(\Inpsyde\MultilingualPress\Framework\Database\Table $table = null) : bool
        {
        }
        /**
         * Uninstalls the given table.
         *
         * @param Table|null $table
         * @return bool
         * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
         */
        public function uninstall(\Inpsyde\MultilingualPress\Framework\Database\Table $table = null) : bool
        {
        }
        /**
         * Returns the according SQL string for the columns in the given table schema.
         *
         * @param array $schema
         * @return string
         */
        private function allColumns(array $schema) : string
        {
        }
        /**
         * Returns the according SQL string for the keys of the given table.
         *
         * @param Table $table
         * @return string
         */
        private function allKeys(\Inpsyde\MultilingualPress\Framework\Database\Table $table) : string
        {
        }
        /**
         * Returns the SQL string for the table options.
         *
         * @return string
         */
        private function tableOptions() : string
        {
        }
        /**
         * Checks if a table with the given name exists in the database.
         *
         * @param string $tableName
         * @return bool
         */
        private function tableExists(string $tableName) : bool
        {
        }
        /**
         * Inserts the according default content into the given table.
         *
         * @param Table $table
         */
        private function insertDefaultContent(\Inpsyde\MultilingualPress\Framework\Database\Table $table)
        {
        }
    }
    /**
     * Table list implementation using the WordPress database object.
     */
    class TableList
    {
        const ALL_TABLES_CACHE_KEY = 'allTables';
        /**
         * @var \wpdb
         */
        private $db;
        /**
         * @var Facade
         */
        private $cache;
        /**
         * @var CacheSettingsRepository
         */
        private $cacheSettingsRepository;
        /**
         * @param \wpdb $db
         * @param Facade $cache
         * @param CacheSettingsRepository $cacheSettingsRepository
         */
        public function __construct(\wpdb $db, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository)
        {
        }
        /**
         * Returns an array with the names of all tables for the site with the given ID.
         * By default will return main site tables.
         *
         * @param int|null $siteId
         * @return array of all table names for given site
         * @throws Throwable
         */
        public function allTablesForSite(int $siteId = null) : array
        {
        }
        /**
         * Returns an array with the names of all tables.
         *
         * @return array of all table names for given site
         * @throws Throwable
         */
        public function allTables() : array
        {
        }
        /**
         * Returns an array with the names of all network tables.
         *
         * @return array The array of network table names
         * @throws Throwable
         */
        public function networkTables() : array
        {
        }
        /**
         * Returns an array with the names of all tables for the site with the given ID.
         *
         * @param int $siteId
         * @return array The array of site table names
         * @throws Throwable
         */
        public function siteTables(int $siteId) : array
        {
        }
        /**
         * @return array
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         */
        private function allTablesCache() : array
        {
        }
    }
    /**
     * Table replacer implementations using the WordPress database object.
     */
    class TableReplacer
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @param \wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Replaces the content of one table with another table's content.
         *
         * @param string $destination
         * @param string $source
         * @return bool
         */
        public function replace(string $destination, string $source) : bool
        {
        }
    }
    /**
     * Table string replacer implementation using the WordPress database object.
     */
    class TableStringReplacer
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @param \wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Replaces one string with another all given columns of the given table at once.
         *
         * @param string $table
         * @param string[] $columns
         * @param string $search
         * @param string $replacement
         * @return int
         */
        public function replace(string $table, array $columns, string $search, string $replacement) : int
        {
        }
        /**
         * Returns SQL string for replacing the given string with given replacement in given columns.
         *
         * @param string[] $columns
         * @param string $search
         * @param string $replacement
         * @return string
         */
        private function replacementsSql(array $columns, string $search, string $replacement) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Class Entity
     * @package Inpsyde\MultilingualPress\Framework
     */
    class Entity
    {
        /**
         * @var WP_Post|WP_Term|WP_Comment|Entity|null
         */
        private $entity;
        /**
         * @var int
         */
        private $id = 0;
        /**
         * @param WP_Post|WP_Term|WP_Comment|Entity $object
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function __construct($object)
        {
        }
        /**
         * @param string $var
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function __get(string $var)
        {
        }
        /**
         * @return int
         */
        public function id() : int
        {
        }
        /**
         * @return bool
         */
        public function isValid() : bool
        {
        }
        /**
         * @param string $type
         *
         * @return bool
         */
        public function is(string $type) : bool
        {
        }
        /**
         * Retrieve the class name of the entity
         *
         * @return string
         */
        public function type() : string
        {
        }
        /**
         * @param string $prop
         * @param null $default
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function prop(string $prop, $default = null)
        {
        }
        /**
         * @return WP_Post|WP_Term|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function expose()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Factory {
    /**
     * Class to be used for class resolution in factories.
     */
    class ClassResolver
    {
        /**
         * @var string
         */
        private $base;
        /**
         * @var bool
         */
        private $baseIsClass;
        /**
         * @var string
         */
        private $defaultClass;
        /**
         * @param string $base
         * @param string|null $defaultClass
         * @throws \InvalidArgumentException If the given base is not a valid fully qualified class
         */
        public function __construct(string $base, string $defaultClass = null)
        {
        }
        /**
         * Resolves the class to be used for instantiation, which might be either the given class or
         * the default class.
         *
         * @param string|null $class
         * @return string
         * @throws \InvalidArgumentException If no class is given and no default class is available.
         */
        public function resolve(string $class = null) : string
        {
        }
        /**
         * Checks if the class with the given name is valid with respect to the defined base.
         *
         * @param string $class
         * @return string
         * @throws Exception\InvalidClass If the given class is invalid with respect to the defined base.
         */
        private function checkClass(string $class) : string
        {
        }
    }
    /**
     * Factory for WordPress error objects.
     */
    class ErrorFactory
    {
        /**
         * @var ClassResolver
         */
        private $classResolver;
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new WordPress error object, instantiated with the given arguments.
         *
         * @param array $args
         * @param string $class
         * @return \WP_Error
         */
        public function create(array $args = [], string $class = '') : \WP_Error
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Factory\Exception {
    /**
     * Exception to be thrown when trying to create an instance of an invalid class.
     */
    class InvalidClass extends \Exception
    {
    }
}
namespace Inpsyde\MultilingualPress\Framework\Factory {
    class LanguageFactory
    {
        /**
         * @var ClassResolver
         */
        private $classResolver;
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new language object of the given (or default) class,
         * instantiated with the given arguments.
         *
         * @param array $args
         * @param string|null $class
         * @return Language
         */
        public function create(array $args = [], string $class = null) : \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
    }
    /**
     * Factory for nonce objects.
     */
    class NonceFactory
    {
        /**
         * @var ClassResolver
         */
        private $classResolver;
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new nonce object, instantiated with the given arguments.
         *
         * @param array $args
         * @param string $class
         * @return Nonce
         */
        public function create(array $args = [], string $class = '') : \Inpsyde\MultilingualPress\Framework\Nonce\Nonce
        {
        }
    }
    class UrlFactory
    {
        /**
         * @var ClassResolver
         */
        private $classResolver;
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new url object of the given (or default) class,
         * instantiated with the given arguments.
         *
         * @param array $args
         * @param string|null $class
         * @return Url
         */
        public function create(array $args = [], string $class = null) : \Inpsyde\MultilingualPress\Framework\Url\Url
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Class Filesystem
     * @package Inpsyde\MultilingualPress\Framework
     */
    class Filesystem
    {
        const ACTION_INIT_CREDENTIALS = 'multilingualpress.init_filesystem_credentials';
        const FILTER_CREDENTIALS_CONTEXT = 'multilingualpress.filesystem_context';
        /**
         * @var \WP_Filesystem_Base
         */
        private $wpFilesystem;
        /**
         * @return string
         */
        public static function forceDirect() : string
        {
        }
        /**
         * @return bool
         */
        public static function forceCredentials() : bool
        {
        }
        /**
         * @return void
         */
        public static function removeForceFilters()
        {
        }
        /*
         * We are not really interested in methods that requires credentials like FTP and such,
         * so we don't support them out-of-the-box, but following filters are trivial to remove.
         *
         * @return void
         */
        private static function addForceFilters()
        {
        }
        /**
         * @param string $source
         * @param string $destination
         * @param int $mode
         *
         * @return bool
         */
        public function copy(string $source, string $destination, int $mode = null) : bool
        {
        }
        /**
         * @param string $source
         * @param string $destination
         * @param int $mode
         *
         * @return bool
         */
        public function copyIfNotExist(string $source, string $destination, int $mode = null) : bool
        {
        }
        /**
         * @param string $source
         * @param string $destination
         *
         * @return bool
         */
        public function move(string $source, string $destination) : bool
        {
        }
        /**
         * @param string $source
         * @param string $destination
         *
         * @return bool
         */
        public function moveIfNotExist(string $source, string $destination) : bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function deleteFile(string $filepath) : bool
        {
        }
        /**
         * @param string $path
         *
         * @return bool
         */
        public function deleteFolder(string $path) : bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function pathExists(string $filepath) : bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function isFile(string $filepath) : bool
        {
        }
        /**
         * @param string $path
         *
         * @return bool
         */
        public function isDir(string $path) : bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function isReadable(string $filepath) : bool
        {
        }
        /**
         * @param string $path
         * @param int $mode
         *
         * @return bool
         */
        public function mkDirP(string $path, int $mode = null) : bool
        {
        }
        /**
         * @param string $path
         * @param int $mode
         *
         * @return bool
         */
        public function mkDir(string $path, int $mode = null) : bool
        {
        }
        /**
         * Return an instance of WP_Filesystem.
         *
         * @return \WP_Filesystem_Base
         */
        private function wpFilesystem() : \WP_Filesystem_Base
        {
        }
        /**
         * @return int
         */
        private function folderChmod() : int
        {
        }
        /**
         * @return int
         */
        private function fileChmod() : int
        {
        }
        /**
         * Check a thing against \WP_Filesystem_Base
         *
         * @param mixed $wpFilesystem
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        private function isFilesystemBase($wpFilesystem) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Filter {
    /**
     * Interface for all filter implementations.
     */
    interface Filter
    {
        const DEFAULT_ACCEPTED_ARGS = 1;
        const DEFAULT_PRIORITY = 10;
        /**
         * Returns the number of accepted arguments.
         *
         * @return int
         */
        public function acceptedArgs() : int;
        /**
         * Removes the filter.
         *
         * @return bool
         */
        public function disable() : bool;
        /**
         * Adds the filter.
         *
         * @return bool
         */
        public function enable() : bool;
        /**
         * Returns the hook name.
         *
         * @return string
         */
        public function hook() : string;
        /**
         * Returns the callback priority.
         *
         * @return int
         */
        public function priority() : int;
    }
    /**
     * Trait for basic filter implementations.
     *
     * @see Filter
     */
    trait FilterTrait
    {
        /**
         * @var int
         */
        private $acceptedArgs;
        /**
         * @var callable
         */
        private $callback;
        /**
         * @var string
         */
        private $hook;
        /**
         * @var int
         */
        private $priority;
        /**
         * @return bool
         *
         * @see Filter::enable()
         */
        public function enable() : bool
        {
        }
        /**
         * @return bool
         *
         * @see Filter::disable()
         */
        public function disable() : bool
        {
        }
        /**
         * @return string
         *
         * @see Filter::hook()
         */
        public function hook() : string
        {
        }
        /**
         * @return int
         *
         * @see Filter::priority()
         */
        public function priority() : int
        {
        }
        /**
         * @return int
         *
         * @see Filter::acceptedArgs()
         */
        public function acceptedArgs() : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Http {
    /**
     * Interface for all HTTP request abstraction implementations.
     */
    interface Request
    {
        const CONNECT = 'CONNECT';
        const DELETE = 'DELETE';
        const GET = 'GET';
        const HEAD = 'HEAD';
        const OPTIONS = 'OPTIONS';
        const PATCH = 'PATCH';
        const POST = 'POST';
        const PUT = 'PUT';
        const TRACE = 'TRACE';
        const INPUT_GET = INPUT_GET;
        const INPUT_POST = INPUT_POST;
        const INPUT_REQUEST = 99;
        const INPUT_COOKIE = INPUT_COOKIE;
        const INPUT_SERVER = INPUT_SERVER;
        const INPUT_ENV = INPUT_ENV;
        const METHODS = [self::CONNECT, self::DELETE, self::GET, self::HEAD, self::OPTIONS, self::PATCH, self::POST, self::PUT, self::TRACE];
        /**
         * Returns the URL for current request.
         *
         * @return Url
         */
        public function url() : \Inpsyde\MultilingualPress\Framework\Url\Url;
        /**
         * Returns the body of the request as string.
         *
         * @return string
         */
        public function body() : string;
        /**
         * Return a value from request body, optionally filtered.
         *
         * @param string $name
         * @param int $source The input source of the value. One of the `INPUT_*` constants.
         * @param int $filter
         * @param int $options
         * @return mixed
         */
        public function bodyValue(string $name, int $source = self::INPUT_REQUEST, int $filter = FILTER_UNSAFE_RAW, int $options = FILTER_FLAG_NONE);
        /**
         * Returns header value as set in the request.
         *
         * @param string $name
         * @return string
         */
        public function header(string $name) : string;
        /**
         * Returns method (GET, POST..) value as set in the request.
         *
         * @return string
         */
        public function method() : string;
    }
    /**
     * Interface for all HTTP server request abstraction implementations.
     */
    interface ServerRequest extends \Inpsyde\MultilingualPress\Framework\Http\Request
    {
        /**
         * Returns a server value.
         *
         * @param string $name
         * @return string
         */
        public function serverValue(string $name) : string;
    }
    final class PhpServerRequest implements \Inpsyde\MultilingualPress\Framework\Http\ServerRequest
    {
        /**
         * @var array|null
         */
        private static $values;
        /**
         * @var array|null
         */
        private static $headers;
        /**
         * @var array|null
         */
        private static $server;
        /**
         * @var Url|null
         */
        private static $url;
        /**
         * @var string|null
         */
        private static $body;
        const INPUT_SOURCES = [INPUT_POST => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_POST, INPUT_GET => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_GET, INPUT_REQUEST => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_REQUEST, INPUT_COOKIE => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_COOKIE, INPUT_SERVER => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_SERVER, INPUT_ENV => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_ENV];
        /**
         * Returns the URL for current request.
         *
         * @return Url
         */
        public function url() : \Inpsyde\MultilingualPress\Framework\Url\Url
        {
        }
        /**
         * @inheritdoc
         */
        public function body() : string
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function bodyValue(string $name, int $method = \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_REQUEST, int $filter = FILTER_UNSAFE_RAW, int $options = FILTER_FLAG_NONE)
        {
        }
        /**
         * @inheritdoc
         */
        public function header(string $name) : string
        {
        }
        /**
         * @inheritdoc
         */
        public function serverValue(string $name) : string
        {
        }
        /**
         * @inheritdoc
         */
        public function method() : string
        {
        }
        /**
         * Ensure request body is available in class property.
         */
        private function ensureBody()
        {
        }
        /**
         * Ensure server values from request are available in class property.
         */
        private function ensureServer()
        {
        }
        /**
         * Ensure headers from request are available in class property.
         */
        private function ensureHeaders()
        {
        }
        /**
         * Ensure values from request are available in class property.
         */
        private function ensureValues()
        {
        }
        /**
         * Ensure URL marshaled from request is available in class property.
         */
        private function ensureUrl()
        {
        }
        /**
         * Returns the given filter options, potentially adapted to work with array data.
         *
         * @param mixed $value
         * @param mixed $options
         * @return int|array
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        private function adaptFilterOptions($value, $options)
        {
        }
        /**
         * Right before "sanitize_comment_cookies" action, WordPress calls `wp_magic_quotes` that adds
         * slashes to $_GET, $_POST, $_COOKIE, and $_SERVER.
         * If that happened, we remove slashes to have access to "raw" value, and leave the burden of
         * slashing, if necessary, to client code.
         *
         * @param array $values
         * @return array
         *
         * @see wp_magic_quotes()
         */
        private function maybeUnslash(array $values) : array
        {
        }
        /**
         * Normalizes an input source to a known value.
         *
         * Will attempt to convert the source to a standardized known value,
         * if it is registered in the map.
         *
         * @param int|string $source The input source to normalize.
         * @return int The normalized input source.
         * @throws RangeException If cannot normalize.
         */
        protected function normalizeInputSource($source) : int
        {
        }
    }
    /**
     * phpcs:disable WordPress.VIP.SuperGlobalInputUsage
     * phpcs:disable WordPress.CSRF
     */
    class RequestGlobalsManipulator
    {
        const METHOD_GET = 'GET';
        const METHOD_POST = 'POST';
        /**
         * @var string
         */
        private $requestMethod;
        /**
         * @var array
         */
        private $storage = [];
        /**
         * @param string $requestMethod
         */
        public function __construct(string $requestMethod = self::METHOD_POST)
        {
        }
        /**
         * Removes all data from the request globals.
         *
         * @return int
         */
        public function clear() : int
        {
        }
        /**
         * Restores all data from the storage.
         *
         * @return int
         */
        public function restore() : int
        {
        }
    }
    /**
     * Something able to handle the server request.
     */
    interface RequestHandler
    {
        /**
         * Handles the given server request.
         *
         * @param ServerRequest $request
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request);
    }
    /**
     * URL implementation that is build starting from server data as array.
     */
    final class ServerUrl implements \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * @var array
         */
        private $serverData;
        /**
         * @var string
         */
        private $url;
        /**
         * @var string
         */
        private $host;
        /**
         * @param array $serverData
         * @param string $host
         */
        public function __construct(array $serverData, string $host = '')
        {
        }
        /**
         * Returns the URL string.
         *
         * @return string
         */
        public function __toString() : string
        {
        }
        /**
         * Extract URL from server data and stores in object properties if not set yet.
         */
        private function ensureUrl()
        {
        }
        /**
         * @return array
         */
        private function hostAndPort() : array
        {
        }
        /**
         * @return array
         */
        private function pathFragmentAndQuery() : array
        {
        }
        /**
         * @return string
         */
        private function path() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Integration {
    /**
     * Interface for all integration controllers.
     */
    interface Integration
    {
        /**
         * Integrates some (possibly external) service with MultilingualPress.
         */
        public function integrate() : void;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Language {
    /**
     * Trait Bcp47tagValidator
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    trait Bcp47tagValidator
    {
        /**
         * Pattern to test against
         *
         * @var string
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         */
        private $pattern = '/^(?<grandfathered>(?:en-GB-oed|i-(?:ami|bnn|default|enochian|hak|klingon|lux|mingo|navajo|pwn|t(?:a[oy]|su))|sgn-(?:BE-(?:FR|NL)|CH-DE))|(?:art-lojban|cel-gaulish|no-(?:bok|nyn)|zh-(?:guoyu|hakka|min(?:-nan)?|xiang)))|(?:(?<language>(?:[A-Za-z]{2,3}(?:-(?<extlang>[A-Za-z]{3}(?:-[A-Za-z]{3}){0,2}))?)|[A-Za-z]{4}|[A-Za-z]{5,8})(?:-(?<script>[A-Za-z]{4}))?(?:-(?<region>[A-Za-z]{2}|[0-9]{3}))?(?:-(?<variant>[A-Za-z0-9]{5,8}|[0-9][A-Za-z0-9]{3}))*(?:-(?<extension>[0-9A-WY-Za-wy-z](?:-[A-Za-z0-9]{2,8})+))*)(?:-(?<privateUse>x(?:-[A-Za-z0-9]{1,8})+))?$/Di';
        // phpcs:enable
        /**
         * Validate bcp47Tag
         *
         * @param string $bcp47Tag
         * @return bool
         */
        protected function validate(string $bcp47Tag) : bool
        {
        }
    }
    /**
     * Class Bcp47Tag
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class Bcp47Tag implements \Inpsyde\MultilingualPress\Framework\Stringable
    {
        use \Inpsyde\MultilingualPress\Framework\Language\Bcp47tagValidator;
        /**
         * @var string
         */
        private $value;
        /**
         * Bcp47Tag constructor.
         * @param string $bcp47Tag
         * @throws InvalidArgumentException
         */
        public function __construct(string $bcp47Tag)
        {
        }
        /**
         * @return string
         */
        public function __toString() : string
        {
        }
    }
    /**
     * Interface for all language data type implementations.
     */
    interface Language
    {
        const ISO_SHORTEST = 'iso_shortest';
        /**
         * Returns the ID of the language.
         *
         * @return int
         */
        public function id() : int;
        /**
         * Checks if the language is written right-to-left (RTL).
         *
         * @return bool
         */
        public function isRtl() : bool;
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function name() : string;
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function englishName() : string;
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function nativeName() : string;
        /**
         * Returns the language ISO 639 code.
         *
         * @param string $which
         * @return string
         */
        public function isoCode(string $which = self::ISO_SHORTEST) : string;
        /**
         * Returns the language name to be used for frontend purposes.
         *
         * @return string
         */
        public function isoName() : string;
        /**
         * Returns the language BCP-47 tag.
         *
         * @return string
         */
        public function bcp47tag() : string;
        /**
         * Returns the language locale.
         *
         * @return string
         */
        public function locale() : string;
        /**
         * Returns the language type.
         *
         * @return string
         */
        public function type() : string;
    }
    /**
     * Null language implementation.
     */
    final class NullLanguage implements \Inpsyde\MultilingualPress\Framework\Language\Language
    {
        /**
         * @inheritdoc
         */
        public function id() : int
        {
        }
        /**
         * @inheritdoc
         */
        public function isRtl() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function englishName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function nativeName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoCode(string $which = self::ISO_SHORTEST) : string
        {
        }
        /**
         * @inheritdoc
         */
        public function bcp47tag() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function locale() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function type() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Message {
    /**
     * Interface Message
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    interface MessageInterface
    {
        /**
         * Message Content
         *
         * @return string
         */
        public function content() : string;
        /**
         * Response Data
         *
         * @return array
         */
        public function data() : array;
        /**
         * @return string
         */
        public function type() : string;
        /**
         * @param string $type
         * @return bool
         */
        public function isOfType(string $type) : bool;
    }
    /**
     * Class Message
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class Message implements \Inpsyde\MultilingualPress\Framework\Message\MessageInterface
    {
        /**
         * @var string
         */
        private $content;
        /**
         * @var array
         */
        private $data;
        /**
         * @var string
         */
        private $type;
        /**
         * SuccessMessage constructor.
         * @param string $type
         * @param string $content
         * @param array $data
         * @throws \InvalidArgumentException
         */
        public function __construct(string $type, string $content, array $data)
        {
        }
        /**
         * @inheritDoc
         */
        public function type() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function content() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function data() : array
        {
        }
        /**
         * @inheritDoc
         */
        public function isOfType(string $type) : bool
        {
        }
    }
    /**
     * Represents a factory of messages.
     */
    interface MessageFactoryInterface
    {
        /**
         * Create a new instance of a Message.
         *
         * @param string $type The message type. This is determined by module semantics.
         * @param string $content The message content.
         * @param array $data Structured data of the message.
         * All values in this array must be recursively serializable
         * @return MessageInterface The new message.
         *
         * @throws \InvalidArgumentException If message data is invalid.
         * @throws \Exception If message could not be created.
         */
        public function create(string $type, string $content, array $data) : \Inpsyde\MultilingualPress\Framework\Message\MessageInterface;
    }
    /**
     * A factory of Messages.
     *
     * Will create instances of a class that corresponds to the message type,
     * or fall back to a default class if specified, finally throwing.
     */
    class MessageFactory implements \Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface
    {
        /**
         * @var array
         */
        private $typeFactories;
        /**
         * @var string|null
         */
        private $fallbackFactory;
        /**
         * @param array<string, callable> $typeFactories
         * A map of message type codes to their respective factories.
         * Each factory has the following signature:
         * `function (string $code, string $content, array $data)`
         * @param callable|null $fallbackFactory The factory of the class to use if type code doesn't match.
         * Use `null` to indicated that no fallback should be used, and the factory should throw instead.
         */
        public function __construct(array $typeFactories, callable $fallbackFactory = null)
        {
        }
        /**
         * @inheritDoc
         */
        public function create(string $type, string $content, array $data) : \Inpsyde\MultilingualPress\Framework\Message\MessageInterface
        {
        }
        /**
         * Retrieves a factory for a message type.
         *
         * @param string $type The code of a message type.
         *
         * @return callable The factory of a message. See {@see __construct()} parameter `$typeFactories`.
         * @throws OutOfRangeException If specified type code is invalid.
         *
         */
        protected function messageFactory(string $type) : callable
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Module\Exception {
    /**
     * Exception to be thrown when a module that does not exist is to be manipulated.
     */
    class InvalidModule extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $moduleId
         * @param string $action
         * @return InvalidModule
         */
        public static function forId(string $moduleId, string $action = 'read') : self
        {
        }
    }
    /**
     * Exception to be thrown when a module that has already been registered is to be manipulated.
     */
    class ModuleAlreadyRegistered extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $moduleId
         * @param string $action
         * @return ModuleAlreadyRegistered
         */
        public static function forId(string $moduleId, string $action = 'register') : self
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Module {
    /**
     * When pointed to a directory of modules, locates module files in that directory.
     */
    class FileLocator implements \IteratorAggregate
    {
        /**
         * The base directory to look for files in.
         *
         * @var string
         */
        protected $baseDir;
        /**
         * The name of the module file.
         *
         * @var string
         */
        protected $moduleFileName;
        /**
         * The maximal directory depth to scan into.
         *
         * @var int
         */
        protected $maxDepth;
        public function __construct(string $baseDir, string $moduleFileName, int $maxDepth)
        {
        }
        /**
         * {@inheritdoc}
         *
         * @throws Exception If problem retrieving internal iterator.
         * {@see https://youtrack.jetbrains.com/issue/WI-44884}.
         */
        #[\ReturnTypeWillChange]
        public function getIterator()
        {
        }
        /**
         * Retrieves paths of module files.
         *
         * @throws Exception If problem retrieving paths.
         *
         * @return Traversable The list of file name paths.
         */
        protected function getPaths() : \Traversable
        {
        }
        /**
         * Determines whether or not a module file is valid.
         *
         * @param SplFileInfo $fileInfo The file to filter.
         *
         * @return bool True if the file is valid for inclusion; false otherwise.
         */
        protected function filterFile(\SplFileInfo $fileInfo) : bool
        {
        }
        /**
         * Creates a recursive directory iterator.
         *
         * @param string $dir Path to the directory to iterate over.
         *
         * @throws UnexpectedValueException If the directory cannot be accessed.
         *
         * @return RecursiveIteratorIterator The iterator that will recursively
         * iterate over items in the specified directory.
         */
        protected function createRecursiveDirectoryIterator(string $dir) : \RecursiveIteratorIterator
        {
        }
        /**
         * Filters a list of items by applying a callback.
         *
         * @param Traversable $list The list to filter.
         * @param callable $callback The callback criteria to use for filtering.
         *
         * @return Traversable The list of items from the iterator that match the criteria.
         */
        protected function filterList(\Traversable $list, callable $callback) : \Traversable
        {
        }
        /**
         * Finds the deepest iterator that matches.
         *
         * Because the given traversable can be an {@see IteratorAggregate},
         * it will try to get its inner iterator.
         *
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         * @link https://github.com/Dhii/iterator-helper-base/blob/v0.1-alpha2/src/ResolveIteratorCapableTrait.php
         * phpcs:enable
         * Ported from here.
         *
         * @param Traversable $iterator The iterator to resolve.
         * @param int         $limit    The depth limit for resolution.
         *
         * @throws OutOfRangeException      If infinite recursion is detected.
         * @throws UnexpectedValueException If the iterator could not be resolved within
         *                                  the depth limit.
         *
         * @return Iterator The inner-most iterator, or whatever the test function allows.
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        protected function resolveIterator(\Traversable $iterator, int $limit = 100) : \Iterator
        {
        }
    }
    final class Module
    {
        /**
         * @var string
         */
        private $description;
        /**
         * @var string
         */
        private $id;
        /**
         * @var bool
         */
        private $isActive;
        /**
         * @var string
         */
        private $name;
        /**
         * @var bool
         */
        private $disabled;
        /**
         * @param string $id
         * @param array $data
         */
        public function __construct(string $id, array $data = [])
        {
        }
        /**
         * Activates the module.
         *
         * @return Module
         */
        public function activate() : \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Deactivates the module.
         *
         * @return Module
         */
        public function deactivate() : \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Returns the description of the module.
         *
         * @return string
         */
        public function description() : string
        {
        }
        /**
         * Returns the ID of the module.
         *
         * @return string
         */
        public function id() : string
        {
        }
        /**
         * Checks if the module is active.
         *
         * @return bool
         */
        public function isActive() : bool
        {
        }
        /**
         * Module is not able to be activated
         *
         * @return bool
         */
        public function isDisabled() : bool
        {
        }
        /**
         * Returns the name of the module.
         *
         * @return string
         */
        public function name() : string
        {
        }
    }
    /**
     * Locates modules in module files.
     */
    class ModuleLocator implements \IteratorAggregate
    {
        /**
         * The list of module file paths to load.
         *
         * @var Traversable
         */
        protected $moduleFiles;
        /**
         * @param Traversable $moduleFiles The list of module definition file paths.
         */
        public function __construct(\Traversable $moduleFiles)
        {
        }
        /**
         * {@inheritdoc}
         *
         * @return ArrayIterator
         *
         * @throws Throwable If problem locating modules.
         */
        #[\ReturnTypeWillChange]
        public function getIterator()
        {
        }
        /**
         * Retrieves a list of modules.
         *
         * @throws Throwable If problem locating modules.
         *
         * @return ServiceProvider[] A list of modules.
         */
        protected function locate() : array
        {
        }
        /**
         * Retrieves the list of module files.
         *
         * @return Traversable The list of absolute paths to module definition files.
         */
        protected function getModuleFiles() : \Traversable
        {
        }
        /**
         * Creates a module defined by the specified file.
         *
         * @param string $filePath The path to the file which defines the module.
         *
         * @throws UnexpectedValueException If the file defined by the specified path does not exist
         * or is not readable.
         * @throws RangeException If the file does not contain a valid module definition.
         * @throws RuntimeException If problem creating module.
         *
         * @return ServiceProvider The module defined in the file.
         */
        protected function createModule(string $filePath) : \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
        {
        }
    }
    class ModuleManager
    {
        const MODULE_STATE_ACTIVE = 1;
        const MODULE_STATE_ALL = 0;
        const MODULE_STATE_INACTIVE = 2;
        const OPTION = 'multilingualpress_modules';
        /**
         * @var Module[]
         */
        private $modules = [];
        /**
         * @var string
         */
        private $option;
        /**
         * @var bool[]
         */
        private $states;
        /**
         * @param string $option
         */
        public function __construct(string $option)
        {
        }
        /**
         * Activates the module with the given ID.
         *
         * @param string $id
         * @return Module
         * @throws InvalidModule If there is no module with the given ID.
         */
        public function activateById(string $id) : \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Deactivates the module with the given ID.
         *
         * @param string $id
         * @return Module
         * @throws InvalidModule If there is no module with the given ID.
         */
        public function deactivateById(string $id) : \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Checks if any modules have been registered.
         *
         * @return bool
         */
        public function isManagingAnything() : bool
        {
        }
        /**
         * Checks if the module with the given ID has been registered.
         *
         * @param string $id
         * @return bool
         */
        public function isManagingModule(string $id) : bool
        {
        }
        /**
         * Checks if the module with the given ID is active.
         *
         * @param string $id
         * @return bool
         */
        public function isModuleActive(string $id) : bool
        {
        }
        /**
         * Returns the module with the given ID.
         *
         * @param string $id
         * @return Module
         * @throws InvalidModule If there is no module with the given ID.
         */
        public function moduleOfId(string $id) : \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Returns all modules with the given state.
         *
         * @param int $state
         * @return Module[]
         */
        public function modulesByState(int $state = self::MODULE_STATE_ALL) : array
        {
        }
        /**
         * Registers the given module.
         *
         * @param Module $module
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Module\Module $module) : bool
        {
        }
        /**
         * Saves the modules persistently.
         *
         * @return bool
         */
        public function persistModules() : bool
        {
        }
        /**
         * Unregisters the module with the given.
         *
         * @param string $moduleId
         * @return Module[]
         */
        public function unregisterById(string $moduleId) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Storage for the (switched) state of the network.
     */
    class NetworkState
    {
        /**
         * @var int
         */
        private $siteId;
        /**
         * @var int[]
         */
        private $stack;
        /**
         * Returns a new instance for the global site ID and switched stack.
         *
         * @return static
         */
        public static function create() : \Inpsyde\MultilingualPress\Framework\NetworkState
        {
        }
        private function __construct()
        {
        }
        /**
         * Restores the stored site state.
         *
         * @return int
         */
        public function restore() : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Nonce {
    /**
     * Interface for all nonce context implementations.
     */
    interface Context extends \ArrayAccess
    {
    }
    trait ReadOnlyContextTrait
    {
        /**
         * @param $name
         * @param $value
         * @throws ContextValueManipulationNotAllowed
         */
        public function offsetSet($name, $value) : void
        {
        }
        /**
         * @param $name
         * @throws ContextValueManipulationNotAllowed
         */
        public function offsetUnset($name) : void
        {
        }
    }
    /**
     * Array-based nonce context implementation.
     */
    final class ArrayContext implements \Inpsyde\MultilingualPress\Framework\Nonce\Context
    {
        use \Inpsyde\MultilingualPress\Framework\Nonce\ReadOnlyContextTrait;
        /**
         * @var array
         */
        private $data;
        /**
         * @param array $data
         */
        public function __construct(array $data)
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetExists($name) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetGet($offset)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Nonce\Exception {
    /**
     * Exception to be thrown when a nonce context value is to be manipulated.
     */
    class ContextValueManipulationNotAllowed extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $name
         * @param string $action
         * @return ContextValueManipulationNotAllowed
         */
        public static function forName(string $name, string $action = 'set') : \Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueManipulationNotAllowed
        {
        }
    }
    /**
     * Exception to be thrown when a nonce context value that has not yet been set is to be read from
     * the container.
     */
    class ContextValueNotSet extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $name
         * @param string $action
         * @return ContextValueNotSet
         */
        public static function forName(string $name, string $action = 'read') : \Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueNotSet
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Nonce {
    /**
     * Interface for all nonce implementations.
     */
    interface Nonce
    {
        /**
         * Returns the nonce value.
         *
         * @return string
         */
        public function __toString() : string;
        /**
         * Returns the nonce action.
         *
         * @return string
         */
        public function action() : string;
        /**
         * Checks if the nonce is valid with respect to the given context.
         * Implementation can decide what to do in case of no context given.
         *
         * @param Context|null $context
         * @return bool
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Nonce\Context $context = null) : bool;
    }
    /**
     * Nonce context implementation wrapping around the server request.
     */
    final class ServerRequestContext implements \Inpsyde\MultilingualPress\Framework\Nonce\Context
    {
        use \Inpsyde\MultilingualPress\Framework\Nonce\ReadOnlyContextTrait;
        /**
         * @var ServerRequest
         */
        private $request;
        /**
         * @param ServerRequest|null $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request = null)
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetExists($name) : bool
        {
        }
        /**
         * @inheritdoc
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
    }
    interface SiteAwareNonce extends \Inpsyde\MultilingualPress\Framework\Nonce\Nonce
    {
        /**
         * Make nonce instance specific for a given site.
         *
         * @param int $siteId
         * @return SiteAwareNonce
         */
        public function withSite(int $siteId) : \Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce;
    }
    /**
     * WordPress-specific nonce implementation.
     */
    final class WpNonce implements \Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce
    {
        /**
         * @var string
         */
        private $action;
        /**
         * @var int
         */
        private $siteId;
        /**
         * @param string $action
         */
        public function __construct(string $action)
        {
        }
        /**
         * @inheritdoc
         */
        public function withSite(int $siteId) : \Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce
        {
        }
        /**
         * @inheritdoc
         */
        public function __toString() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function action() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Nonce\Context $context = null) : bool
        {
        }
        /**
         * Returns a hash for the current action and site ID.
         *
         * @return string
         */
        private function actionHash() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * @method string basename()
     * @method string dirPath()
     * @method string dirUrl()
     * @method string filePath()
     * @method string name()
     * @method string website()
     * @method string version()
     * @method string textDomain()
     * @method string textDomainPath()
     */
    class PluginProperties implements \ArrayAccess
    {
        const BASENAME = 'basename';
        const DIR_PATH = 'dirPath';
        const DIR_URL = 'dirUrl';
        const FILE_PATH = 'filePath';
        const NAME = 'name';
        const WEBSITE = 'website';
        const VERSION = 'version';
        const TEXT_DOMAIN = 'textDomain';
        const TEXT_DOMAIN_PATH = 'textDomainPath';
        /**
         * @var array
         */
        private $properties;
        /**
         * @param string $pluginFilePath
         */
        public function __construct(string $pluginFilePath)
        {
        }
        /**
         * @param string $name
         * @param array $args
         * @return string
         */
        public function __call(string $name, array $args = []) : string
        {
        }
        /**
         * Checks if a property with the given name exists.
         *
         * @param string $name
         * @return bool
         */
        public function offsetExists($name) : bool
        {
        }
        /**
         * Returns the value of the property with the given name.
         *
         * @param string $offset
         * @return mixed
         * @throws \OutOfRangeException If there is no property with the given name.
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * Disabled.
         *
         * @inheritdoc
         *
         * @throws \BadMethodCallException
         */
        public function offsetSet($offset, $value) : void
        {
        }
        /**
         * Disabled.
         *
         * @inheritdoc
         *
         * @throws \BadMethodCallException
         */
        public function offsetUnset($offset) : void
        {
        }
    }
    /**
     * Version number implementation according to the SemVer specification.
     *
     * @see http://semver.org/#semantic-versioning-specification-semver
     */
    class SemanticVersionNumber
    {
        const FALLBACK_VERSION = '0.0.0';
        /**
         * @var string
         */
        private $version;
        /**
         * @param string $version
         */
        public function __construct(string $version)
        {
        }
        /**
         * Returns the version string.
         *
         * @return string
         */
        public function __toString() : string
        {
        }
        /**
         * Formats the given number according to the Semantic Versioning specification.
         *
         * @param string $version
         * @return string
         *
         * @see http://semver.org/#semantic-versioning-specification-semver
         */
        private function normalize(string $version) : string
        {
        }
        /**
         * Returns a 3 items array with the 3 parts of SemVer specs, in order:
         * - The numeric part of SemVer specs
         * - The pre-release part of SemVer specs, could be empty
         * - The meta part of SemVer specs, could be empty.
         *
         * @param string $version
         * @return string[]
         */
        private function matchSemverPattern(string $version) : array
        {
        }
        /**
         * Sanitizes given identifier according to SemVer specs.
         * Allow for underscores, replacing them with hyphens.
         *
         * @param string $identifier
         * @return string
         */
        private function sanitizeIdentifier(string $identifier) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service {
    /**
     * Interface for all bootstrappable service provider implementations.
     */
    interface BootstrappableServiceProvider extends \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * Bootstraps the registered services.
         *
         * @param Container $container
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container);
    }
    /**
     * Append-only container implementation to be used for dependency management.
     */
    class Container implements \ArrayAccess
    {
        /**
         * @var callable[]
         */
        private $factories = [];
        /**
         * @var array
         */
        private $values = [];
        /**
         * @var string[]
         */
        private $shared = [];
        /**
         * @var string[]
         */
        private $factoryOnly = [];
        /**
         * @var bool
         */
        private $isBootstrapped = false;
        /**
         * @var bool
         */
        private $isLocked = false;
        /**
         * @param array $values
         */
        public function __construct(array $values = [])
        {
        }
        /**
         * Bootstraps (and locks) the container.
         *
         * Only shared values and factory callbacks are accessible from now on.
         */
        public function bootstrap()
        {
        }
        /**
         * Locks the container.
         *
         * A locked container cannot be manipulated anymore.
         * All stored values and factory callbacks are still accessible.
         */
        public function lock()
        {
        }
        /**
         * Returns true when either a service factory callback or a value with the given name are stored
         * stored in the container.
         *
         * PSR-11 compatible.
         *
         * @param string $name
         * @return bool
         */
        public function has(string $name) : bool
        {
        }
        /**
         * Retrieve a service or a value from the container.
         *
         * Values are just returned.
         * Services are built first time they are requested.
         * Stored factories are always executed and the returned value is returned.
         *
         * PSR-11 compatible.
         *
         * @param string $name
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function get(string $name)
        {
        }
        /**
         * Stores the given service factory callback with the given name.
         *
         * @param string $name
         * @param callable $factory
         * @return Container
         * @throws Exception\NameOverwriteNotAllowed
         * @throws Exception\WriteAccessOnLockedContainer
         */
        public function addService(string $name, callable $factory) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Stores the given value with the given name.
         *
         * Scalar values are automatically shared.
         *
         * @param string $name
         * @param $value
         * @return Container
         * @throws Exception\NameOverwriteNotAllowed
         * @throws Exception\WriteAccessOnLockedContainer
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function addValue(string $name, $value) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * @param string $name
         * @param callable $factory
         * @return Container
         */
        public function addFactory(string $name, callable $factory) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Stores the given value or factory callback with the given name, and defines it to be
         * accessible even after the container has been bootstrapped.
         *
         * @param string $name
         * @param callable $factory
         * @return Container
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function share(string $name, callable $factory) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Stores the given value with the given name, and defines it to be
         * accessible even after the container has been bootstrapped.
         *
         * Scalar values are automatically shared.
         *
         * @param string $name
         * @param $value
         * @return Container
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function shareValue(string $name, $value) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * @param string $name
         * @param callable $value
         * @return Container
         */
        public function shareFactory(string $name, callable $value) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Replaces the factory callback with the given name with the given factory callback.
         *
         * The new factory callback will receive as first argument the object created by the current
         * factory, and as second argument the container.
         *
         * @param string $name
         * @param callable $factory
         * @return Container
         * @throws Exception\WriteAccessOnLockedContainer
         * @throws Exception\NameNotFound
         * @throws Exception\NameOverwriteNotAllowed
         */
        public function extend(string $name, callable $factory) : \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * @inheritdoc
         *
         * @see Container::has()
         */
        public function offsetExists($offset) : bool
        {
        }
        /**
         * @inheritdoc
         *
         * @see Container::get()
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * @inheritdoc
         *
         * @see Container::addService()
         * @see Container::addValue()
         */
        public function offsetSet($offset, $value) : void
        {
        }
        /**
         * Removing values or factory callbacks is not allowed.
         *
         * @param string $offset
         * @throws Exception\UnsetNotAllowed
         */
        public function offsetUnset($offset) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service\Exception {
    /**
     * Exception base class for all exceptions thrown by the container.
     *
     * This is necessary to be able to catch all exceptions thrown in the module in one go.
     * Moreover, compliance with PSR-11 would be easier, with pretty much no code necessary.
     */
    class ContainerException extends \Exception
    {
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class InvalidValueAccess extends \Inpsyde\MultilingualPress\Framework\Service\Exception\ContainerException
    {
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class ExtendingResolvedNotAllowed extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
        /**
         * @param string $name
         * @return ExtendingResolvedNotAllowed
         */
        public static function forName(string $name) : self
        {
        }
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class InvalidValueReadAccess extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
    }
    /**
     * Exception to be thrown when a not shared value or factory callback is to be accessed on a
     * bootstrapped container.
     */
    class LateAccessToNotSharedService extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueReadAccess
    {
        /**
         * @param string $name
         * @param string $action
         * @return self
         */
        public static function forService(string $name, string $action) : self
        {
        }
    }
    /**
     * Exception to be thrown when a value or factory callback could not be found in the container.
     */
    class NameNotFound extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueReadAccess
    {
        /**
         * Returns a new exception object.
         *
         * @param string $name
         * @return self
         */
        public static function forName(string $name) : self
        {
        }
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class NameOverwriteNotAllowed extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
        /**
         * @param string $name
         * @return NameOverwriteNotAllowed
         */
        public static function forServiceName(string $name) : self
        {
        }
        /**
         * @param string $name
         * @return NameOverwriteNotAllowed
         */
        public static function forValueName(string $name) : self
        {
        }
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class UnsetNotAllowed extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
        /**
         * @param string $name
         * @return UnsetNotAllowed
         */
        public static function forName(string $name) : self
        {
        }
    }
    /**
     * Exception to be thrown when a locked container is to be manipulated.
     */
    class WriteAccessOnLockedContainer extends \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
    {
        /**
         * @param string $name
         * @return WriteAccessOnLockedContainer
         */
        public static function forName(string $name) : self
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service {
    /**
     * Interface for all integration service provider implementations.
     */
    interface IntegrationServiceProvider extends \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * Integrates the registered services with MultilingualPress.
         *
         * @param Container $container
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container);
    }
    /**
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    class ServiceProvidersCollection implements \Countable
    {
        /**
         * @var \SplObjectStorage
         */
        private $storage;
        public function __construct()
        {
        }
        /**
         * Adds the given service provider to the collection.
         *
         * @param ServiceProvider $provider
         * @return ServiceProvidersCollection
         */
        public function add(\Inpsyde\MultilingualPress\Framework\Service\ServiceProvider $provider) : \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Removes the given service provider from the collection.
         *
         * @param ServiceProvider $provider
         * @return ServiceProvidersCollection
         */
        public function remove(\Inpsyde\MultilingualPress\Framework\Service\ServiceProvider $provider) : \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Calls the method with the given name on all registered providers,
         * and passes on potential further arguments.
         *
         * @param string $methodName
         * @param array ...$args
         */
        public function applyMethod(string $methodName, ...$args)
        {
        }
        /**
         * Executes the given callback for all registered providers,
         * and passes along potential further arguments.
         *
         * @param callable $callback
         * @param array ...$args
         */
        public function applyCallback(callable $callback, ...$args)
        {
        }
        /**
         * Executes the given callback for all registered providers, and returns the instance that
         * contains the providers that passed the filtering.
         *
         * @param callable $callback
         * @param array ...$args
         * @return ServiceProvidersCollection
         */
        public function filter(callable $callback, ...$args) : \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Executes the given callback for all registered providers, and returns the instance that
         * contains the providers obtained.
         *
         * @param callable $callback
         * @param array ...$args
         * @return ServiceProvidersCollection
         * @throws \UnexpectedValueException If a given callback did not return a service provider instance.
         */
        public function map(callable $callback, ...$args) : \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Executes the given callback for all registered providers, and passes along the result of
         * previous callback.
         *
         * @param callable $callback
         * @param mixed $initial
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function reduce(callable $callback, $initial = null)
        {
        }
        /**
         * Returns the number of providers in the collection.
         *
         * @return int
         */
        public function count() : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting {
    /**
     * The interface for options of settings.
     */
    interface SettingOptionInterface
    {
        /**
         * The setting id
         *
         * @return string
         */
        public function id() : string;
        /**
         * The setting value
         *
         * @return string
         */
        public function value() : string;
        /**
         * The setting label
         *
         * @return string
         */
        public function label() : string;
        /**
         * The setting option description
         *
         * @return string
         */
        public function description() : string;
    }
    class SettingOption implements \Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface
    {
        protected $id;
        protected $value;
        protected $callback;
        protected $label;
        protected $description;
        protected $type;
        /**
         * @param string $id The setting id
         * @param string $value The setting value
         * @param string $label The setting label
         * @param string $description The setting description
         */
        public function __construct(string $id, string $value, string $label, string $description)
        {
        }
        /**
         * @return string The setting id
         */
        public function id() : string
        {
        }
        /**
         * @return string The setting value
         */
        public function value() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function description() : string
        {
        }
    }
    /**
     * Settings box view to show additional information (e.g., for a module).
     */
    class SettingsBoxView
    {
        const KSES_TAGS = ['label' => ['class' => true, 'for' => true]];
        /**
         * @var SettingsBoxViewModel
         */
        private $model;
        /**
         * @param SettingsBoxViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\SettingsBoxViewModel $model)
        {
        }
        /**
         * Renders the complete settings box content.
         */
        public function render()
        {
        }
        /**
         * Renders the title, if not empty.
         */
        private function renderTitle()
        {
        }
        /**
         * Renders the description, if not empty.
         */
        private function renderDescription()
        {
        }
    }
    /**
     * Interface for all settings box view model implementations.
     */
    interface SettingsBoxViewModel
    {
        /**
         * Returns the description.
         *
         * @return string
         */
        public function description() : string;
        /**
         * Returns the ID of the container element.
         *
         * @return string
         */
        public function id() : string;
        /**
         * Returns the ID of the form element to be used by the label in order to
         * make it accessible for screen readers.
         *
         * @return string
         */
        public function labelId() : string;
        /**
         * Renders the markup for the settings box.
         */
        public function render();
        /**
         * Returns the title of the settings box.
         *
         * @return string
         */
        public function title() : string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting\Site {
    /**
     * Site setting.
     */
    class SiteSetting
    {
        /**
         * @var bool
         */
        private $checkUser;
        /**
         * @var SiteSettingViewModel
         */
        private $model;
        /**
         * @var SiteSettingUpdater
         */
        private $updater;
        /**
         * @param SiteSettingViewModel $model
         * @param SiteSettingUpdater $updater
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel $model, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingUpdater $updater, bool $checkUser = true)
        {
        }
        /**
         * Registers the according callbacks.
         *
         * @param string $renderHook
         * @param string $updateHook
         */
        public function register(string $renderHook, string $updateHook = '')
        {
        }
    }
    /**
     * Interface for all site setting view implementations.
     */
    interface SiteSettingView
    {
        /**
         * Renders the site setting markup.
         *
         * @param int $siteId
         * @return bool
         */
        public function render(int $siteId) : bool;
    }
    /**
     * Site setting view implementation for multiple single settings.
     */
    final class SiteSettingMultiView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        /**
         * @var bool
         */
        private $checkUser;
        /**
         * @var SiteSettingView[]
         */
        private $views;
        /**
         * Returns a new instance.
         *
         * @param SiteSettingViewModel[] $settings
         * @param bool $checkUser
         * @return SiteSettingMultiView
         */
        public static function fromViewModels(array $settings, bool $checkUser = true) : \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingMultiView
        {
        }
        /**
         * @param SiteSettingView[] $views
         * @param bool $checkUser
         */
        public function __construct(array $views, bool $checkUser = true)
        {
        }
        /**
         * @inheritdoc
         *
         * @wp-hook SiteSettingsSectionView::ACTION_AFTER . '_' . NewSiteSettings::SECTION_ID
         */
        public function render(int $siteId) : bool
        {
        }
    }
    /**
     * Site setting view implementation for a single setting.
     */
    final class SiteSettingSingleView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        /**
         * @var bool
         */
        private $checkUser;
        /**
         * @var SiteSettingViewModel
         */
        private $model;
        /**
         * @param SiteSettingViewModel $model
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel $model, bool $checkUser = true)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId) : bool
        {
        }
    }
    class SiteSettingUpdater
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var string
         */
        private $option;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param string $option
         * @param Request $request
         * @param Nonce|null $nonce
         */
        public function __construct(string $option, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce = null)
        {
        }
        /**
         * Updates the setting with the given data for the site with the given ID.
         *
         * @param int $siteId
         * @return bool
         */
        public function update(int $siteId) : bool
        {
        }
    }
    /**
     * Site setting view implementation for a whole settings section.
     */
    final class SiteSettingsSectionView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        const ACTION_AFTER = 'multilingualpress.after_site_settings';
        const ACTION_BEFORE = 'multilingualpress.before_site_settings';
        /**
         * @var SiteSettingsSectionViewModel
         */
        private $model;
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId = 0) : bool
        {
        }
    }
    /**
     * Interface for all site settings section view model implementations.
     */
    interface SiteSettingsSectionViewModel
    {
        /**
         * Returns the ID of the site settings section.
         *
         * @return string
         */
        public function id() : string;
        /**
         * Returns the markup for the site settings section.
         *
         * @param int $siteId
         * @return bool
         */
        public function renderView(int $siteId) : bool;
        /**
         * Returns the title of the site settings section.
         *
         * @return string
         */
        public function title() : string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting\User {
    /**
     * User setting.
     */
    class UserSetting
    {
        /**
         * @var bool
         */
        private $checkUser;
        /**
         * @var UserSettingViewModel
         */
        private $model;
        /**
         * @var UserSettingUpdater
         */
        private $updater;
        /**
         * @param UserSettingViewModel $model
         * @param UserSettingUpdater $updater
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel $model, \Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingUpdater $updater, bool $checkUser = true)
        {
        }
        /**
         * Registers the according callbacks.
         */
        public function register()
        {
        }
    }
    /**
     * User setting updater implementation validating a nonce specific to the update action included in
     * the request data.
     */
    class UserSettingUpdater
    {
        /**
         * @var string
         */
        private $metaKey;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param string $metaKey
         * @param Request $request
         * @param Nonce|null $nonce
         */
        public function __construct(string $metaKey, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce = null)
        {
        }
        /**
         * Updates the setting with the data in the request for the user with the given ID.
         *
         * @param int $userId
         * @return bool
         */
        public function update(int $userId) : bool
        {
        }
    }
    /**
     * User setting view.
     */
    class UserSettingView
    {
        /**
         * @var bool
         */
        private $checkUser;
        /**
         * @var UserSettingViewModel
         */
        private $model;
        /**
         * @param UserSettingViewModel $model
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel $model, bool $checkUser = true)
        {
        }
        /**
         * Renders the user setting markup.
         *
         * @param \WP_User $user
         * @return bool
         */
        public function render(\WP_User $user) : bool
        {
        }
    }
    /**
     * Interface for all user setting view model implementations.
     */
    interface UserSettingViewModel
    {
        /**
         * Renders the user setting.
         *
         * @param \WP_User $user
         */
        public function render(\WP_User $user);
        /**
         * Returns the title of the user setting.
         *
         * @return string
         */
        public function title() : string;
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Trait SiteIdValidatorTrait
     * @package Inpsyde\MultilingualPress\Framework
     */
    trait SiteIdValidatorTrait
    {
        /**
         * @param int $siteId
         * @throws UnexpectedValueException
         */
        protected function siteIdMustBeGreaterThanZero(int $siteId)
        {
        }
    }
    /**
     * Trait ThrowableHandleCapableTrait
     * @package Inpsyde\MultilingualPress\Framework
     */
    trait ThrowableHandleCapableTrait
    {
        /**
         * @param Throwable $throwable
         * @throws Throwable
         */
        protected function handleThrowable(\Throwable $throwable)
        {
        }
        /**
         * @param Throwable $throwable
         */
        protected function logThrowable(\Throwable $throwable)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Translator {
    /**
     * Interface for all translator implementations.
     */
    interface Translator
    {
        /**
         * Returns the translation data for the given site, according to the given arguments.
         *
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return Translation
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation;
    }
    /**
     * Null translator implementation.
     */
    final class NullTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Url {
    /**
     * Escaped URL data type.
     */
    final class EscapedUrl implements \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * @var string
         */
        private $url;
        /**
         * @param string $url
         */
        public function __construct(string $url)
        {
        }
        /**
         * Returns the URL string.
         *
         * @return string
         */
        public function __toString() : string
        {
        }
    }
    /**
     * Class Url
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class SimpleUrl implements \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * @var string
         */
        private $url;
        /**
         * SimpleUrl constructor.
         * @param string $url
         * @throws InvalidArgumentException
         */
        public function __construct(string $url)
        {
        }
        /**
         * @inheritDoc
         */
        public function __toString() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Widget\Dashboard {
    class Options
    {
        /**
         * @var array|null
         */
        private static $allOptions;
        /**
         * @var string
         */
        private $widgetId;
        /**
         * @param string $widgetId
         */
        public function __construct(string $widgetId)
        {
        }
        /**
         * Returns the options for the widget with the given ID.
         *
         * @return array
         */
        public function options() : array
        {
        }
        /**
         * Returns a specific widget option, if available.
         *
         * @param string $name
         * @param mixed $default
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function option(string $name, $default = null)
        {
        }
        /**
         * Saves an array of options for the widget with the given ID.
         *
         * @param array $options
         * @return bool
         */
        public function updateAll(array $options = []) : bool
        {
        }
        /**
         * Saves a specific widget option.
         *
         * @param string $name
         * @param mixed $value
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function update(string $name, $value) : bool
        {
        }
        /**
         * Ensure options are stored in the static var and return them.
         *
         * @return array
         */
        private function allOptions() : array
        {
        }
    }
    /**
     * Interface for all dashboard widget view implementations.
     */
    interface View
    {
        /**
         * Renders the widget's view.
         *
         * @param array $widgetInstanceSettings
         */
        public function render(array $widgetInstanceSettings);
    }
    class Widget
    {
        /**
         * @var array
         */
        private $callbackArgs;
        /**
         * @var string
         */
        private $capability;
        /**
         * @var callable|null
         */
        private $controlCallback;
        /**
         * @var string
         */
        private $widgetId;
        /**
         * @var string
         */
        private $name;
        /**
         * @var View
         */
        private $view;
        /**
         * @param string $widgetId
         * @param string $widgetName
         * @param View $view
         * @param string $capability
         * @param array $callbackArgs
         * @param callable|null $controlCallback
         */
        public function __construct(string $widgetId, string $widgetName, \Inpsyde\MultilingualPress\Framework\Widget\Dashboard\View $view, string $capability = '', array $callbackArgs = [], callable $controlCallback = null)
        {
        }
        /**
         * Registers the widget.
         *
         * @return bool
         */
        public function register() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Widget\Sidebar {
    /**
     * Trait to be used by all self-registering widget implementations.
     *
     * @see Widget
     */
    trait SelfRegisteringWidgetTrait
    {
        /**
         * Registers the widget.
         *
         * @return bool
         */
        public function register() : bool
        {
        }
    }
    /**
     * Interface for all sidebar widget view implementations.
     */
    interface View
    {
        /**
         * Renders the widget's front end view.
         *
         * @param array $args
         * @param array $instance
         * @param string $idBase
         */
        public function render(array $args, array $instance, string $idBase);
    }
    /**
     * Interface for all registrable widget implementations.
     */
    interface Widget
    {
        /**
         * Registers the widget.
         *
         * @return bool
         */
        public function register() : bool;
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    class WordpressContext
    {
        const TYPE_ADMIN = 'admin';
        const TYPE_HOME = 'home';
        const TYPE_POST_TYPE_ARCHIVE = 'post-type-archive';
        const TYPE_SEARCH = 'search';
        const TYPE_SINGULAR = 'post';
        const TYPE_TERM_ARCHIVE = 'term';
        const TYPE_DATE_ARCHIVE = 'date-archive';
        const TYPE_CUSTOMIZER = 'customizer';
        /**
         * @var callable[]
         */
        private $callbacks;
        /**
         * @var string[]
         */
        private $types;
        /**
         * @param \WP_Query|null $wpQuery
         */
        public function __construct(\WP_Query $wpQuery = null)
        {
        }
        /**
         * Returns the (first) post type of the current request.
         *
         * @return string
         */
        public function postType() : string
        {
        }
        /**
         * Returns the ID of the queried object.
         *
         * For term archives, this is the term taxonomy ID (not the term ID).
         *
         * @return int
         */
        public function queriedObjectId() : int
        {
        }
        /**
         * Returns all types of the current request or empty string on failure.
         *
         * @return string[]
         */
        public function types() : array
        {
        }
        /**
         * Returns the type of the current request or empty string on failure.
         *
         * @return string
         */
        public function type() : string
        {
        }
        /**
         * Returns if the current request match given type.
         *
         * @param string $type
         * @return bool
         */
        public function isType(string $type) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF {
    /**
     * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
     * @psalm-type FieldType = 'repeater'|'group'|'flexible_content'|'image'|'gallery'|'taxonomy'|'clone'|'simple'|post_object
     * @psalm-type Field = array{name: string, value: mixed, type?: FieldType, layouts?: array<Field>}
     * phpcs:enable
     */
    class FieldCopier
    {
        /**
         * ACF field Types
         */
        protected const FIELD_TYPE_IMAGE = 'image';
        protected const FIELD_TYPE_GALLERY = 'gallery';
        protected const FIELD_TYPE_TAXONOMY = 'taxonomy';
        protected const FIELD_TYPE_POST_OBJECT = 'post_object';
        protected const FIELD_TYPE_RELATIONSHIP = 'relationship';
        protected const FILE_FIELD_TYPES_FILTER = 'multilingualpress_acf_file_field_types_filter';
        protected const DEFAULT_FILE_FIELD_TYPES = ['file', 'video', 'image', 'application'];
        /**
         * @var Copier
         */
        protected $copier;
        /**
         * @var array
         */
        private $acfFileFieldTypes;
        public function __construct(\Inpsyde\MultilingualPress\Attachment\Copier $copier)
        {
        }
        /**
         * Handle the copy of ACF Fields
         *
         * The Method is a callback for PostRelationSaveHelper::FILTER_SYNC_KEYS filter
         * It will receive the keys of the meta fields which should be synced and
         * will add the ACF field keys
         *
         * @param array $keysToSync The list of meta keys
         * where should be added the ACF field keys to be synced
         * @param RelationshipContext $context
         * @param Request $request
         * @return array The list of meta keys to be synced
         * @throws NonexistentTable
         */
        public function handleCopyACFFields(array $keysToSync, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * Gets the ACF field objects.
         *
         * Gets the ACF field object based on post meta key
         *
         * @param int $postId The id for the post for which to get ACF field objects
         * @return array The list of advanced custom fields
         * @psalm-return array<Field> The list of advanced custom fields
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        private function getACFFieldObjects(int $postId) : array
        {
        }
        /**
         * Extract all meta keys from the list of ACF fields.
         *
         * We need a list of ACF post meta keys, including field key reference that each field has,
         * cause this way the data for the target page will be complete immediately and the editor
         * wont have to save the page in order for ACF content to show up in the frontend.
         *
         * @param array $acfFieldObjects The list of advanced custom fields
         * @psalm-param array<Field> $acfFieldObjects The list of advanced custom fields
         * @return array<string> The list of ACF post meta keys
         */
        protected function extractACFFieldMetaKeys(array $acfFieldObjects) : array
        {
        }
        /**
         * Deals with ACF field types that need special handling such as files and taxonomies.
         *
         * @param array $acfFieldObjects The list of advanced custom fields
         * @psalm-param array<Field> $acfFieldObjects The list of advanced custom fields
         * @param RelationshipContext $context
         * @throws NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        private function handleSpecialACFFieldTypes(array $acfFieldObjects, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * The method will handle the Taxonomy type fields copy process
         *
         * @param string $fieldType The ACF field type, should be image, gallery or file
         * @param array|string|WP_Term $fieldValue The value of taxonomy field
         * @param RelationshipContext $context
         * @param string $fieldKey The ACF field key
         * @throws NonexistentTable
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        protected function handleTaxTypeFieldsCopy(string $fieldType, $fieldValue, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, string $fieldKey)
        {
        }
        /**
         * The method will handle the file type fields(image, gallery, file) copy process
         *
         * @param string $fieldType The ACF field type, should be image, gallery or file
         * @param array $fieldValue The ACF field value
         * @param RelationshipContext $context
         * @param string $fieldKey The ACF field key
         */
        protected function handleFileTypeFieldsCopy(string $fieldType, array $fieldValue, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, string $fieldKey)
        {
        }
        /**
         * The Method will copy the attachments from source site to the remote site
         *
         * @param RelationshipContext $context
         * @param array $attachmentIds The list of attachment IDs which should be copied to remote entity
         * @return array The list of the attachment IDs in remote site which are copied from source site
         */
        protected function copyAttachments(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, array $attachmentIds) : array
        {
        }
        /**
         * Filter the values of the ACF fields
         *
         * The Method will filter the values of the ACF fields in remote site and will replace them with
         * the correct ids which are copied from source site
         *
         * @param array $values The values which should be replaced in remote site fields
         * @param string $filedKey The ACF field Key of the remote site
         * for which the value should be filtered
         */
        protected function filterRemoteFieldValues(array $values, string $filedKey)
        {
        }
        /**
         * Gets the connected post IDs of a given ones.
         *
         * @param int[] $postIds The list of post IDs.
         * @param RelationshipContext $context
         * @return int[] The list of post IDs.
         * @throws NonexistentTable
         */
        protected function connectedPostIds(array $postIds, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * Gets the list of post IDs regarding how the field is configured.
         *
         * @param string $returnType The return type configuration.
         * @psalm-param 'id'|'object' $returnType
         * @param int[]|int|WP_Post[]|WP_Post $value The field value.
         * @return int[] The list of post IDs.
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        protected function selectedPostIdsByFieldConfig(string $returnType, $value) : array
        {
        }
        /**
         * Handles the clone type fields.
         *
         * @param string $fieldValue The field value.
         * @param string $fieldKey The field key.
         * @return array The clone field.
         * @psalm-return Field
         */
        protected function handleCloneFields(string $fieldValue, string $fieldKey) : array
        {
        }
    }
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'acf';
        /**
         * @inheritDoc
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Setup Metabox Fields
         */
        private function activateMetaboxes()
        {
        }
        /**
         * Enable ACF fields copying functionality
         *
         * @param Container $container
         */
        private function enableCopyACFFields(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @return bool
         */
        private function isACFActive() : bool
        {
        }
        /**
         * Disable MLP settings for ACF custom post type.
         *
         * Regardless of whether the ACF module is active, ACF custom post type settings should be removed
         * from admin area, cause they are not translatable. The custom post type is called "Field Groups"
         */
        protected function disableSettingsForAcfEntities()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\Field {
    class CopyACFFields
    {
        const FILTER_COPY_ACF_FIELDS_IS_CHECKED = 'multilingualpress.copy_custom_fields_is_checked';
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post {
    /**
     * MultilingualPress ACF Metabox Fields
     */
    class MetaboxFields
    {
        const TAB = 'tab-custom-fields';
        const FIELD_COPY_ACF_FIELDS = 'remote-acf-fields-copy';
        /**
         * Retrieve all fields for the ACF metabox tab.
         *
         * @return MetaboxTab[]
         */
        public function allFieldsTabs() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar {
    /**
     * Replaces the site names in the admin bar with the respective alternative language titles.
     */
    class AdminBarCustomizer
    {
        /**
         * @var SettingsRepository
         */
        private $settingsRepository;
        /**
         * @param SettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar\SettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Replaces the current site's name with the site's alternative language title, if not empty.
         *
         * @param \WP_Admin_Bar $adminBar
         * @return \WP_Admin_Bar
         */
        public function replaceSiteName(\WP_Admin_Bar $adminBar) : \WP_Admin_Bar
        {
        }
        /**
         * Replaces all site names with the individual site's alternative language title, if not empty.
         *
         * @param \WP_Admin_Bar $adminBar
         * @return \WP_Admin_Bar
         */
        public function replaceSiteNodes(\WP_Admin_Bar $adminBar) : \WP_Admin_Bar
        {
        }
    }
    /**
     * Module service provider.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'alternative_language_title';
        const SETTING_NONCE_ACTION = 'multilingualpress_save_alt_language_title_setting_nonce_';
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function registerSettings(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function activateModuleForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param SiteSetting $setting
         */
        private function activateModuleForNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSetting $setting)
        {
        }
    }
    class SettingsRepository
    {
        const OPTION_SITE = 'multilingualpress_alt_language_title';
        /**
         * Returns the alternative language title of the site with the given ID.
         *
         * @param int $siteId
         * @return string
         */
        public function alternativeLanguageTitle(int $siteId) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\BeaverBuilder {
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'beaverbuilder';
        const BEAVERBUILDER_POSTMETA_METAKEYS = 'beaverbuilder.postmeta.metakeys';
        public const CONFIGURATION_NAME_FOR_UNSUPPORTED_POST_TYPES = 'beaverbuilder.UnsupportedPostTypes';
        public const CONFIGURATION_NAME_FOR_FILTERS_NEEDED_TO_REMOVE_ENTITIES_SUPPORT = 'beaverbuilder.FiltersNeededToRemoveEntitiesSupport';
        /**
         * @inheritdoc
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * When "Copy source content" option is checked the method will copy the additional postmeta data
         * Which is needed for Beaver Builder to work correctly.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        protected function handleCopyContentEditions(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Copy Meta data from source post to remote post
         *
         * @param array $data Metadata to be copied
         * @param RelationshipContext $context
         */
        protected function copyMetaData(array $data, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @return bool
         */
        protected function isBeaverBuilderActive() : bool
        {
        }
        /**
         * Removes the support of beaver entities for translation metaboxes.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService | NameNotFound
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function filterSupportForEntities(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\BlockType {
    /**
     * Represents the BlockType.
     *
     * @psalm-type name = string
     * @psalm-type type = array{type: string}
     * @psalm-type value = scalar|array
     */
    interface BlockTypeInterface
    {
        /**
         * The name of the block type.
         *
         * @return string
         */
        public function name() : string;
        /**
         * The block type category name, used in search interfaces to arrange block types by category.
         *
         * @return string
         */
        public function category() : string;
        /**
         * The block type icon.
         *
         * @return string
         */
        public function icon() : string;
        /**
         * The block type title.
         *
         * @return string
         */
        public function title() : string;
        /**
         * The block type description.
         *
         * @return string
         */
        public function description() : string;
        /**
         * Returns block type attributes config.
         *
         * @return array<string, mixed> A map of attribute name to type.
         * @psalm-return array<name, type>
         */
        public function attributes() : array;
        /**
         * Returns block extra config.
         *
         * These are additional custom configs which can contain block type specific information.
         *
         * @return array<string, mixed> A map of extra config name to value.
         * @psalm-return array<name, value>
         */
        public function extra() : array;
        /**
         * Renders the block type with given attributes.
         *
         * @param array<string, mixed> $attributes A map of attribute name to value.
         * @psalm-param array<name, value> $attributes
         * @return string
         * @throws RuntimeException If problem rendering.
         */
        public function render(array $attributes) : string;
        /**
         * The context factory.
         *
         * @return ContextFactoryInterface
         */
        public function contextFactory() : \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface;
        /**
         * Returns the template path of a block type.
         *
         * @return string The template path.
         */
        public function templatePath() : string;
    }
    class BlockType implements \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface
    {
        /**
         * @var string
         */
        protected $name;
        /**
         * @var string
         */
        protected $category;
        /**
         * @var array
         */
        protected $attributes;
        /**
         * @var string
         */
        protected $icon;
        /**
         * @var string
         */
        protected $title;
        /**
         * @var string
         */
        protected $description;
        /**
         * @var array
         */
        protected $extra;
        /**
         * @var string
         */
        protected $templatePath;
        /**
         * @var ContextFactoryInterface
         */
        protected $contextFactory;
        /**
         * @var TemplateRendererInterface
         */
        protected $templateRenderer;
        public function __construct(string $name, string $category, string $icon, string $title, string $description, array $attributes, array $extra, string $templatePath, \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface $contextFactory, \Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface $templateRenderer)
        {
        }
        /**
         * @inheritDoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function category() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function icon() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function title() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function description() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function attributes() : array
        {
        }
        /**
         * @inheritDoc
         */
        public function extra() : array
        {
        }
        /**
         * @inheritDoc
         */
        public function contextFactory() : \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface
        {
        }
        /**
         * @inheritDoc
         */
        public function templatePath() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(array $attributes) : string
        {
        }
    }
    /**
     * Can create a BlockType.
     *
     * @psalm-type optionName = string
     * @psalm-type optionValue = array{type: string}
     * @psalm-type extraValue = scalar|array
     * @psalm-type blockConfig = array{
     *      name: string,
     *      category: string,
     *      attributes: array<optionName, optionValue>,
     *      templatePath: string,
     *      contextFactory: ContextFactoryInterface,
     *      icon?: string,
     *      title?: string,
     *      description?: string,
     *      extra?: array<optionName, extraValue>,
     * }
     */
    interface BlockTypeFactoryInterface
    {
        /**
         * Creates a new block type instance with a given config.
         *
         * @param array $config The config.
         * @psalm-param blockConfig $config
         * @return BlockTypeInterface The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createBlockType(array $config) : \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface;
    }
    class BlockTypeFactory implements \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeFactoryInterface
    {
        /**
         * @var TemplateRendererInterface
         */
        protected $templateRenderer;
        public function __construct(\Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface $templateRenderer)
        {
        }
        /**
         * @inheritDoc
         */
        public function createBlockType(array $config) : \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar {
    /**
     * Can register a BlockType.
     */
    interface BlockTypeRegistrarInterface
    {
        /**
         * Register the given block type.
         *
         * @param BlockTypeInterface $blockType The block type to register.
         * @throws RuntimeException If failing to register.
         */
        public function register(\Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface $blockType) : void;
    }
    class BlockTypeRegistrar implements \Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar\BlockTypeRegistrarInterface
    {
        /**
         * @var string
         */
        protected $scriptName;
        public function __construct(string $scriptName)
        {
        }
        /**
         * @inheritDoc
         */
        public function register(\Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface $blockType) : void
        {
        }
        /**
         * Converts the given block name to JS variable name.
         *
         * @param string $blockName The name of the block.
         * @return string The converted JS variable name.
         */
        protected function blockNameAsVariableName(string $blockName) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\Context {
    /**
     * Can create a context from the given attributes.
     *
     * @psalm-type name = string
     * @psalm-type value = scalar|array
     */
    interface ContextFactoryInterface
    {
        /**
         * Creates the context from the given attributes.
         *
         * @param array<string, mixed> $attributes A map of attribute name to value.
         * @psalm-param array<name, value> $attributes
         * @return array<string, mixed> The context.
         * @psalm-return array<name, value>
         * @throws RuntimeException If problem creating.
         */
        public function createContext(array $attributes) : array;
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'blocks';
        public const SCRIPT_NAME_TO_REGISTER_BLOCK_SCRIPTS = 'multilingualpress-blocks';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Registers the given block types.
         *
         * @param BlockTypeRegistrarInterface $blockTypeRegistrar
         * @param BlockTypeInterface[] $blockTypes A list of block types.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function registerBlockTypes(\Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar\BlockTypeRegistrarInterface $blockTypeRegistrar, array $blockTypes) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer {
    /**
     * Can render the given template with the given context.
     */
    interface TemplateRendererInterface
    {
        /**
         * Renders the given template with the given context.
         *
         * @param string $templatePath The template path.
         * @param array<string, mixed> $context The context.
         * @return string The rendered HTML.
         * @throws RuntimeException If failing to render.
         */
        public function render(string $templatePath, array $context) : string;
    }
    class BlockTypeTemplateRenderer implements \Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface
    {
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.StaticClosure
         */
        public function render(string $templatePath, array $context) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\CommentsCopy {
    /**
     * Can copy the comments to the given sites.
     */
    interface CommentsCopierInterface
    {
        /**
         * Copies the comments by given comment IDs from give source site ID to the sites by given site IDs.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int[] $sourceCommentIds The list of comment IDs.
         * @param int[] $remoteSiteIds The list of site IDs.
         * @throws RuntimeException If problem copying.
         */
        public function copyCommentsToSites(int $sourceSiteId, array $sourceCommentIds, array $remoteSiteIds) : void;
    }
    class CommentsCopier implements \Inpsyde\MultilingualPress\Module\Comments\CommentsCopy\CommentsCopierInterface
    {
        public const ACTION_AFTER_REMOTE_COMMENT_IS_INSERTED = 'multilingualpress.after_remote_comment_is_inserted';
        /**
         * @var CommentsRelationshipContextFactoryInterface
         */
        protected $relationshipContextFactory;
        /**
         * @var CommentRelationSaveHelperInterface
         */
        protected $commentRelationSaveHelper;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function copyCommentsToSites(int $sourceSiteId, array $sourceCommentIds, array $remoteSiteIds) : void
        {
        }
        /**
         * Inserts the given comment to the given site.
         *
         * @param array $comment A map of WP_Comment fields to values.
         * @param int $siteId The site ID.
         * @return int The inserted comment id.
         * @throws RuntimeException|NonexistentTable If problem inserting.
         */
        protected function insertComment(array $comment, int $siteId) : int
        {
        }
        /**
         * Checks if the comment connection already exists in given remote site.
         *
         * @param int $commentId The comment ID.
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The remote site ID.
         * @return bool true if the comment connection exists, otherwise false.
         */
        protected function commentConnectionExistsInSite(int $commentId, int $sourceSiteId, int $remoteSiteId) : bool
        {
        }
        /**
         * Returns the remote comment parent ID.
         *
         * @param int $commentParentId The source comment parent ID.
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The remote site ID.
         * @return int The remote comment parent ID.
         */
        protected function remoteCommentParent(int $commentParentId, int $sourceSiteId, int $remoteSiteId) : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\RelationshipContext {
    /**
     * Can save the relationship for comments.
     */
    interface CommentRelationSaveHelperInterface
    {
        /**
         * Relates the comments of given relationship context.
         *
         * @param CommentsRelationshipContextInterface $context
         * @throws RuntimeException If problem relating.
         */
        public function relateComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context) : void;
        /**
         * Disconnects the comments of given relationship context.
         *
         * @param CommentsRelationshipContextInterface $context
         * @throws RuntimeException If problem disconnecting.
         */
        public function disconnectComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context) : void;
    }
    class CommentRelationSaveHelper implements \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface
    {
        public const ACTION_BEFORE_SAVE_COMMENT_RELATIONS = 'multilingualpress.before_save_comment_relations';
        public const ACTION_AFTER_SAVED_COMMENTS_RELATIONS = 'multilingualpress.after_saved_comment_relations';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function relateComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context) : void
        {
        }
        /**
         * @inheritDoc
         */
        public function disconnectComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context) : void
        {
        }
    }
    interface CommentsRelationshipContextInterface
    {
        /**
         * The remote comment ID.
         *
         * @return int
         */
        public function remoteCommentId() : int;
        /**
         * The remote comment object.
         *
         * @return WP_Comment|null
         */
        public function remoteComment() : ?\WP_Comment;
        /**
         * The remote post ID.
         *
         * @return int
         */
        public function remotePostId() : ?int;
        /**
         * The remote site ID.
         *
         * @return int
         */
        public function remoteSiteId() : int;
        /**
         * The remote comment parent comment ID.
         *
         * @return int
         */
        public function remoteCommentParentId() : ?int;
        /**
         * Returns whether the comment has connection.
         *
         * @return bool
         */
        public function hasRemoteComment() : bool;
        /**
         * The source comment ID.
         *
         * @return int
         */
        public function sourceCommentId() : int;
        /**
         * The source site ID.
         *
         * @return int
         */
        public function sourceSiteId() : int;
        /**
         * The source comment object.
         *
         * @return WP_Comment|null
         */
        public function sourceComment() : ?\WP_Comment;
        /**
         * Print HTML fields for the relationship context.
         *
         * @param MetaboxFieldsHelperInterface $helper
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper) : void;
    }
    class CommentsRelationshipContext implements \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
    {
        public const REMOTE_COMMENT_ID = 'remote_comment_id';
        public const REMOTE_POST_ID = 'remote_post_id';
        public const REMOTE_SITE_ID = 'remote_site_id';
        public const SOURCE_COMMENT_ID = 'source_comment_id';
        public const SOURCE_SITE_ID = 'source_site_id';
        protected const DEFAULTS = [self::REMOTE_COMMENT_ID => 0, self::REMOTE_POST_ID => 0, self::REMOTE_SITE_ID => 0, self::SOURCE_COMMENT_ID => 0, self::SOURCE_SITE_ID => 0];
        /**
         * @var WP_Comment[]
         */
        protected $comments = [];
        /**
         * @var array
         */
        protected $data;
        /**
         * Returns a new context object, instantiated according to the data in the given context object
         * and the array.
         *
         * @param CommentsRelationshipContext $context
         * @param array $data
         * @return CommentsRelationshipContext
         */
        public static function fromExistingAndData(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $context, array $data) : \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext
        {
        }
        public function __construct(array $data = [])
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteCommentId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteComment() : ?\WP_Comment
        {
        }
        /**
         * @inheritDoc
         */
        public function remotePostId() : ?int
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteSiteId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteCommentParentId() : ?int
        {
        }
        /**
         * @inheritDoc
         */
        public function hasRemoteComment() : bool
        {
        }
        /**
         * @inheritDoc
         */
        public function sourceCommentId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function sourceSiteId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function sourceComment() : \WP_Comment
        {
        }
        /**
         * Returns the comment object from given site by given type.
         *
         * @param int $siteId The site ID.
         * @param string $type The type: source or remote.
         * @return WP_Comment|null
         */
        protected function commentByType(int $siteId, string $type) : ?\WP_Comment
        {
        }
        /**
         * @inheritDoc
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper) : void
        {
        }
    }
    /**
     * Can create relationship context for comments.
     */
    interface CommentsRelationshipContextFactoryInterface
    {
        /**
         * Creates new relationship context for comments.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The remote site ID.
         * @param int $sourceCommentId The source comment ID.
         * @param int $remoteCommentId The remote comment ID.
         * @return CommentsRelationshipContextInterface The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createCommentsRelationshipContext(int $sourceSiteId, int $remoteSiteId, int $sourceCommentId, int $remoteCommentId) : \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface;
    }
    class CommentsRelationshipContextFactory implements \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createCommentsRelationshipContext(int $sourceSiteId, int $remoteSiteId, int $sourceCommentId, int $remoteCommentId) : \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments {
    /**
     * Service provider for Comments.
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'mlp-comments';
        /**
         * {@inheritDoc}
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * {@inheritdoc}
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * {@inheritdoc}
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound|\Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstraps frontend functionality.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound|\Throwable
         */
        protected function bootstrapFrontEnd(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstraps admin functionality.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound|NonexistentTable
         */
        protected function bootstrapAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstraps Network admin functionality.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound
         */
        protected function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : void
        {
        }
        /**
         * Bootstraps the translation metaboxes for comments.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound|NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function bootstrapMetaboxes(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : void
        {
        }
        /**
         * Will add the custom translation column in comments list view admin screen.
         *
         * @param TranslationColumnInterface $translationColumn
         */
        protected function bootstapTranslationColumnForListView(\Inpsyde\MultilingualPress\Framework\Admin\TranslationColumnInterface $translationColumn) : void
        {
        }
        /**
         * Will enqueue the module assets.
         *
         * @param AssetManager $assetManager
         * @param AssetFactory $assetFactory
         */
        protected function enqueueAssets(\Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager, \Inpsyde\MultilingualPress\Asset\AssetFactory $assetFactory)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\SiteSettings {
    class CommentSettingViewModel implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var SiteRelations
         */
        protected $siteRelations;
        /**
         * @var array<SettingOptionInterface>
         */
        protected $options;
        /**
         * @var WP_Post_Type
         */
        protected $postType;
        /**
         * @var CommentsSettingsRepositoryInterface
         */
        protected $siteTabSettingsRepository;
        public function __construct(array $options, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $siteTabSettingsRepository, string $postType)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * Renders the options for given source site.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The Remote site ID.
         * @param SettingOptionInterface $option The option.
         * @return void
         * @throws NonexistentTable
         */
        protected function renderOption(int $sourceSiteId, int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface $option) : void
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * Returns the comment setting option name for given post type.
         *
         * @param string $postType The post type name.
         * @param string $optionId The option ID name.
         * @return string The option name.
         */
        protected function fieldName(string $postType, string $optionId) : string
        {
        }
    }
    class CommentSettingsPageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var string
         */
        protected $action;
        /**
         * @var SettingsPageTabData
         */
        protected $data;
        /**
         * @var Nonce
         */
        protected $nonce;
        /**
         * @var int
         */
        protected $siteId;
        /**
         * @var SiteSettingView
         */
        protected $view;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view, int $siteId, string $action, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
    }
    /**
     * @psalm-type PostTypeName = string
     * @psalm-type OptionName = string
     * @psalm-type siteIds = list<int>
     * @psalm-type CommentSettings = array<PostTypeName, array<OptionName, siteIds>>
     */
    class CommentSettingsUpdater implements \Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable
    {
        public const ACTION_AFTER_COMMENT_SITE_SETTINGS_ARE_UPDATED = 'multilingualpress.after_comment_settings_are_updated';
        /**
         * @var Request
         */
        protected $request;
        /**
         * @var CommentsCopierInterface
         */
        protected $commentsCopier;
        /**
         * @var CommentsSettingsRepositoryInterface
         */
        protected $commentsSettingsRepository;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\Comments\CommentsCopy\CommentsCopierInterface $commentsCopier, \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function defineInitialSettings(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(int $siteId)
        {
        }
        /**
         * Updates the comment settings for the given site.
         *
         * @param array<string, array<string, int[]>> $commentsSettings The map of post type names to comment option values.
         * @psalm-param CommentSettings
         * @param int $siteId The site ID.
         */
        protected function updateCommentSettings(array $commentsSettings, int $siteId) : void
        {
        }
        /**
         * Returns the comment setting option values by given name.
         *
         * @param array<string, array<string, int[]>> $commentsSettings The map of post type names to comment option values.
         * @psalm-param CommentSettings $commentsSettings
         * @param string $optionName The comment setting option name.
         * @return array The map of post type names to comment setting option values.
         * @psalm-return CommentSettings
         */
        protected function commentSettingOptionValuesToSave(array $commentsSettings, string $optionName) : array
        {
        }
    }
    class CommentSettingsView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        public const ACTION_AFTER = 'multilingualpress.after_site_tab_settings';
        public const ACTION_BEFORE = 'multilingualpress.before_site_tab_settings';
        /**
         * @var SiteSettingsSectionViewModel
         */
        protected $model;
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId) : bool
        {
        }
    }
    /**
     * The repository for comment settings.
     *
     * @psalm-type PostTypeName = string
     * @psalm-type OptionName = string
     * @psalm-type siteIds = list<int>
     * @psalm-type CommentSettings = array<PostTypeName, array<OptionName, siteIds>>
     */
    interface CommentsSettingsRepositoryInterface
    {
        /**
         * Gets the given setting option value for the post type of the site.
         *
         * @param string $optionName The option name.
         * @param string $postTypeName The post type name.
         * @param int $siteId The site ID.
         * @return int[] The list of site IDs.
         */
        public function settingOptionValue(string $optionName, string $postTypeName, int $siteId) : array;
        /**
         * Gets all comment IDs of a given site for a given post type.
         *
         * @param string $postType The post type name.
         * @param int $siteId The site ID.
         * @return int[] A list of comment IDs.
         */
        public function postTypeComments(string $postType, int $siteId) : array;
        /**
         * Gets the comments settings of a given site.
         *
         * @param int $siteId The site ID.
         * @return array<string, array<string, int[]>> The map of post type names to comment setting option values.
         * @psalm-return CommentSettings
         */
        public function allSettings(int $siteId) : array;
    }
    class CommentsSettingsRepository implements \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface
    {
        public const COMMENTS_TAB_UPDATE_ACTION_NAME = 'update_multilingualpress_comments_site_settings';
        public const COMMENTS_TAB_NONCE_NAME = 'save_site_comment_settings';
        public const COMMENTS_TAB_SETTING = 'mlp_site_comments';
        public const COMMENTS_TAB_OPTION_COPY_COMMENTS = 'comments_copy';
        public const COMMENTS_TAB_OPTION_COPY_NEW_COMMENT = 'copy_new_comment';
        public const FILTER_COMMENTS_ENABLED_FOR_POST_TYPE = 'multilingualpress.are_comments_enabled_for_post_type';
        /**
         * @inheritDoc
         */
        public function settingOptionValue(string $optionName, string $postTypeName, int $siteId) : array
        {
        }
        /**
         * @inheritDoc
         */
        public function postTypeComments(string $postType, int $siteId) : array
        {
        }
        /**
         * @inheritDoc
         */
        public function allSettings(int $siteId) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Ajax {
    class AjaxSearchCommentRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        public const ACTION = 'multilingualpress_remote_comment_search';
        public const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_post_search_arguments';
        /**
         * @var CommentsRelationshipContextFactoryInterface
         */
        protected $relationshipContextFactory;
        /**
         * @var string
         */
        protected $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory, string $alreadyConnectedNotice)
        {
        }
        /**
         * @inheritDoc
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * Finds the comment for given context
         *
         * @param CommentsRelationshipContextInterface $context
         * @return array
         */
        protected function findComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context) : array
        {
        }
        /**
         * Creates the relationship context from given request.
         *
         * @param ServerRequest $request
         * @return CommentsRelationshipContextInterface
         * @throws RuntimeException if problem creating.
         */
        protected function createContextFromRequest(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request) : \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
        {
        }
        /**
         * Checks if the comment with given comment ID is connected to any comment from given site ID.
         *
         * @param int $commentId The comment ID.
         * @param int $siteId The site ID.
         * @return bool true if is connected, otherwise false.
         * @throws NonexistentTable
         */
        protected function isConnectedWithCommentOfSite(int $commentId, int $siteId) : bool
        {
        }
    }
    class AjaxUpdateCommentsRelationshipRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        public const ACTION = 'multilingualpress_update_comment_relationship';
        protected const AVAILABLE_TASKS = ['new', 'existing', 'remove'];
        /**
         * @var CommentsRelationshipContextFactoryInterface
         */
        protected $relationshipContextFactory;
        /**
         * @var ContentRelations
         */
        protected $contentRelations;
        /**
         * @var CommentMetaboxTabInterface[]
         */
        protected $metaboxTabs;
        /**
         * @var CommentMetaboxField[]
         */
        protected $metaboxFields;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $metaboxFieldsHelperFactory;
        /**
         * @var CommentRelationSaveHelperInterface
         */
        protected $commentRelationSaveHelper;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, array $metaboxTabs, array $metaboxFields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $metaboxFieldsHelperFactory, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritDoc
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * Creates the relationship context from given request.
         *
         * @param ServerRequest $request
         * @return CommentsRelationshipContextInterface
         * @throws RuntimeException if problem creating.
         */
        protected function createContextFromRequest(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request) : \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
        {
        }
        /**
         * Configures the given context.
         *
         * @param CommentsRelationshipContextInterface $context
         * @return CommentsRelationshipContextInterface
         * @throws NonexistentTable
         */
        protected function configureContext(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context) : \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi {
    class CommentMetabox implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox
    {
        public const RELATIONSHIP_TYPE = 'comment';
        public const ID_PREFIX = 'multilingualpress_comment_translation_metabox_';
        /**
         * @var string
         */
        protected $title;
        /**
         * @var CommentsRelationshipContextInterface
         */
        protected $relationshipContext;
        /**
         * @var CommentMetaboxTabInterface[]
         */
        protected $metaboxTabs;
        /**
         * @var CommentMetaboxField[]
         */
        protected $metaboxFields;
        /**
         * @var MetaboxFieldsHelperInterface
         */
        protected $metaboxFieldsHelper;
        /**
         * @var CommentRelationSaveHelperInterface
         */
        protected $commentRelationSaveHelper;
        public function __construct(string $title, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext, array $metaboxTabs, array $metaboxFields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $metaboxFieldsHelper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritDoc
         */
        public function siteId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info
        {
        }
        /**
         * @inheritdoc
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
        {
        }
        /**
         * @inheritdoc
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
    }
    /**
     * Represents the comment metabox tab.
     */
    interface CommentMetaboxTabInterface
    {
        /**
         * The id of the metabox tab.
         *
         * @return string
         */
        public function id() : string;
        /**
         * The label to show to the tab header.
         *
         * @return string
         */
        public function label() : string;
        /**
         * The fields collection for the current tab.
         *
         * @return CommentMetaboxField[]
         */
        public function fields() : array;
        /**
         * If the metabox tab is enabled or not.
         *
         * @param CommentsRelationshipContextInterface $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : bool;
        /**
         * Render the metabox markup.
         *
         * @param MetaboxFieldsHelper $helper
         * @param CommentsRelationshipContextInterface $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext);
    }
    class CommentMetaboxTab implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\CommentMetaboxTabInterface
    {
        public const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.TranslationUi.Comment.AfterTranslationUiTab';
        public const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.TranslationUi.Comment.BeforeTranslationUiTab';
        public const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.TranslationUi.Comment.TranslationUiShowTab';
        public const FILTER_COMMENT_METABOX_TAB = 'multilingualpress.TranslationUi.Comment.TranslationUiTab';
        /**
         * @var string
         */
        protected $id;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var CommentMetaboxField[]
         */
        protected $fields;
        public function __construct(string $id, string $label, array $fields)
        {
        }
        /**
         * @inheritDoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function fields() : array
        {
        }
        /**
         * @inheritDoc
         */
        public function enabled(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : bool
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext)
        {
        }
    }
    class CommentMetaboxView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @var CommentMetaboxTabInterface[]
         */
        protected $metaboxTabs;
        /**
         * @var MetaboxFieldsHelperInterface
         */
        protected $helper;
        /**
         * @var CommentsRelationshipContextInterface
         */
        protected $relationshipContext;
        public function __construct(array $metaboxTabs, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info)
        {
        }
        /**
         * Renders the metabox wrapper div HTML attributes.
         *
         * @return void
         */
        protected function boxDataAttributes() : void
        {
        }
        /**
         * Renders the metabox tab anchors.
         *
         * @param CommentMetaboxTabInterface $tab
         */
        protected function renderTabAnchor(\Inpsyde\MultilingualPress\Module\Comments\TranslationUi\CommentMetaboxTabInterface $tab) : void
        {
        }
        /**
         * Renders the "trashed" message.
         */
        private function renderTrashedMessage() : void
        {
        }
    }
    class CommentsListViewTranslationColumn implements \Inpsyde\MultilingualPress\Framework\Admin\TranslationColumnInterface
    {
        /**
         * @var string
         */
        protected $name;
        /**
         * @var string
         */
        protected $title;
        /**
         * @var ContentRelations
         */
        protected $contentRelations;
        public function __construct(string $name, string $title, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritDoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function title() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function value(int $id) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field {
    /**
     * Represents the comment metabox field.
     */
    interface CommentMetaboxField
    {
        /**
         * The key of the field.
         *
         * @return string
         */
        public function key() : string;
        /**
         * The field label.
         *
         * @return string
         */
        public function label() : string;
        /**
         * Renders the field by given context.
         *
         * @param CommentsRelationshipContextInterface $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void;
    }
    class CommentMetaboxCopyContent implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        public const FILTER_COPY_CONTENT_IS_CHECKED = 'multilingualpress.Comments.copy_content_is_checked';
        /**
         * @var string
         */
        protected $key;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
    }
    class CommentMetaboxFieldAuthorEmail implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        /**
         * @var string
         */
        private $key;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        /**
         * @var string
         */
        protected $label;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
    }
    class CommentMetaboxFieldAuthorName implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        /**
         * @var string
         */
        private $key;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
    }
    class CommentMetaboxFieldAuthorUrl implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        /**
         * @var string
         */
        private $key;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
    }
    class CommentMetaboxRelation implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        /**
         * @var string
         */
        protected $key;
        /**
         * @var string
         */
        protected $label;
        public function __construct(string $key, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a value for 'id' HTML attribute based on relation type.
         *
         * @param string $type The relation type (existing, new, remove, leave).
         * @return string The value for 'id' HTML attribute.
         */
        protected function relationFieldId(string $type, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper) : string
        {
        }
        /**
         * The relation field markup based on the relation type.
         *
         * @param string $fieldId The value for 'id' HTML attribute.
         * @param string $fieldName The value for 'name' HTML attribute.
         * @param string $type The relation type (existing, new, remove, leave).
         * @param string $description The field description.
         * @return void
         */
        protected function relationFieldMarkup(string $fieldId, string $fieldName, string $type, string $description) : void
        {
        }
        /**
         * Returns the relation field description based on relation type.
         *
         * @param string $type The relation type (existing, new, remove, leave).
         * @param string $languageName The language name.
         * @param bool $hasRemoteComment True if remote connection exists, otherwise false.
         * @param string $commentType The comment type. can be 'comment' or 'review' or custom type.
         * @return string The relation field description
         */
        protected function relationFieldDescription(string $type, string $languageName, bool $hasRemoteComment, string $commentType) : string
        {
        }
        /**
         * The "Search for remote site comments to connect" input markup.
         *
         * @param MetaboxFieldsHelper $helper
         * @return void
         */
        protected function searchRow(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper) : void
        {
        }
        /**
         * The update relation button markup.
         *
         * @return void
         */
        protected function buttonRow() : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
    }
    class CommentMetaboxStatus implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        public const FILTER_TRANSLATION_UI_POST_STATUSES = 'multilingualpress.translation_ui_comment_statuses';
        /**
         * @var string
         */
        protected $key;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        /**
         * Available comment statuses.
         *
         * @return array<string> The list of available comment statuses
         */
        protected function availableStatuses() : array
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi {
    /**
     * @psalm-type FieldName = string
     * @psalm-type FieldValue = scalar
     */
    class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        public const FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE = 'multilingualpress.taxonomies_slugs_before_remove';
        public const FILTER_NEW_RELATE_REMOTE_COMMENT_BEFORE_INSERT = 'multilingualpress.new_relate_remote_comment_before_insert';
        public const ACTION_METABOX_AFTER_RELATE_COMMENTS = 'multilingualpress.metabox_after_relate_comments';
        public const ACTION_METABOX_BEFORE_UPDATE_REMOTE_COMMENT = 'multilingualpress.metabox_before_update_remote_comment';
        public const ACTION_METABOX_AFTER_UPDATE_REMOTE_COMMENT = 'multilingualpress.metabox_after_update_remote_comment';
        /**
         * @var CommentMetaboxField[]
         */
        protected $metaboxFields;
        /**
         * @var MetaboxFieldsHelperInterface
         */
        private $fieldsHelper;
        /**
         * @var CommentsRelationshipContextInterface
         */
        private $relationshipContext;
        /**
         * @var CommentRelationSaveHelperInterface
         */
        protected $commentRelationSaveHelper;
        public function __construct(array $metaboxFields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $fieldsHelper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * Checks if the relationship should be updated based on given params.
         *
         * @param string $relationType The relation type (existing, new, remove, leave).
         * @param bool $hasRemoteComment True if connection exists, otherwise false.
         * @return bool true if relationship should be updated, otherwise false.
         */
        protected function shouldSaveComment(string $relationType, bool $hasRemoteComment) : bool
        {
        }
        /**
         * Returns the map of field keys to values from given request.
         *
         * @param Request $request
         * @return array<string, scalar> The map of field keys to values.
         * @psalm-return array<FieldName, FieldValue>
         */
        protected function allFieldsValues(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * Creates the remote comment data for given request.
         *
         * @param array<string, scalar> $values A map of field keys to values.
         * @psalm-param array<FieldName, FieldValue>
         * @param Request $request
         * @return array A map of WP_comment properties to values.
         */
        protected function createCommentData(array $values, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * Saves the given comment (inserts or updates) for given relation type.
         *
         * @param array $comment A map of WP_comment properties to values.
         * @param string $relationType The relation type (existing, new, remove, leave).
         * @return int The inserted or updated comment ID.
         */
        protected function saveComment(array $comment, string $relationType) : int
        {
        }
        /**
         * Checks if the field value with given name is changed for given request.
         *
         * @param string $fieldName The field name.
         * @param Request $request
         * @return bool true if the field value with given name is changed, otherwise false.
         */
        protected function isFieldValueChanged(string $fieldName, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Elementor {
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'elementor';
        const ELEMENTOR_POSTMETA_METAKEYS = 'elementor.postmeta.metakeys';
        const ELEMENTOR_ENTITIES_TO_REMOVE_SUPPORT = 'elementor.entities.slugs';
        const FILTERS_NEEDED_TO_REMOVE_ENTITIES_SUPPORT = 'filters.needed.to.remove.entities.support';
        /**
         * @inheritdoc
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * When "Copy source content" option is checked the method will copy the additional postmeta data
         * Which is needed for elementor to work correctly.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        protected function handleCopyContentEditions(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Copy Meta data from source post to remote post
         *
         * @param array $data Metadata to be copied
         * @param RelationshipContext $context
         */
        protected function copyMetaData(array $data, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @return bool
         */
        protected function isElementorActive() : bool
        {
        }
        /**
         * Elementor Post types and taxonomies doesn't need to be supported
         *
         * @param array $entities for which the support needs to be deleted
         * @param string $filter The filter name which is used to remove support
         *
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function filterSupportForEntities(array $entities, string $filter)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite {
    /**
     * Represents the ExternalSite.
     */
    interface ExternalSiteInterface
    {
        /**
         * The ID of the external site.
         *
         * @return int
         */
        public function id() : int;
        /**
         * The external site language name.
         *
         * @return string
         */
        public function languageName() : string;
        /**
         * The external site URL.
         *
         * @return string
         */
        public function siteUrl() : string;
        /**
         * The external site language locale.
         *
         * @return string
         */
        public function locale() : string;
        /**
         * Whether redirect is enabled for external site.
         *
         * @return bool
         */
        public function isRedirectEnabled() : bool;
        /**
         * Whether display of hreflang is enabled for external site.
         *
         * @return bool
         */
        public function isHreflangEnabled() : bool;
        /**
         * The external site display style.
         *
         * @return string
         */
        public function displayStyle() : string;
    }
    class ExternalSite implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
    {
        /**
         * @var int
         */
        protected $id;
        /**
         * @var string
         */
        protected $languageName;
        /**
         * @var string
         */
        protected $siteUrl;
        /**
         * @var string
         */
        protected $locale;
        /**
         * @var bool
         */
        protected $isRedirectEnabled;
        /**
         * @var bool
         */
        protected $isHreflangEnabled;
        /**
         * @var string
         */
        protected $displayStyle;
        public function __construct(int $id, string $siteUrl, string $languageName, string $locale, bool $isRedirectEnabled, bool $isHreflangEnabled, string $displayStyle)
        {
        }
        /**
         * @inheritDoc
         */
        public function id() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function languageName() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function siteUrl() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function locale() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function isRedirectEnabled() : bool
        {
        }
        /**
         * @inheritDoc
         */
        public function isHreflangEnabled() : bool
        {
        }
        /**
         * @inheritDoc
         */
        public function displayStyle() : string
        {
        }
    }
    /**
     * Can create an ExternalSite.
     *
     * @psalm-type externalSiteConfig = array{
     *      ID: int,
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      site_redirect: int,
     *      enable_hreflang: int,
     *      display_style: string,
     * }
     */
    interface ExternalSiteFactoryInterface
    {
        /**
         * Creates a new external site instance with a given config.
         *
         * @param array $config The config.
         * @psalm-param externalSiteConfig $config
         * @return ExternalSiteInterface The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createExternalSite(array $config) : \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface;
    }
    class ExternalSiteFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createExternalSite(array $config) : \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesMetaBox {
    /**
     * Can render an External sites MetaBox.
     */
    interface ExternalSitesMetaBoxViewInterface
    {
        /**
         * Renders the given external sites MetaBox HTML markup for given post.
         *
         * @param ExternalSiteInterface[] $externalSites The list of external sites.
         * @param int $postId The post ID.
         */
        public function render(array $externalSites, int $postId) : void;
    }
    class ExternalSitesMetaBoxView implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesMetaBox\ExternalSitesMetaBoxViewInterface
    {
        public const META_NAME = 'mlp-external-sites';
        /**
         * @inheritDoc
         */
        public function render(array $externalSites, int $postId) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository {
    /**
     * Represents the repository for ExternalSites.
     *
     * @psalm-type externalSiteData = array{
     *      ID: int,
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      enable_hreflang: int,
     *      site_redirect: int
     * }
     */
    interface ExternalSitesRepositoryInterface
    {
        /**
         * Deletes the external site with the given ID.
         *
         * @param int $id The external site ID.
         * @throws RuntimeException If problem deleting.
         */
        public function deleteExternalSite(int $id) : void;
        /**
         * Returns the list of all existing external sites.
         *
         * @return ExternalSiteInterface[] The list of all existing external sites.
         * @throws RuntimeException If problem returning.
         */
        public function allExternalSites() : array;
        /**
         * Inserts the external site entry according to the given data.
         *
         * @param array $externalSiteData The requested external site data.
         * @psalm-param externalSiteData $externalSiteData
         * @throws RuntimeException If problem inserting.
         */
        public function insertExternalSite(array $externalSiteData) : void;
        /**
         * Updates the external site entry according to the given data.
         *
         * @param int $siteId The external site ID to update.
         * @param array $externalSiteData The requested external site data.
         * @psalm-param externalSiteData $externalSiteData
         * @throws RuntimeException If problem updating.
         */
        public function updateExternalSite(int $siteId, array $externalSiteData) : void;
        /**
         * Returns the external site for the given column.
         *
         * @param string $column The column name.
         * @param string|int $value The column value.
         * @return ExternalSiteInterface|null The external site or null if it doesn't exist with the given params.
         * @throws RuntimeException If problem returning.
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function externalSiteBy(string $column, $value) : ?\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface;
        /**
         * Returns the auto increment value from external sites table.
         *
         * @return int
         */
        public function autoIncrementValue() : int;
    }
    /**
     * @psalm-type columnName = string
     * @psalm-type specification = string
     * @psalm-type externalSiteConfig = array{
     *      ID: int,
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      enable_hreflang: int,
     *      site_redirect: int,
     *      display_style: string
     * }
     */
    class ExternalSitesRepository implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface
    {
        /**
         * @var wpdb
         */
        protected $wpdb;
        /**
         * @var Table
         */
        protected $table;
        /**
         * @var ExternalSiteFactoryInterface
         */
        protected $externalSiteFactory;
        /**
         * @var string[]
         */
        protected $requiredColumnNames;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteFactoryInterface $externalSiteFactory, array $requiredColumnNames)
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function deleteExternalSite(int $id) : void
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function allExternalSites() : array
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function insertExternalSite(array $externalSiteData) : void
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function updateExternalSite(int $siteId, array $externalSiteData) : void
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function externalSiteBy(string $column, $value) : ?\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
        {
        }
        /**
         * @inheritDoc
         */
        public function autoIncrementValue() : int
        {
        }
        /**
         * Creates a new external site instance with a given config.
         *
         * @param array $config A map of external site field name to value.
         * @psalm-param externalSiteConfig $config
         * @return ExternalSiteInterface The new instance.
         */
        protected function createExternalSite(array $config) : \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
        {
        }
        /**
         * Extracts the column specifications from a given table.
         *
         * @param Table $table The table.
         * @return array<string, string> A map of column name to specification.
         * @psalm-return array<columnName, specification>
         */
        protected function extractColumnSpecifications(\Inpsyde\MultilingualPress\Framework\Database\Table $table) : array
        {
        }
        /**
         * finds the data specifications from the given column specifications.
         *
         * @param array<string, string> $columnSpecifications A map of column name to specification.
         * @psalm-param array<columnName, specification> $columnSpecifications
         * @param array $data The request data.
         * @psalm-param externalSiteConfig $data
         * @return string[] The list of specifications.
         */
        protected function findSpecifications(array $columnSpecifications, array $data) : array
        {
        }
        /**
         * Validates the required data.
         *
         * @param array $data The request data.
         * @psalm-param externalSiteConfig $data
         * @return void
         * @throws RuntimeException if validation fails.
         */
        protected function validateData(array $data) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags {
    /**
     * Can create flag for external sites.
     */
    interface ExternalSiteFlagFactoryInterface
    {
        /**
         * Creates flag image tag for given external site.
         *
         * @param ExternalSiteInterface $externalSite
         * @return string The flag(<img>) tag.
         */
        public function createFlagImageTag(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite) : string;
        /**
         * Creates flag image Url for given locale.
         *
         * @param string $externalSiteLocale The language locale.
         * @return string The flag image url.
         */
        public function createFlagUrl(string $externalSiteLocale) : string;
    }
    class ExternalSiteFlagFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface
    {
        /**
         * @var string
         */
        protected $pluginPath;
        /**
         * @var string
         */
        protected $pluginUrl;
        /**
         * @var string
         */
        protected $pathToFlagsFolder;
        public function __construct(string $pluginPath, string $pluginUrl, string $pathToFlagsFolder)
        {
        }
        /**
         * @inheritDoc
         */
        public function createFlagImageTag(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite) : string
        {
        }
        /**
         * @inheritDoc
         */
        public function createFlagUrl(string $externalSiteLocale) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations {
    class HreflangIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected $externalSites;
        /**
         * @var array
         */
        protected $ksesTags;
        public function __construct(array $externalSites, array $ksesTags)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate() : void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId) : string
        {
        }
    }
    class LanguageSwitcherWidgetIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected $externalSites;
        /**
         * @var LanguageSwitcherItemFactory
         */
        protected $itemFactory;
        /**
         * @var ExternalSiteFlagFactoryInterface
         */
        protected $externalSiteFlagFactory;
        /**
         * @var string
         */
        protected $externalSiteKeyWord;
        /**
         * @var bool
         */
        protected $isSiteFlagsModuleActive;
        public function __construct(array $externalSites, \Inpsyde\MultilingualPress\Module\LanguageSwitcher\ItemFactory $itemFactory, \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface $externalSiteFlagFactory, string $externalSiteKeyWord, bool $isSiteFlagsModuleActive)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate() : void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId) : string
        {
        }
    }
    class QuickLinksIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected $externalSites;
        public function __construct(array $externalSites)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate() : void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect {
    /**
     * Can create RedirectTarget for external site.
     *
     * @psalm-type redirectTargetConfig = array{
     *      locale: string,
     *      priority: int,
     *      siteId: int,
     *      url: string,
     *      user_priority: float,
     *      language_fallback_priority: int
     * }
     */
    interface ExternalSiteRedirectTargetFactoryInterface
    {
        /**
         * Creates a new RedirectTarget instance with a given config.
         *
         * @param array $config The config.
         * @psalm-param redirectTargetConfig $config
         * @return RedirectTarget The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createExternalSiteRedirectTarget(array $config) : \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget;
    }
    class ExternalSiteRedirectTargetFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\ExternalSiteRedirectTargetFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createExternalSiteRedirectTarget(array $config) : \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Settings {
    /**
     * Interface ViewRenderer
     * @package Inpsyde\MultilingualPress\Module\Redirect\Settings
     */
    interface ViewRenderer
    {
        /**
         * Print the Title for the Setting
         *
         * @return void
         */
        public function title();
        /**
         * Print the Settings
         *
         * @return void
         */
        public function content();
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\Fallback {
    class ExternalRedirectFallbackViewRenderer implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\ViewRenderer
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected $externalSites;
        /**
         * @var Repository
         */
        protected $repository;
        public function __construct(array $externalSites, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function title()
        {
        }
        /**
         * @inheritDoc
         */
        public function content()
        {
        }
        /**
         * Renders the options List of external sites that can be selected.
         *
         * @param ExternalSiteInterface[] $externalSites
         * @param int $selected The selected site ID.
         */
        protected function renderOptionsForSites(array $externalSites, int $selected)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect {
    class RedirectIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected $externalSites;
        /**
         * @var LanguageNegotiator
         */
        protected $languageNegotiator;
        /**
         * @var ExternalSiteRedirectTargetFactoryInterface
         */
        protected $externalSiteRedirectTargetFactory;
        /**
         * @var Repository
         */
        protected $redirectSettingsRepository;
        /**
         * @var ViewRenderer
         */
        protected $externalRedirectFallbackViewRenderer;
        /**
         * @var ExternalSitesRepositoryInterface
         */
        protected $externalSitesRepository;
        public function __construct(array $externalSites, \Inpsyde\MultilingualPress\Module\Redirect\LanguageNegotiator $languageNegotiator, \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\ExternalSiteRedirectTargetFactoryInterface $externalSiteRedirectTargetFactory, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $redirectSettingsRepository, \Inpsyde\MultilingualPress\Module\Redirect\Settings\ViewRenderer $externalRedirectFallbackViewRenderer, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate() : void
        {
        }
        /**
         * Integrates the redirect fallback functionality for external sites.
         *
         * @return void
         */
        protected function integrateRedirectFallback() : void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId) : string
        {
        }
        /**
         * Checks if redirect is enabled for any external site.
         *
         * @return bool true if redirect is enabled for any external site, otherwise false.
         */
        protected function isRedirectEnabledForAnyExternalSite() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu {
    class AjaxHandler
    {
        public const ACTION = 'multilingualpress_add_external_sites_to_nav_menu';
        /**
         * @var Nonce
         */
        protected $nonce;
        /**
         * @var Request
         */
        protected $request;
        /**
         * @var ExternalSiteMenuItemFactoryInterface
         */
        protected $externalSiteMenuItemFactory;
        /**
         * @var ExternalSiteInterface[]
         */
        protected $allExternalSites;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu\ExternalSiteMenuItemFactoryInterface $externalSiteMenuItemFactory, array $allExternalSites)
        {
        }
        /**
         * Handles the AJAX request and sends an appropriate response.
         */
        public function handle() : void
        {
        }
        /**
         * Gets the list of external site IDs from request.
         *
         * @return int[] The list of external site IDs.
         */
        protected function externalSiteIdsFromRequest() : array
        {
        }
    }
    /**
     * Can create a menu item for external site.
     *
     */
    interface ExternalSiteMenuItemFactoryInterface
    {
        /**
         * Creates a new menu item in given menu for given external site
         *
         * @param int $menuId The WordPress navigation menu ID.
         * @param ExternalSiteInterface $externalSite The external site.
         * @return WP_Post The WordPress menu item object.
         * @throws RuntimeException If problem creating.
         */
        public function createExternalSiteMenuItem(int $menuId, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite) : \WP_Post;
    }
    class ExternalSiteMenuItemFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu\ExternalSiteMenuItemFactoryInterface
    {
        public const META_KEY_EXTERNAL_SITE_ID = '_external_site_id';
        public const META_KEY_ITEM_TYPE = '_menu_item_type';
        protected const ITEM_TYPE = 'mlp_external_site';
        protected const FILTER_MENU_EXTERNAL_SITE_NAME = 'multilingualpress.nav_menu_external_site_name';
        /**
         * @inheritDoc
         */
        public function createExternalSiteMenuItem(int $menuId, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite) : \WP_Post
        {
        }
    }
    class MetaBoxView
    {
        public const ID = 'mlp-navMenu-external-sites';
        /**
         * @var ExternalSiteInterface[]
         */
        protected $externalSites;
        /**
         * @var string
         */
        protected $selectAllUrl;
        /**
         * @var array
         */
        protected $submitButtonAttributes;
        public function __construct(array $externalSites, string $selectAllUrl, array $submitButtonAttributes)
        {
        }
        /**
         * @inheritDoc
         */
        public function render() : void
        {
        }
        /**
         * Renders checkboxes to select external sites.
         */
        protected function renderCheckboxes() : void
        {
        }
        /**
         * Renders a single item for given external site with given name.
         *
         * @param string $name The item name.
         * @param int $siteId The external site ID.
         */
        protected function renderCheckbox(string $name, int $siteId) : void
        {
        }
        /**
         * Renders the button controls HTML.
         */
        protected function renderButtonControls() : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'external-sites';
        public const NONCE_ACTION_FOR_EXTERNAL_SITES_NAV_MENU = 'add_external_sites_to_nav_menu';
        public const CONFIGURATION_NAME_FOR_EXTERNAL_SITE_KEYWORD = 'multilingualpress.ExternalSites.ExternalSiteKeyWord';
        public const CONFIGURATION_NAME_FOR_EXTERNAL_SITE_DISPLAY_STYLES = 'multilingualpress.ExternalSites.DisplayStyle';
        public const CONFIGURATION_NAME_FOR_FLAGS_FOLDER_PATH = 'multilingualpress.FlagsFolderPath';
        public const CONFIGURATION_NAME_FOR_UNSUPPORTED_POST_TYPES = 'multilingualpress.ExternalSites.Integrations.UnsupportedPostTypes';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Performs various tasks when is in admin screen on module activation.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         */
        protected function activateModuleForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Will enqueue the module assets.
         *
         * @param AssetManager $assetManager
         * @param AssetFactory $assetFactory
         */
        protected function enqueueAssets(\Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager, \Inpsyde\MultilingualPress\Asset\AssetFactory $assetFactory)
        {
        }
        /**
         * Renders the MetaBoxes for given external sites.
         *
         * @param ExternalSiteInterface[] $externalSites The list of external sites.
         * @param ExternalSitesMetaBoxViewInterface $externalSitesMetaBoxView
         * @param string[] $unsupportedPostTypes The list of unsupported post types.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function renderMetaBoxes(array $externalSites, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesMetaBox\ExternalSitesMetaBoxViewInterface $externalSitesMetaBoxView, array $unsupportedPostTypes)
        {
        }
        /**
         * Saves the requested external sites metabox values.
         *
         * @param ServerRequest $request
         */
        protected function saveMetaBoxes(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * Filters the external site menu item on frontend.
         *
         * @param ExternalSitesRepository $externalSitesRepository
         * @param bool $isSiteFlagsModuleActive true if the site flags module is active, otherwise false.
         * @param ExternalSiteFlagFactoryInterface $externalSiteFlagImageTagFactory
         * @throws Throwable
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function filterExternalSiteMenuItem(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepository $externalSitesRepository, bool $isSiteFlagsModuleActive, \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface $externalSiteFlagImageTagFactory) : void
        {
        }
        /**
         * Filters the menu items for external sites.
         *
         * @param wpdb $wpdb
         */
        protected function filterMenuItems(\wpdb $wpdb) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Settings {
    class PageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var Nonce
         */
        protected $nonce;
        /**
         * @var Request
         */
        protected $request;
        /**
         * @var TableFormView
         */
        protected $table;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\ExternalSites\Settings\TableFormView $table)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
        /**
         * Renders the form.
         *
         * @return void
         */
        protected function renderForm() : void
        {
        }
    }
    /**
     * @psalm-type Action = 'insert'|'update'|'delete'
     *
     * @psalm-type Item = array{
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      site_redirect?: int,
     *      enable_hreflang?: int
     * }
     */
    class RequestHandler
    {
        public const ACTION = 'update_multilingualpress_external_sites';
        public const ACTION_AFTER_EXTERNAL_SITE_IS_DELETED = 'multilingualpress.after_external_site_is_deleted';
        /**
         * @var Nonce
         */
        protected $nonce;
        /**
         * @var Request
         */
        protected $request;
        /**
         * @var ExternalSitesRepositoryInterface
         */
        protected $externalSitesRepository;
        /**
         * @var PersistentAdminNotices
         */
        protected $notices;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices)
        {
        }
        /**
         * Handles the POST requests.
         */
        public function handlePostRequest() : void
        {
        }
        /**
         * Process the given action(insert, update, delete) with the given data items.
         *
         * @param string $action The action name, can be insert, update or delete.
         * @psalm-param Action $action
         * @param array $items The list of items.
         * @psalm-param Item[] $items
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function processAction(string $action, array $items) : void
        {
        }
        /**
         * Splits the request data into the map of appropriate action names to a list of external site items.
         *
         * @param array $externalSites The list of external site items.
         * @psalm-param Item[] $externalSites
         * @return array A map of appropriate action name to a list of external site items.
         */
        protected function splitExternalSites(array $externalSites) : array
        {
        }
        /**
         * Configures the external site's request data.
         *
         * @param array $externalSites The list of external site items.
         * @psalm-param Item[] $externalSites
         */
        protected function configureExternalSitesRequestData(array &$externalSites)
        {
        }
    }
    /**
     * @psalm-type Attributes = array{
     *      class?: string,
     *      size?: int,
     *      data-connected?: string,
     *      data-none?: string
     * }
     * @psalm-type Column = array{
     *      header: string,
     *      type: string,
     *      attributes: Attributes,
     *      options: array<string, string>
     * }
     * @psalm-type ColumnName = string
     */
    class TableFormView
    {
        /**
         * @var string
         */
        public const INPUT_NAME_PREFIX = 'externalSites';
        /**
         * @var string
         */
        public const TABLE_ID = 'mlp-external-sites-table';
        /**
         * @var ExternalSitesRepositoryInterface
         */
        protected $externalSitesRepository;
        /**
         * @var array
         */
        protected $columns;
        /**
         * @param ExternalSitesRepositoryInterface $externalSitesRepository
         * @param array $columns
         * @psalm-param array<ColumnName, Column>
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository, array $columns)
        {
        }
        /**
         * Renders the table.
         *
         * @return void
         */
        public function render() : void
        {
        }
        /**
         * The table body markup.
         *
         * @return void
         */
        protected function tBody() : void
        {
        }
        /**
         * Creates an empty row.
         *
         * @return void
         */
        protected function emptyRow() : void
        {
        }
        /**
         * The row HTML markup.
         *
         * @param int $id The row ID.
         * @param ExternalSiteInterface $externalSite
         * @return void
         */
        protected function row(int $id, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite) : void
        {
        }
        /**
         * The column HTML markup.
         *
         * @param string $col The column Name.
         * @param int $id The row ID.
         * @param array $data The column configuration data.
         * @psalm-param Column $data
         * @param scalar $value The input value.
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        protected function column(string $col, int $id, array $data, $value) : void
        {
        }
        /**
         * The input markup.
         *
         * @param int $id The row ID.
         * @param string $col The column name.
         * @param string $value The input value.
         * @param array $attributes The column attributes configuration.
         * @psalm-param Attributes $attributes
         * @return void
         */
        protected function text(int $id, string $col, string $value, array $attributes = []) : void
        {
        }
        /**
         * The checkbox markup.
         *
         * @param int $id The row ID.
         * @param string $col The column name.
         * @param bool $value The input value.
         * @param array $attributes The column attributes configuration.
         * @psalm-param Attributes $attributes
         */
        protected function checkbox(int $id, string $col, bool $value, array $attributes = []) : void
        {
        }
        /**
         * The select markup.
         *
         * @param int $id The row ID.
         * @param string $col The column name.
         * @param string $value The input value.
         * @param array<string, string> $options The select options.
         * @param array $attributes The column attributes configuration.
         * @psalm-param Attributes $attributes
         */
        protected function select(int $id, string $col, string $value, array $options, array $attributes = []) : void
        {
        }
        /**
         * Creates the input name from given row ID and column name.
         *
         * @param int $id The row ID
         * @param string $col The column name.
         * @return string The input name.
         */
        protected function inputName(int $id, string $col) : string
        {
        }
        /**
         * Creates the input ID from given row ID and column name.
         *
         * @param int $id The row ID
         * @param string $col The column name.
         * @return string The input ID.
         */
        protected function inputId(int $id, string $col) : string
        {
        }
        /**
         * The table head HTML markup.
         *
         * @return void
         */
        protected function header() : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\LanguageManager {
    /**
     * MultilingualPress Language Manager Database
     */
    class Db
    {
        /**
         * @var array
         */
        private static $dbFormat = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'];
        /**
         * @var array
         */
        private static $dbWhere = ['%d'];
        /**
         * @var int
         */
        const PAGE_SIZE = 100;
        /**
         * @var Languages
         */
        private $languages;
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var Table
         */
        private $table;
        /**
         * Db constructor.
         * @param \wpdb $wpdb
         * @param Languages $languages
         * @param Table $table
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Api\Languages $languages, \Inpsyde\MultilingualPress\Framework\Database\Table $table)
        {
        }
        /**
         * @return int
         */
        public function nextLanguageID() : int
        {
        }
        /**
         * @return Language[]
         */
        public function read() : array
        {
        }
        /**
         * @param array $items
         * @throws NonexistentTable
         * @return array
         */
        public function update(array $items) : array
        {
        }
        /**
         * @param array $items
         * @return array
         */
        public function create(array $items) : array
        {
        }
        /**
         * @param array $items
         * @return array
         */
        public function delete(array $items) : array
        {
        }
    }
    /**
     * Class LanguageInstaller
     */
    class LanguageInstaller
    {
        /**
         * @param Language $language
         * @return bool
         */
        public function install(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : bool
        {
        }
        /**
         * @param Language $language
         * @return bool
         */
        public function exists(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : bool
        {
        }
        /**
         * @param Language $language
         * @return string
         */
        private function codeLanguageFromWpOrg(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : string
        {
        }
        /**
         * @param Language $language
         * @return array
         */
        private function languageIdentifiers(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : array
        {
        }
        /**
         * Load missed functions.
         */
        private function loadFunctions()
        {
        }
    }
    /**
     * Language Manager Page View
     */
    final class PageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var TableFormView
         */
        private $table;
        /**
         * PageView constructor.
         * @param Nonce $nonce
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\LanguageManager\TableFormView $table)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
        /**
         * Render the form
         *
         * @return void
         */
        private function renderForm()
        {
        }
    }
    class RequestHandler
    {
        const ACTION = 'update_multilingualpress_languages';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var Updater
         */
        private $updater;
        /**
         * @param Updater $updater
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageManager\Updater $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest()
        {
        }
        /**
         * @param array $languages
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        private function ensureLanguagesData(array &$languages)
        {
        }
    }
    /**
     * Class ServiceProvider
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'language-manager';
        const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'language_manager_assets_factory';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         * @throws AssetException
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws AssetException
         */
        private function enqueueAssets(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param string $currentPage
         * @return bool
         */
        private function isMultilingualPressSettingsPage(string $currentPage) : bool
        {
        }
    }
    /**
     * Language Manager Table Form View
     */
    class TableFormView
    {
        /**
         * @var string
         */
        private static $languageInstallationStatus = 'language_installation_status';
        /**
         * @var string
         */
        private $name = 'languages';
        /**
         * @var string
         */
        private $id = 'mlp-language-manager-table';
        /**
         * @var Db
         */
        private $db;
        /**
         * @var LanguageInstaller
         */
        private $languageInstaller;
        /**
         * TableFormView constructor.
         * @param Db $db
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageManager\Db $db, \Inpsyde\MultilingualPress\Module\LanguageManager\LanguageInstaller $languageInstaller)
        {
        }
        /**
         * @return void
         */
        public function render()
        {
        }
        /**
         * @return void
         */
        private function tBody()
        {
        }
        /**
         * @return void
         */
        private function emptyRow()
        {
        }
        /**
         * @param int $id
         * @param Language $row
         */
        private function row(int $id, \Inpsyde\MultilingualPress\Framework\Language\Language $language)
        {
        }
        /**
         * @param string $col
         * @param int $id
         * @param array $data
         * @param mixed $content
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function column(string $col, int $id, array $data, $content)
        {
        }
        /**
         * @param int $id
         * @param string $col
         * @param $value
         * @param array $attributes
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function checkbox(int $id, string $col, $value, array $attributes = [])
        {
        }
        /**
         * @param int $id
         * @param string $col
         * @param $value
         * @param array $attributes
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function number(int $id, string $col, $value, array $attributes = [])
        {
        }
        /**
         * @param int $id
         * @param string $col
         * @param $value
         * @param array $attributes
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function text(int $id, string $col, $value, array $attributes = [])
        {
        }
        /**
         * @param int $id
         * @param string $col
         * @param $value
         * @param $attributes
         * @return array
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function prepareInputData(int $id, string $col, $value, $attributes) : array
        {
        }
        /**
         * @param int $id
         * @param string $col
         * @return string
         */
        private function inputName(int $id, string $col) : string
        {
        }
        /**
         * @param int $id
         * @param string $col
         * @return string
         */
        private function inputId(int $id, string $col) : string
        {
        }
        /**
         * @return void
         */
        private function header()
        {
        }
        /**
         * @return array
         */
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        private function columns() : array
        {
        }
        /**
         * @param Language $language
         * @return string
         */
        private function languageInstallationStatus(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : string
        {
        }
        /**
         * @param string $class
         * @return string
         */
        private function sanitizeColumnsHtmlClass(string $class) : string
        {
        }
    }
    /**
     * MultilingualPress Language Manager Updater
     */
    class Updater
    {
        /**
         * @var Db
         */
        private $storage;
        /**
         * @var Table
         */
        private $table;
        /**
         * @var LanguageInstaller
         */
        private $languageInstaller;
        /**
         * @var LanguageFactory
         */
        private $languageFactory;
        /**
         * Updater constructor.
         * @param Db $storage
         * @param Table $table
         * @param LanguageFactory $languageFactory
         * @param LanguageInstaller $languageInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageManager\Db $storage, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory $languageFactory, \Inpsyde\MultilingualPress\Module\LanguageManager\LanguageInstaller $languageInstaller)
        {
        }
        /**
         * @param array $languages
         * @return bool
         * @throws NonexistentTable
         */
        public function updateLanguages(array $languages) : bool
        {
        }
        /**
         * @param array $languages
         * @return array
         */
        public function splitLanguages(array $languages) : array
        {
        }
        /**
         * @param array $languages
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function excludeEmptyLanguageData(array &$languages)
        {
        }
        /**
         * @param array $languages
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function excludeMalformedLanguageData(array &$languages)
        {
        }
        /**
         * @param array $languages
         */
        // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
        private function ensureKeys(array &$languages)
        {
        }
        /**
         * @param array $languages
         * @return array
         */
        private function installLanguages(array $languages) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\LanguageSwitcher {
    class Item
    {
        /**
         * @var string
         */
        private $languageName;
        /**
         * @var string
         */
        private $locale;
        /**
         * @var string
         */
        private $isoCode;
        /**
         * @var string
         */
        private $flag;
        /**
         * @var string
         */
        private $url;
        /**
         * @var int
         */
        private $siteId;
        /**
         * @var string
         */
        private $hreflangDisplayCode;
        /**
         * @var string
         */
        protected $type;
        public function __construct(string $languageName, string $locale, string $isoCode, string $flag, string $url, int $siteId, string $hreflangDisplayCode, string $type = '')
        {
        }
        /**
         * @return string
         */
        public function languageName() : string
        {
        }
        /**
         * @return string
         */
        public function isoCode() : string
        {
        }
        /**
         * @return string
         */
        public function flag() : string
        {
        }
        /**
         * @return string
         */
        public function url() : string
        {
        }
        /**
         * @return int
         */
        public function siteId() : int
        {
        }
        /**
         * @return string
         */
        public function locale() : string
        {
        }
        /**
         * @return string
         */
        public function hreflangDisplayCode() : string
        {
        }
        /**
         * The item type.
         *
         * Can be used to specify the special item types like for external sites.
         *
         * @return string
         */
        public function type() : string
        {
        }
    }
    class ItemFactory
    {
        public function create(string $languageName, string $locale, string $isoCode, string $flag, string $url, int $siteId, string $hreflangDisplayCode, string $type = '') : \Inpsyde\MultilingualPress\Module\LanguageSwitcher\Item
        {
        }
    }
    class Model
    {
        /**
         * @var Translations
         */
        private $translations;
        /**
         * @var ItemFactory
         */
        private $itemFactory;
        /**
         * @var SiteSettingsRepository
         */
        protected $siteSettingsRepository;
        /**
         * Whether the ExternalSites module is active.
         *
         * @var bool
         */
        protected $isExternalSitesModuleActive;
        /**
         * @param Translations $translations
         * @param ItemFactory $itemFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $translations, \Inpsyde\MultilingualPress\Module\LanguageSwitcher\ItemFactory $itemFactory, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, bool $isExternalSitesModuleActive)
        {
        }
        /**
         * @param array $args
         * @param array $instance
         * @return array
         */
        public function data(array $args, array $instance) : array
        {
        }
        /**
         * @return Translation[]
         */
        protected function translations() : array
        {
        }
        /**
         * Returns flag image url from multilingualpress-site-flags plugin
         *
         * This is neeeded for old site-flags plugin to work.
         *
         * @param array $model
         * @param string $isoCode
         * @return string
         */
        protected function languageFlag(array $model, string $isoCode) : string
        {
        }
        public function hreflangDisplayCode($siteId) : string
        {
        }
    }
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'language-switcher';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
    class View
    {
        public const FILTER_ITEM_LANGUAGE_NAME = 'multilingualpress.language_switcher_item_language_name';
        public const FILTER_LANGUAGE_SWITCHER_ITEM_FLAG_URL = 'multilingualpress.languageSwitcher.ItemFlagUrl';
        public const FILTER_LANGUAGE_SWITCHER_ITEMS = 'multilingualpress.languageSwitcher.Items';
        public const FILTER_SHOULD_PRESERVE_LANGUAGE_SWITCHER_ITEM_URL_PARAMS = 'multilingualpress.languageSwitcher.should_preserve_url_params';
        /**
         * Displays widget view in frontend
         *
         * @param array $model
         * @return void
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function render(array $model)
        {
        }
        /**
         * Creates the widget title markup
         *
         * @param string $beforeTitle
         * @param string $title
         * @param string $afterTitle
         * @return string Tittle markup
         */
        protected function title(string $beforeTitle, string $title, string $afterTitle) : string
        {
        }
        /**
         * retrieve an array of item classes
         *
         * @param int $siteId
         * @return array of classes
         */
        protected function itemClass(int $siteId) : array
        {
        }
    }
    class Widget extends \WP_Widget
    {
        /**
         * @var Model
         */
        private $model;
        /**
         * @var View
         */
        private $view;
        /**
         * @var ModuleManager
         */
        private $moduleManager;
        /**
         * Whether the ExternalSites module is active.
         *
         * @var bool
         */
        protected $isExternalSitesModuleActive;
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageSwitcher\Model $model, \Inpsyde\MultilingualPress\Module\LanguageSwitcher\View $view, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, bool $isExternalSitesModuleActive)
        {
        }
        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function widget($args, $instance)
        {
        }
        /**
         * Outputs the options form on admin
         *
         * @param array $instance
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function form($instance)
        {
        }
        /**
         * Processing widget options on save
         *
         * @param array $newInstance
         * @param array $oldInstance
         * @return array|void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function update($newInstance, $oldInstance)
        {
        }
        /**
         * Whether to show the site flags option
         *
         * The "Show Flags" option should be shown if the old version of Site Flags addon is active or
         * if the new Site Flags module is enabled
         *
         * @return bool
         */
        protected function isShowFlagOption() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\OriginalTranslationLanguage\Integrations\QuickLinks {
    class QuickLinksIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var ViewModel
         */
        protected $originalLanguageViewModel;
        /**
         * @var ContentRelationshipMetaInterface
         */
        protected $contentRelationshipMeta;
        /**
         * @var string
         */
        protected $originalKeyword;
        /**
         * @var bool
         */
        protected $quickLinkSettingOptionValue;
        public function __construct(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel $originalLanguageViewModel, \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface $contentRelationshipMeta, string $originalKeyword, bool $quickLinkSettingOptionValue)
        {
        }
        /**
         * @inheritDoc
         */
        public function integrate() : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model {
    /**
     * Interface ViewModel
     * @package Inpsyde\MultilingualPress\Core\Setting
     */
    interface ViewModel
    {
        /**
         * The ID of the Model
         *
         * @return string
         */
        public function id() : string;
        /**
         * Print the Title for the Setting
         *
         * @return void
         */
        public function title() : void;
        /**
         * Print the Settings
         *
         * @return void
         */
        public function render() : void;
    }
}
namespace Inpsyde\MultilingualPress\Module\OriginalTranslationLanguage\Integrations\QuickLinks {
    class QuickLinksOriginalLanguageViewModel implements \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel
    {
        /**
         * @var string
         */
        protected $modelName;
        /**
         * @var string
         */
        protected $quickLinksModuleSettingsName;
        /**
         * @var string
         */
        protected $description;
        /**
         * @var bool
         */
        protected $value;
        public function __construct(string $modelName, string $quickLinksModuleSettingsName, string $description, bool $value)
        {
        }
        /**
         * @inheritDoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function title() : void
        {
        }
        /**
         * @inheritDoc
         */
        public function render() : void
        {
        }
        /**
         * Returns the original language setting name.
         *
         * @return string
         */
        protected function originalLanguageSettingId() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\OriginalTranslationLanguage {
    /**
     * @psalm-type relatedSites = array{id: int, name: string}
     */
    class MetaboxRenderer implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\PostMetaboxRendererInterface
    {
        /**
         * @var array<int, string> The list of related sites.
         * @psalm-var array<relatedSites>
         */
        protected $relatedSites;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var string
         */
        protected $relationshipMetaName;
        /**
         * @var ContentRelationshipMetaInterface
         */
        protected $contentRelationshipMeta;
        public function __construct(array $relatedSites, string $label, string $relationshipMetaName, \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface $contentRelationshipMeta)
        {
        }
        /**
         * @inheritDoc
         */
        public function render(int $postId) : void
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'original_translation_language';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : void
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model {
    /**
     * Class ModelCollectionValidator
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    trait ModelCollectionValidator
    {
        /**
         * Check that all of the items withing the give argument are instances of
         * ModelInterface
         *
         * @param array $models
         * @return bool
         */
        protected function validate(array $models) : bool
        {
        }
    }
    /**
     * Class Collection
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class Collection implements \IteratorAggregate, \Countable
    {
        use \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ModelCollectionValidator;
        /**
         * @var ModelInterface[]
         */
        private $collection;
        /**
         * Collection constructor.
         * @param array $models
         * @throws InvalidArgumentException
         */
        public function __construct(array $models)
        {
        }
        /**
         * @inheritDoc
         */
        #[\ReturnTypeWillChange]
        public function getIterator()
        {
        }
        /**
         * @inheritDoc
         */
        #[\ReturnTypeWillChange]
        public function count()
        {
        }
    }
    /**
     * Class CollectionFactory
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class CollectionFactory
    {
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @var Translations
         */
        private $translations;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Api\Translations $translations)
        {
        }
        /**
         * Create the Model Collection
         *
         * All of the models within the collection are related with the given site and content id.
         *
         * @param int $sourceSiteId
         * @param int $sourceContentId
         * @return Collection
         * @throws InvalidArgumentException
         */
        public function create(int $sourceSiteId, int $sourceContentId) : \Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection
        {
        }
        /**
         * Build the Collection of Models by the Given Content Relations
         *
         * Content Relations is an array where the keys are the site id and the value the content id.
         *
         * @param array $contentRelations
         * @return Collection
         * @throws InvalidArgumentException
         */
        protected function buildModelCollectionByContentRelations(array $contentRelations) : \Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection
        {
        }
        /**
         * Create the Single Model
         *
         * The returned value is an array like this one
         *
         * ```
         * [
         *     'url' => URL OF THE TARGET POST,
         *     'language' => HTTP LANGUAGE CODE OF THE TARGET SITE
         *     'label' => THE TEXT TO USE AS LABEL FOR THE ITEM
         * ]
         * ```
         *
         * @param int $remoteSiteId
         * @param int $remoteContentId
         * @return ModelInterface
         * @throws InvalidArgumentException
         * @throws NonexistentTable
         */
        protected function singleModel(int $remoteSiteId, int $remoteContentId) : \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ModelInterface
        {
        }
        /**
         * Gets the hreflang display code of the given site.
         *
         * @param int $siteId The site ID.
         * @return string The hreflang display code
         */
        protected function hreflangDisplayCode(int $siteId) : string
        {
        }
        /**
         * Create a NetworkState Instance
         *
         * Basically a wrapper for a static constructor that's difficult to mock in unit tests.
         *
         * @return NetworkState
         */
        protected function networkState() : \Inpsyde\MultilingualPress\Framework\NetworkState
        {
        }
        /**
         * Get the translations for remote content
         *
         * @param int $remoteContentId
         * @return Translations[]
         */
        protected function translations(int $remoteContentId) : array
        {
        }
        /**
         * Creates Bcp47Tag for given site ID.
         *
         * @param int $siteId The site ID.
         * @return Bcp47Tag The Bcp47Tag tag
         */
        protected function createBcp47Tag(int $siteId) : \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag
        {
        }
    }
    /**
     * Interface ModelInterface
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    interface ModelInterface
    {
        /**
         * Return the Url
         *
         * @return Url
         */
        public function url() : \Inpsyde\MultilingualPress\Framework\Url\Url;
        /**
         * Return the Language HTTP Code
         *
         * @return Bcp47Tag
         */
        public function language() : \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag;
        /**
         * Return a Text Label
         *
         * @return string
         */
        public function label() : string;
    }
    /**
     * Class Model
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class Model implements \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ModelInterface
    {
        /**
         * @var string
         */
        private $url;
        /**
         * @var string
         */
        private $language;
        /**
         * @var string
         */
        private $label;
        private $hreflangDisplayCode;
        /**
         * Model constructor.
         * @param Url $url
         * @param Bcp47Tag $language
         * @param string $label
         * @param string $hreflangDisplayCode
         * @throws InvalidArgumentException
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Url\Url $url, \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag $language, string $label, string $hreflangDisplayCode)
        {
        }
        /**
         * @inheritDoc
         */
        public function url() : \Inpsyde\MultilingualPress\Framework\Url\Url
        {
        }
        /**
         * @inheritDoc
         */
        public function language() : \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag
        {
        }
        /**
         * @inheritDoc
         */
        public function label() : string
        {
        }
        public function hreflangDisplayCode() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks {
    /**
     * @psalm-type siteId = int
     * @psalm-type siteName = string
     * @psalm-type relatedSites = array<siteId, siteName>
     */
    class QuickLink
    {
        public const FILTER_NOFOLLOW_ATTRIBUTE = 'multilingualpress.quicklinks_nofollow';
        public const FILTER_RENDER_AS_SELECT = 'multilingualpress.QuickLinks.RenderAsSelect';
        public const FILTER_QUICKLINK_LABEL = 'multilingualpress.QuickLinks.Label';
        public const FILTER_MODEL_COLLECTION = 'multilingualpress.QuickLinks.ModelCollection';
        /**
         * @var CollectionFactory
         */
        private $collectionFactory;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Repository
         */
        private $settingRepository;
        /**
         * @var array<int, string>
         * @psalm-var relatedSites
         */
        protected $relatedSites;
        /**
         * QuickLink constructor.
         * @param CollectionFactory $collectionFactory
         * @param Nonce $nonce
         * @param Repository $settingRepository
         * @param array<int, string> $relatedSites A map of the related site site IDs to site names.
         * @psalm-param relatedSites $relatedSites
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\CollectionFactory $collectionFactory, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository $settingRepository, array $relatedSites)
        {
        }
        /**
         * Filter the Post Content
         *
         * Include the Quick Links in the content output
         *
         * @param string $theContent
         * @return string
         */
        public function filter(string $theContent) : string
        {
        }
        /**
         * Render
         *
         * @param string $position
         * @param Collection $modelCollection
         */
        protected function render(string $position, \Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $modelCollection)
        {
        }
        /**
         * Render the collection.
         *
         * @param Collection $collection
         */
        protected function renderCollection(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $collection) : void
        {
        }
        /**
         * Render the Quick Links as a List of Links
         *
         * @param Collection $modelCollection
         */
        protected function renderAsLinkList(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $modelCollection)
        {
        }
        /**
         * Render the Quick Links as a Select/Dropdown Element
         *
         * @param Collection $modelCollection
         */
        protected function renderAsSelect(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $modelCollection)
        {
        }
    }
    /**
     * Class Redirector
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class Redirector
    {
        const REDIRECT_VALUE_KEY = 'mlp_quicklinks_redirect_selection';
        const ACTION_BEFORE_VALIDATE_REDIRECT = 'multilingualpress.before_validate_redirect';
        const ACTION_AFTER_VALIDATE_REDIRECT = 'multilingualpress.after_validate_redirect';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * Redirector constructor.
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Take Redirect Action
         *
         * @return void
         */
        public function redirect()
        {
        }
    }
    /**
     * Class ServiceProvider
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'quick_links';
        const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'quicklinks_assets_factory';
        /**
         * @inheritDoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritDoc
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritDoc
         * @throws AssetException
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Activate Module for Admin
         *
         * @param Container $container
         * @throws AssetException
         */
        protected function activateModuleForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Register and Enqueue Scripts for Admin
         *
         * @param Container $container
         * @throws AssetException
         */
        protected function setupScriptsForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Activate module for Frontend
         *
         * @param Container $container
         * @throws AssetException
         */
        protected function activateModuleForFrontend(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Register and Enqueue Scripts for Frontend
         *
         * @param Container $container
         * @throws AssetException
         */
        protected function setupScriptsForFrontend(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Setup ValidateRedirectFilter
         */
        protected function setupValidateRedirectFilter(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks\Settings {
    /**
     * Class QuickLinksPositionViewModel
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
     */
    class QuickLinksPositionViewModel implements \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel
    {
        const ID = 'position';
        /**
         * @var Repository
         */
        private $repository;
        /**
         * QuickLinksPositionViewModel constructor.
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function title() : void
        {
        }
        /**
         * @inheritDoc
         */
        public function render() : void
        {
        }
    }
    /**
     * Class Repository
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class Repository
    {
        public const MODULE_SETTINGS = 'multilingualpress_module_quicklinks_settings';
        public const MODULE_SETTING_QUICKLINKS_POSITION = 'position';
        /**
         * Retrieve the value for the given Quick Links setting name.
         *
         * @param string $settingName The setting name.
         * @return string The setting value.
         */
        public function settingValue(string $settingName) : string
        {
        }
        /**
         * Retrieve the Module Settings
         *
         * @return array
         */
        protected function moduleSettings() : array
        {
        }
        /**
         * Update the Given Module Settings
         *
         * @param array $options
         * @return void
         */
        public function updateModuleSettings(array $options) : void
        {
        }
        /**
         * Gets the default setting value by given setting name.
         *
         * @param string $settingName The setting name.
         * @return string The default setting value.
         */
        protected function defaultValueForSetting(string $settingName) : string
        {
        }
    }
    /**
     * Class TabView
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
     */
    class TabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        const FILTER_VIEW_MODELS = 'multilingualpress.quicklinks_module_setting_models';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var ViewModel
         */
        private $viewModels;
        /**
         * ModuleSettingsTabView constructor
         *
         * @param Nonce $nonce
         * @param ViewModel[] $viewModels
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel ...$viewModels)
        {
        }
        /**
         * Render the Settings Tab Content
         *
         * @inheritDoc
         */
        public function render()
        {
        }
        /**
         * Retrieve the Models
         *
         * @return ViewModel[]
         */
        protected function viewModels() : array
        {
        }
        /**
         * Validate View Model by Type Hint all of the models of the given collection
         *
         * @param array $models
         * @return array
         */
        protected function validateViewModels(array $models) : array
        {
        }
    }
    /**
     * Class Updater
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
     */
    class Updater
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Repository
         */
        private $repository;
        /**
         * Updater constructor
         *
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository $repository)
        {
        }
        /**
         * Update Module Redirect Settings
         *
         * @param Request $request
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks {
    /**
     * Class ValidateRedirectFilter
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class ValidateRedirectFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        /**
         * @var wpdb
         */
        private $wpdb;
        /**
         * ValidateRedirectFilter constructor.
         * @param wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Enable the filter
         *
         * @return void
         */
        public function enableExtendsAllowedHosts()
        {
        }
        /**
         * Disable the filter
         *
         * @return bool
         */
        public function disable() : bool
        {
        }
        /**
         * Filter
         *
         * @param array $homeHosts
         * @param $remoteHosts
         * @return array
         */
        public function extendsAllowedHosts(array $homeHosts, string $remoteHosts) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect {
    /**
     * Parser for Accept-Language headers, sorting by priority.
     */
    class AcceptLanguageParser
    {
        /**
         * Parses the given Accept header and returns the according data in array form and returns
         * an array with language codes as keys, and priorities as values.
         *
         * @param string $header
         * @return float[]
         */
        public function parseHeader(string $header) : array
        {
        }
        /**
         * Returns the given Accept header without comment.
         *
         * A comment starts with a `(` and ends with the first `)`.
         *
         * @param string $header
         * @return string
         */
        private function removeHeaderComment(string $header) : string
        {
        }
        /**
         * Returns the array with the individual values of the given Accept header.
         *
         * @param string $headerString
         * @return string[]
         */
        private function headerValues(string $headerString) : array
        {
        }
        /**
         * Returns the array with the language and priority of the given value, and an empty array for
         * an invalid language.
         *
         * @param string $value
         * @return array
         */
        private function splitValue(string $value) : array
        {
        }
    }
    /**
     * Interface for all redirector implementations.
     */
    interface Redirector
    {
        const FILTER_REDIRECTOR_TYPE = 'multilingualpress.redirector_type';
        const ACTION_TARGET_NOT_FOUND = 'multilingualpress.redirect_target_not_found';
        const TYPE_JAVASCRIPT = 'JAVASCRIPT';
        const TYPE_PHP = 'PHP';
        /**
         * Redirects the user to the best-matching language version, if any.
         *
         * @return void
         */
        public function redirect();
    }
    /**
     * Class JsRedirector
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    final class JsRedirector implements \Inpsyde\MultilingualPress\Module\Redirect\Redirector
    {
        const FILTER_UPDATE_INTERVAL = 'multilingualpress.noredirect_update_interval';
        const SCRIPT_HANDLE = 'multilingualpress-redirect';
        /**
         * @var AssetManager
         */
        private $assetManager;
        /**
         * @var LanguageUrlDictionaryFactory
         */
        private $languageUrlDictionaryFactory;
        /**
         * @var Repository
         */
        private $redirectSettingsRepository;
        /**
         * @param LanguageUrlDictionaryFactory $languageUrlDictionaryFactory
         * @param AssetManager $assetManager
         * @param Repository $redirectSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\LanguageUrlDictionaryFactory $languageUrlDictionaryFactory, \Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $redirectSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         * @throws AssetException
         * @throws NonexistentTable
         */
        public function redirect()
        {
        }
        /**
         * @return string
         */
        private function getRedirectFallbackSiteLanguageTag() : string
        {
        }
    }
    /**
     * @psalm-type languageCode = string
     */
    class LanguageNegotiator
    {
        const FILTER_REDIRECT_URL = 'multilingualpress.redirect_url';
        const FILTER_POST_STATUS = 'multilingualpress.redirect_post_status';
        const FILTER_PRIORITY_FACTOR = 'multilingualpress.language_only_priority_factor';
        const FILTER_REDIRECT_TARGETS = 'multilingualpress.redirect_targets';
        /**
         * @var float
         */
        private $languageOnlyPriorityFactor;
        /**
         * @var Translations
         */
        private $translations;
        /**
         * @var Repository
         */
        private $repository;
        /**
         * A map of language codes to priorities.
         *
         * @var array<string, float>
         * @psalm-var array<languageCode, float>
         */
        protected $userLanguages;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $translations, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $repository, array $userLanguages)
        {
        }
        /**
         * Returns the redirect target data object for the best-matching language version.
         *
         * @param TranslationSearchArgs|null $args
         * @return RedirectTarget
         */
        public function redirectTarget(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args = null) : \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget
        {
        }
        /**
         * Returns the redirect target data objects for all available language versions.
         *
         * @param TranslationSearchArgs|null $args
         * @return RedirectTarget[]
         */
        public function redirectTargets(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args = null) : array
        {
        }
        /**
         * @param RedirectTarget[] $targets
         * @param Translation[] $translations
         * @return RedirectTarget[]
         */
        private function orderTargets(array $targets, array $translations) : array
        {
        }
        /**
         * Returns all translations according to the given arguments.
         *
         * @param TranslationSearchArgs|null $args
         * @return Translation[]
         */
        private function searchTranslations(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args = null) : array
        {
        }
        /**
         * Returns the priority of the given language.
         *
         * @param string $languageTag The language tag.
         * @return float The priority.
         */
        public function languagePriority(string $languageTag) : float
        {
        }
        /**
         * The Method will get the language tag
         * It will also fix the language tags for language variants
         * and will remove the third part from language ta so de-DE-formal will become de-DE
         *
         * @param Language $language The language Object
         * @return string The language bcp47 tag
         */
        private function languageTag(\Inpsyde\MultilingualPress\Framework\Language\Language $language) : string
        {
        }
        /**
         * Calculate the redirect language fallback priority
         *
         * @param int $siteId
         * @return float The redirect language fallback priority
         */
        protected function languageFallbackPriority(int $siteId) : float
        {
        }
    }
    /**
     * Class LanguageUrlDictionaryFactory
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class LanguageUrlDictionaryFactory
    {
        /**
         * @var LanguageNegotiator
         */
        private $languageNegotiator;
        /**
         * @var TranslationSearchArgs
         */
        private $translationSearchArgs;
        /**
         * RedirectLanguageUrlDictionary constructor.
         * @param TranslationSearchArgs $translationSearchArgs
         * @param LanguageNegotiator $languageNegotiator
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $translationSearchArgs, \Inpsyde\MultilingualPress\Module\Redirect\LanguageNegotiator $languageNegotiator)
        {
        }
        /**
         * Language Url Dictionary
         *
         * @return array
         */
        public function create() : array
        {
        }
    }
    /**
     * Interface for all noredirect storage implementations.
     */
    interface NoRedirectStorage
    {
        const FILTER_LIFETIME = 'multilingualpress.noredirect_storage_lifetime';
        const LIFETIME_IN_SECONDS = 5 * MINUTE_IN_SECONDS;
        const KEY = 'noredirect';
        /**
         * Adds the given language to the storage.
         *
         * Returns false if language is not actually added, e.g it was already added.
         *
         * @param string $language
         * @return bool
         */
        public function addLanguage(string $language) : bool;
        /**
         * Checks if the given language has been stored before.
         *
         * @param string $language
         * @return bool
         */
        public function hasLanguage(string $language) : bool;
    }
    /**
     * Object-cache-based noredirect storage implementation.
     *
     * Only used for logged-in users, so they do not mutually affect each other.
     */
    final class NoRedirectObjectCacheStorage implements \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage
    {
        /**
         * @var string
         */
        private $key;
        /**
         * @inheritdoc
         */
        public function addLanguage(string $language) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function hasLanguage(string $language) : bool
        {
        }
        /**
         * Returns the currently stored languages.
         *
         * @return string[]
         */
        private function storedLanguages() : array
        {
        }
        /**
         * Returns the cache key for the current user.
         *
         * @return string
         */
        private function key() : string
        {
        }
    }
    /**
     * Session-based noredirect storage implementation, used when no user is logged or no persistent
     * object cache is in use.
     *
     * phpcs:disable WordPress.VIP.SessionVariableUsage
     * phpcs:disable WordPress.VIP.SessionFunctionsUsage
     */
    final class NoRedirectSessionStorage implements \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage
    {
        /**
         * @inheritdoc
         */
        public function addLanguage(string $language) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function hasLanguage(string $language) : bool
        {
        }
        /**
         * Ensures a session.
         */
        private function ensureSession()
        {
        }
    }
    /**
     * Permalink filter adding the noredirect query argument.
     */
    final class NoredirectPermalinkFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        const QUERY_ARGUMENT = 'noredirect';
        /**
         * @var string[]
         */
        private $languages;
        /**
         * @param int $priority
         */
        public function __construct(int $priority = self::DEFAULT_PRIORITY)
        {
        }
        /**
         * Adds the no-redirect query argument to the permalink, if applicable.
         *
         * @param string $url
         * @param int $siteId
         * @return string
         */
        public function addNoRedirectQueryArgument(string $url, int $siteId) : string
        {
        }
        /**
         * Removes the noredirect query argument from the given URL.
         *
         * @param string $url
         * @return string
         */
        public function removeNoRedirectQueryArgument(string $url) : string
        {
        }
        /**
         * Returns the individual MultilingualPress language code of all (related)
         * sites with site IDs as keys and the individual MultilingualPress language
         * code as values.
         *
         * @return string[]
         */
        private function languages() : array
        {
        }
    }
    /**
     * Class NotFoundSiteRedirect
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class NotFoundSiteRedirect implements \Inpsyde\MultilingualPress\Module\Redirect\Redirector
    {
        /**
         * @var Repository
         */
        private $redirectSettingsRepository;
        /**
         * @var NoRedirectStorage
         */
        private $noRedirectStorage;
        /**
         * NotFoundSiteRedirect constructor.
         * @param Repository $redirectSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $redirectSettingsRepository, \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage $noRedirectStorage)
        {
        }
        /**
         * @inheritDoc
         */
        public function redirect() : bool
        {
        }
        /**
         * Retrieve the Site Url Where Redirect the User
         *
         * @param int $siteId
         * @return string
         * @throws NonexistentTable
         */
        protected function redirectUrlForSite(int $siteId) : string
        {
        }
        /**
         * Do the Redirect and Stop the Execution
         *
         * @param string $url
         */
        protected function redirectToUrl(string $url)
        {
        }
    }
    /**
     * Class PhpRedirector
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class PhpRedirector implements \Inpsyde\MultilingualPress\Module\Redirect\Redirector
    {
        /**
         * @var LanguageNegotiator
         */
        private $languageNegotiator;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var NoRedirectStorage
         */
        private $noRedirectStorage;
        /**
         * @var AcceptLanguageParser
         */
        private $acceptLanguageParser;
        /**
         * @param LanguageNegotiator $languageNegotiator
         * @param NoRedirectStorage $noRedirectStorage
         * @param Request $request
         * @param AcceptLanguageParser $acceptLanguageParser
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\LanguageNegotiator $languageNegotiator, \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage $noRedirectStorage, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\Redirect\AcceptLanguageParser $acceptLanguageParser)
        {
        }
        /**
         * @inheritdoc
         */
        public function redirect()
        {
        }
        /**
         * Check if the Request language coming from 'Accept-Language' header
         * is the same as the current site language
         *
         * @return bool
         */
        protected function requestLanguageIsSameAsCurrentSiteLanguage() : bool
        {
        }
    }
    /**
     * Request validator to be used for (potential) redirect requests.
     */
    class RedirectRequestChecker
    {
        const FILTER_REDIRECT = 'multilingualpress.do_redirect';
        /**
         * @var NoRedirectStorage
         */
        private $noRedirectStorage;
        /**
         * @var Repository
         */
        private $settingsRepository;
        /**
         * @param Repository $settingsRepository
         * @param NoRedirectStorage $redirectStorage
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $settingsRepository, \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage $redirectStorage)
        {
        }
        /**
         * @return bool
         */
        public function isRedirectRequest() : bool
        {
        }
    }
    /**
     * Redirect site setting.
     */
    class RedirectSiteSettings implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var array<SettingOptionInterface>
         */
        private $options;
        /**
         * @var Repository
         */
        private $repository;
        /**
         * @param array<SettingOptionInterface> $options
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(array $options, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * @method int contentId()
     * @method string language()
     * @method int priority()
     * @method int siteId()
     * @method string url()
     * @method float userPriority()
     * @method float languageFallbackPriority()
     */
    class RedirectTarget
    {
        const KEY_CONTENT_ID = 'contentId';
        const KEY_LANGUAGE = 'language';
        const KEY_PRIORITY = 'priority';
        const KEY_SITE_ID = 'siteId';
        const KEY_URL = 'url';
        const KEY_USER_PRIORITY = 'userPriority';
        const KEY_LANGUAGE_FALLBACK_PRIORITY = 'languageFallbackPriority';
        const DEFAULTS = [self::KEY_CONTENT_ID => 0, self::KEY_LANGUAGE => '', self::KEY_PRIORITY => 0, self::KEY_SITE_ID => 0, self::KEY_URL => '', self::KEY_USER_PRIORITY => 0.0, self::KEY_LANGUAGE_FALLBACK_PRIORITY => 0.0];
        /**
         * @var array
         */
        private $data;
        /**
         * @param array $data
         */
        public function __construct(array $data = [])
        {
        }
        /**
         * @param string $name
         * @param array $args
         * @return int|string|float
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function __call(string $name, array $args = [])
        {
        }
    }
    /**
     * Redirect user setting.
     */
    final class RedirectUserSetting implements \Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel
    {
        /**
         * @var string
         */
        private $userMetaKey;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Repository
         */
        private $repository;
        /**
         * @param string $userMetaKey
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(string $userMetaKey, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\WP_User $user)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * @psalm-type languageCode = string
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'redirect';
        const SETTING_NONCE_ACTION = 'multilingualpress_save_redirect_setting_nonce_';
        const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'redirect_assets_factory';
        /**
         * @inheritdoc
         *
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerRedirector(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerSettings(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         * @param Container $container
         * @throws \Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Performs various admin-specific tasks on module activation.
         *
         * @param Container $container
         */
        private function activateModuleForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Performs various admin-specific tasks on module activation.
         *
         * @param Container $container
         * @param SiteSetting $setting
         */
        private function activateModuleForNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSetting $setting)
        {
        }
        /**
         * Performs various admin-specific tasks on module activation.
         *
         * @param Container $container
         * @throws \Throwable
         */
        private function activateModuleForFrontend(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Settings {
    /**
     * Class RedirectFallbackViewModel
     * @package Inpsyde\MultilingualPress\Module\Redirect\Settings
     */
    class RedirectFallbackViewRenderer implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\ViewRenderer
    {
        /**
         * @var Repository
         */
        private $repository;
        /**
         * RedirectFallbackViewModel constructor.
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function title()
        {
        }
        /**
         * @inheritDoc
         */
        public function content()
        {
        }
        /**
         * Render the Options List of Sites that can be selected
         *
         * @param WP_Site[] $sites
         * @param int $selected
         */
        protected function renderOptionsForSites(array $sites, int $selected)
        {
        }
        /**
         * Render a Single Option
         *
         * @param WP_Site $site
         * @param int $selected
         */
        protected function renderOption(\WP_Site $site, int $selected)
        {
        }
        /**
         * Retrieve the Existing Sites
         *
         * @return array
         */
        protected function sites() : array
        {
        }
    }
    /**
     * Class SettingsRepository
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class Repository
    {
        const META_KEY_USER = 'multilingualpress_redirect';
        const OPTION_SITE = 'multilingualpress_module_redirect';
        const OPTION_SITE_ENABLE_REDIRECT = 'option_site_enable_redirect';
        const OPTION_SITE_ENABLE_REDIRECT_FALLBACK = 'option_site_enable_redirect_fallback';
        const MODULE_SETTINGS = 'multilingualpress_module_redirect_settings';
        const MODULE_SETTING_FALLBACK_REDIRECT_SITE_ID = 'fallback_site_id';
        public const MODULE_SETTING_FALLBACK_REDIRECT_EXTERNAL_SITE_ID = 'fallback_external_site_id';
        /**
         * Is the Redirect enabled for the given site?
         *
         * @param int $siteId
         * @param string $setting
         * @return bool
         */
        public function isRedirectSettingEnabledForSite(int $siteId = 0, string $setting = self::OPTION_SITE_ENABLE_REDIRECT) : bool
        {
        }
        /**
         * Is the Redirect enabled for the given user?
         *
         * @param int $userId
         * @return bool
         */
        public function isRedirectEnabledForUser(int $userId = 0) : bool
        {
        }
        /**
         * Retrieve the redirect site id for fallback
         *
         * @return int
         */
        public function redirectFallbackSiteId() : int
        {
        }
        /**
         * Retrieve the redirect external site id for fallback
         *
         * @return int
         */
        public function redirectFallbackExternalSiteId() : int
        {
        }
        /**
         * Retrieve the Module Settings
         *
         * @return array
         */
        protected function moduleSettings() : array
        {
        }
        /**
         * Update the Given Module Settings
         *
         * @param array $options
         * @return void
         */
        public function updateModuleSettings(array $options)
        {
        }
    }
    /**
     * Class ModuleSettingsTabView
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class TabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public const FILTER_VIEW_MODELS = 'multilingualpress.redirect_module_setting_models';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var ViewRenderer
         */
        private $viewRenderers;
        /**
         * ModuleSettingsTabView constructor
         *
         * @param Nonce $nonce
         * @param ViewRenderer[] $viewRenderer
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\ViewRenderer ...$viewRenderer)
        {
        }
        /**
         * Render the Settings Tab Content
         *
         * @inheritDoc
         */
        public function render()
        {
        }
        /**
         * Retrieve the Models
         *
         * @return ViewRenderer[]
         */
        protected function viewRenderers() : array
        {
        }
        /**
         * Validate View Model by Type Hint all of the models of the given collection
         *
         * @param array $models
         * @return array
         */
        protected function validateViewRenderers(array $models) : array
        {
        }
    }
    /**
     * Class Updater
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class Updater
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Repository
         */
        private $repository;
        /**
         * Updater constructor
         *
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository $repository)
        {
        }
        /**
         * Update Module Redirect Settings
         *
         * @param Request $request
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Trasher {
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const NONCE_ACTION = 'save_trasher_setting';
        const MODULE_ID = 'trasher';
        const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'trasher_asset_factory';
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param string $postType
         * @param TrasherSettingUpdater $trasherSettingUpdater
         * @return void
         */
        private function addRestInsertAction(string $postType, \Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingUpdater $trasherSettingUpdater)
        {
        }
    }
    class Trasher
    {
        /**
         * @var ActivePostTypes
         */
        private $activePostTypes;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var TrasherSettingRepository
         */
        private $settingRepository;
        /**
         * @param TrasherSettingRepository $settingRepository
         * @param ContentRelations $contentRelations
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingRepository $settingRepository, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * Trashes all related posts.
         *
         * @param int $postId
         * @return int
         */
        public function trashRelatedPosts(int $postId) : int
        {
        }
    }
    final class TrasherSettingRepository
    {
        const META_KEY = '_trash_the_other_posts';
        /**
         * Returns the trasher setting value for the post with the given ID, or the current post.
         *
         * @param int $postId
         * @return bool
         */
        public function settingForPost(int $postId = 0) : bool
        {
        }
        /**
         * Updates the trasher setting value for the post with the given ID.
         *
         * @param int $postId
         * @param bool $value
         * @return bool
         */
        public function updateSetting(int $postId, bool $value) : bool
        {
        }
    }
    /**
     * Trasher setting updater.
     */
    class TrasherSettingUpdater
    {
        /**
         * @var ActivePostTypes
         */
        private $activePostTypes;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var TrasherSettingRepository
         */
        private $settingRepository;
        /**
         * @param TrasherSettingRepository $settingRepository
         * @param ContentRelations $contentRelations
         * @param Request $request
         * @param Nonce $nonce
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingRepository $settingRepository, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * Updates the trasher setting of the post with the given ID as well as all related posts.
         *
         * @param int $postId
         * @param \WP_Post $post
         * @return int
         */
        public function update(int $postId, \WP_Post $post) : int
        {
        }
        /**
         * @param \WP_Post $post
         * @param \WP_REST_Request $request
         * @return int
         */
        public function updateFromRestApi(\WP_Post $post, \WP_REST_Request $request) : int
        {
        }
        /**
         * @param int $postId
         * @param bool $value
         * @return int
         */
        private function updateSetting(int $postId, bool $value) : int
        {
        }
    }
    /**
     * Trasher setting view.
     */
    class TrasherSettingView
    {
        /**
         * @var ActivePostTypes
         */
        private $activePostTypes;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var TrasherSettingRepository
         */
        private $settingRepository;
        /**
         * @param TrasherSettingRepository $settingRepository
         * @param Nonce $nonce
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingRepository $settingRepository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * Renders the setting markup.
         *
         * @param \WP_Post $post
         */
        public function render(\WP_Post $post)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\User {
    class MetaValueFilter
    {
        /**
         * Filter the frontend values for user meta fields and replace with correct translations
         *
         * @param string $authorMeta The value of the metadata.
         * @param mixed $userId The user ID.
         * @return string user meta value replaced with correct translation
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function filterMetaValues(string $authorMeta, $userId) : string
        {
        }
    }
    /**
     * Module service provider.
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'user';
        /**
         * @inheritdoc
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @throws Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Will bind the styles for translation metboxes
         *
         * @param AssetManager $assetManager
         */
        protected function renderAssets(\Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager)
        {
        }
        /**
         * Render MultilingualPress custom metaboxes on user profile pages
         *
         * @param Container $container
         * @throws Throwable
         */
        protected function metaboxViewActions(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * When the user profile page is updated we need to save our custom translation meta
         *
         * @param Container $container
         * @throws Throwable
         */
        protected function metaboxUpdateActions(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Filter the frontend values for user meta fields and replace with correct translations
         *
         * @param Container $container
         * @throws Throwable
         */
        protected function filterUserMetaValues(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\User\TranslationUi\Field {
    class Biography
    {
        /**
         * @var string
         */
        private $key;
        /**
         * Biography field constructor.
         * @param string $key The meta key of the biography field
         */
        public function __construct(string $key)
        {
        }
        /**
         * Will render User Biography translation field
         *
         * @param int $userId The user id which is currently in edit
         * @param int $siteId The site id
         * @param MetaboxFieldsHelper $helper
         */
        public function render(int $userId, int $siteId, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\User\TranslationUi {
    class MetaboxAction
    {
        const NAME_PREFIX = 'multilingualpress';
        const TRANSLATION_META = 'multilingualpress_translation_meta';
        /**
         * Will handle the user profile field translations update
         *
         * @param int $userId The user id which is currently in edit
         * @param ServerRequest $request
         */
        public function updateTranslationData(int $userId, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
    }
    class MetaboxFields
    {
        const FIELD_BIOGRAPHY = 'description';
        public const TRANSLATABLE_USER_META_FIELDS = 'multilingualpress.translatable_user_meta_fields';
        /**
         * Will return array of all user translatable fields
         *
         * @return array of All user translatable fields
         */
        public function allFields() : array
        {
        }
    }
    class MetaboxView
    {
        /**
         * @var MetaboxFields
         */
        private $fields;
        /**
         * @param  MetaboxFields  $fields
         * @param  string[]  $assignedLanguageNames
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxFields $fields)
        {
        }
        /**
         * Will render user profile translation settings, which includes
         * The section title for MultilingualPress settings and translatable fields
         * for each site in tabs
         *
         * @param WP_User $user The user which is currently in edit
         * @throws NonexistentTable
         */
        public function render(\WP_User $user)
        {
        }
        /**
         * Will render translation metabox tab title. Should be the site name
         *
         * @param int $siteId The site id which name should be rendered as tab title
         * @throws NonexistentTable
         */
        protected function renderTabAnchor(int $siteId)
        {
        }
        /**
         * Will render translation metabox tab content (translatable options)
         *
         * @param int $siteId The site id
         * @param WP_User $user The user which is currently in edit
         * @param MetaboxFieldsHelper $helper
         */
        protected function renderTabContent(int $siteId, \WP_User $user, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce {
    /**
     * Class ArchiveProductsUrlFilter
     */
    class ArchiveProducts
    {
        /**
         * Retrieve the translated shop page archive url
         *
         * @param string $url
         * @return string
         */
        public function shopArchiveUrl(string $url) : string
        {
        }
    }
    /**
     * Class AttributeTermTranslateUrl
     */
    class AttributeTermTranslateUrl implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait {
            enable as private traitEnable;
        }
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @var \WP_Rewrite
         */
        private $wpRewrite;
        /**
         * AttributeTermTranslateUrlFilter constructor.
         *
         * @param \wpdb $wpdb
         * @param UrlFactory $urlFactory
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * Retrieve the term link page by his term taxonomy Id.
         *
         * @param bool $checker
         * @param Translation $translation
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return bool
         */
        public function termLinkByTaxonomyId(bool $checker, \Inpsyde\MultilingualPress\Framework\Api\Translation $translation, int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : bool
        {
        }
        /**
         * Lazy inject for \WP_Rewrite
         *
         * @param \WP_Rewrite $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null) : bool
        {
        }
        /**
         * Check if the current taxonomy Id is a WooCommerce Attribute Taxonomy
         *
         * @param int $termTaxonomyId
         * @return bool
         */
        private function isAttributeTaxonomy(int $termTaxonomyId) : bool
        {
        }
        /**
         * Build the term link for the translated term
         *
         * @param string $taxonomySlug
         * @param string $termSlug
         * @return string
         */
        private function buildRemoteUrl(string $taxonomySlug, string $termSlug) : string
        {
        }
        /**
         * Retrieve the term data based on term taxonomy Id
         *
         * @param int $termTaxonomyId
         * @return array
         */
        private function termData(int $termTaxonomyId) : array
        {
        }
        /**
         * Query string to retrieve term data
         *
         * @return string
         */
        private function remoteTermSql() : string
        {
        }
        /**
         * Build the permalink structure
         *
         * @param string $taxonomySlug
         * @return string
         */
        private function permalinkStructure(string $taxonomySlug) : string
        {
        }
        /**
         * Build the plain term url
         *
         * @param string $taxonomySlug
         * @param string $termSlug
         * @return string
         */
        private function plainTermLink(string $taxonomySlug, string $termSlug) : string
        {
        }
        /**
         * Retrieve the attribute name by his taxonomy
         *
         * @param string $taxonomySlug
         * @return string
         */
        private function attributeNameByTaxonomySlug(string $taxonomySlug) : string
        {
        }
    }
    /**
     * Class AttributesRelationship
     */
    class AttributesRelationship
    {
        const WC_ATTRIBUTE_TAXONOMY_PREFIX = 'pa_';
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @var TaxonomyRepository
         */
        private $taxonomyRepository;
        /**
         * AttributesRelationship constructor
         *
         * @param TaxonomyRepository $taxonomyRepository
         * @param SiteRelations $siteRelations
         * @param \wpdb $wpdb
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \wpdb $wpdb)
        {
        }
        /**
         * Create attribute taxonomy into current site by getting data by the source site
         *
         * @param \WP_Term $term
         * @param string $taxonomy
         * @return void
         */
        public function createAttributeRelation(\WP_Term $term, string $taxonomy)
        {
        }
        /**
         * Add translation support for attribute taxonomy
         *
         * @param int $id
         * @param array $data
         */
        public function addSupportForAttribute(int $id, array $data)
        {
        }
        /**
         * Retrieve data for the attribute
         *
         * @param int $siteId
         * @param string $attributeName
         * @return array
         */
        private function sourceAttributesByName(int $siteId, string $attributeName) : array
        {
        }
        /**
         * Retrieve all of the translatable taxonomies even the ones not active
         *
         * @return array
         */
        private function translatableTaxonomies() : array
        {
        }
        /**
         * Insert attribute taxonomy into db
         *
         * @param array $attribute
         * @return int
         */
        private function insertAttributeTaxonomy(array $attribute) : int
        {
        }
    }
    /**
     * Class AvailableTaxonomiesAttributes
     */
    class AvailableTaxonomiesAttributes
    {
        /**
         * Remove Attributes from the list of the translatable taxonomies in the settings ui
         *
         * @param array $taxonomies
         * @return array
         */
        public function removeAttributes(array $taxonomies) : array
        {
        }
        /**
         * Retrieve WooCommerce attributes taxonomies names
         *
         * @return array
         */
        private function attributes() : array
        {
        }
    }
    /**
     * Class PermalinkStructure
     */
    class PermalinkStructure
    {
        /**
         * Get the base permalink structure for product by WooCommerce Settings
         *
         * @return string
         */
        public function baseforProduct() : string
        {
        }
        /**
         * Get the base permalink structure for product category by WooCommerce Settings
         *
         * @return string
         */
        public function forProductCategory() : string
        {
        }
        /**
         * Get the base permalink structure for product tag by WooCommerce Settings
         *
         * @return string
         */
        public function forProductTag() : string
        {
        }
        /**
         * Get the base permalink structure for product attribute by WooCommerce Settings
         *
         * @param string $taxonomySlug
         * @return string
         */
        public function forProductAttribute(string $taxonomySlug) : string
        {
        }
        /**
         * Get the permalinks by WooCommerce option
         *
         * @return \stdClass
         */
        private function wooCommercePermalinks() : \stdClass
        {
        }
        /**
         * Get the permalinks structure by WordPress option
         *
         * @return string
         */
        private function permalinksStructure() : string
        {
        }
        /**
         * Retrieve the attribute name by the taxonomy slug
         *
         * @param string $taxonomySlug
         * @return string
         */
        private function attributeNameByTaxonomySlug(string $taxonomySlug) : string
        {
        }
    }
    class ProductMetaboxesBehaviorActivator
    {
        const ALLOWED_POST_TYPES = ['product'];
        /**
         * @var MetaboxFields
         */
        private $metaboxFields;
        /**
         * @var PanelView
         */
        private $panelView;
        /**
         * @var ActivePostTypes
         */
        private $activePostTypes;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var Attachment\Copier
         */
        private $attachmentCopier;
        /**
         * @var PersistentAdminNotices
         */
        private $notice;
        /**
         * ProductMetaboxesActivator constructor
         *
         * @param MetaboxFields $metaboxFields
         * @param PanelView $panelView
         * @param ActivePostTypes $activePostTypes
         * @param ContentRelations $contentRelations
         * @param Attachment\Copier $attachmentCopier
         * @param PersistentAdminNotices $notice
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields $metaboxFields, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\PanelView $panelView, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Attachment\Copier $attachmentCopier, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notice)
        {
        }
        /**
         * @param array $tabs
         * @param Post\RelationshipContext $context
         * @return array
         */
        public function setupMetaboxFields(array $tabs, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param Post\RelationshipContext $relationshipContext
         */
        public function renderPanels(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * @param Post\RelationshipContext $context
         * @param Request $request
         * @param PersistentAdminNotices $notice
         */
        public function saveMetaboxes(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notice)
        {
        }
        /**
         * @param $tabs
         */
        private function removeTabExcerpt(array &$tabs)
        {
        }
    }
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        const MODULE_ID = 'woocommerce';
        const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'woocommerce_assets_factory';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager) : bool
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @throws AssetException
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function activateBasePermalinkStructures(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Add Metaboxes for Product
         *
         * @param Container $container
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        private function activateProductMetaboxes(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Do not add the attributes taxonomies to the list of translatable taxonomies.
         * Them are handled differently within the Product Tab.
         */
        private function removeAttributeTaxonomiesFieldsFromPostMetabox()
        {
        }
        /**
         * Setup assets for WooCommerce
         *
         * @param Container $container
         * @throws AssetException
         */
        private function bootstrapAssets(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @return bool
         */
        private function isWooCommerceActive() : bool
        {
        }
        /**
         * Check if the current admin edit page is for post type product
         *
         * @param string $currentPage
         * @return bool
         */
        private function isEditProductPage(string $currentPage) : bool
        {
        }
        /**
         * @param Container $container
         * @return void
         */
        protected function addProductSearchHandler(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function postTypeActions(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function taxonomyActions(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Handle the WooCommerce reviews support.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        protected function handleSupportForReviews(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : void
        {
        }
        /**
         * Perform an actions when WooCommerce support is deactivated
         *
         * If Woo support is deactivated we should disable the translation metabox support for
         * Woo entities(Products, all Woo taxonomies) and also we need to disable the
         * Woo post type and taxonomy settings from MLP global settings
         *
         * @param Container $container
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function removeWooCommerceSupport(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Disable MLP settings for certain WooCommerce entities.
         *
         * Regardless of whether the WooCommerce module is active, some WooCommerce entities settings should be removed
         * from admin area, cause some entities like "Attributes" are supported under the hood when the module is active and
         * some are not translatable at all, such as "Orders".
         * This method is for removing the settings of such entities from admin area.
         *
         * @param Container $container
         */
        protected function disableSettingsForWooCommerceEntities(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Ajax {
    /**
     * Functionality for searching products in connected sites
     *
     *
     * @psalm-type productId = int
     * @psalm-type title = string
     *
     */
    class Search
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        public const ACTION = 'multilingualpress_remote_post_search';
        public const FILTER_PRODUCT_SEARCH_LIMIT = 'multilingualpress.filter_product_search_limit';
        /**
         * @var ContextBuilder
         */
        private $contextBuilder;
        /**
         * @var wpdb
         */
        private $wpdb;
        /**
         * @var string
         */
        protected $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder, \wpdb $wpdb, string $alreadyConnectedNotice)
        {
        }
        /**
         * Handles the request and calls find products with the search terms
         * @param Request $request
         * @return void
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Finds the product by given search query.
         *
         * @param string $searchQuery The search query.
         * @param RelationshipContext $context
         * @return array<int, string> A map of product ID to product title.
         * @psalm-return array<productId, title>
         * @throws NonexistentTable
         */
        protected function findProducts(string $searchQuery, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * @param string $searchQuery
         * @param int $excludePostId
         * @return array|object|null
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         */
        protected function findProductsByNameOrSku(string $searchQuery, int $excludePostId)
        {
        }
        /**
         * Checks if the product with given product ID is connected to any product from given site ID.
         *
         * @param int $productId The product ID.
         * @param int $siteId The site ID.
         * @return bool true if is connected, otherwise false.
         * @throws NonexistentTable
         */
        protected function isConnectedWithProductOfSite(int $productId, int $siteId) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    interface RenderCallback
    {
        /**
         * The callback to render the field settings.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext);
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\Inventory {
    /**
     * MultilingualPress Product Inventory Field
     */
    class Backorders implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * @var array
         */
        private $backorderOptions;
        /**
         * Backorders constructor.
         *
         * @param array $backorderOptions A map of Woo backorder field options
         */
        public function __construct(array $backorderOptions)
        {
        }
        /**
         * Render the Manage Backorders Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class LowStockAmount implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * @var int
         */
        private $lowStockAmount;
        /**
         * LowStockAmount constructor.
         *
         * @param int $lowStockAmount Woo Store-wide threshold amount value
         */
        public function __construct(int $lowStockAmount)
        {
        }
        /**
         * Render the Manage Low Stock Amount Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip() : string
        {
        }
        /**
         * Create the placeholder text for Low Stock Amount field
         *
         * @return string
         */
        protected function placeholder() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : int
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class ManageStock implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Manage Stock Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description Message
         *
         * @return string
         */
        private function description() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce Inventory product data
     */
    class OverrideInventorySettings implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Override Inventory setting field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress Product General Field
     */
    class Sku implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Sku Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        private function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class SoldIndividually implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Sold Individually Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description Message
         *
         * @return string
         */
        private function description() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class Stock implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Stock Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return int
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : int
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class StockStatus implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Stock Status Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field {
    class OverrideAttributes
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * Class OverrideCrossellsProducts
     */
    final class OverrideCrossellsProducts
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce downloadable product data
     */
    class OverrideDownloadableFiles
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce downloadable product data
     */
    class OverrideDownloadableSettings
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    final class OverrideGroupedProducts
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class OverrideProductGallery
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce product type
     */
    class OverrideProductType
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * Class OverrideUpsellsProducts
     */
    class OverrideUpsellsProducts
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * Class OverrideVariations
     */
    class OverrideVariations
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress Product Url Field
     */
    class ProductUrl
    {
        /**
         * Render the Product Url Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        private function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
    /**
     * MultilingualPress Product Url Button Text Field
     */
    class ProductUrlButtonText
    {
        /**
         * Render the Product Url Button Text Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        private function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
    /**
     * MultilingualPress Purchase Note Field
     */
    class PurchaseNote
    {
        /**
         * Render the Purchase Note Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        private function descriptionTooltip() : string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
    /**
     * MultilingualPress Product Regular Price Field
     */
    class RegularPrice
    {
        /**
         * Render the Product Regular Price Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
        /**
         * Build Regular Price ToolTip
         *
         * @return string
         */
        private function regularPriceTooltip() : string
        {
        }
    }
    /**
     * MultilingualPress Product Sale Price Field
     */
    class SalePrice
    {
        /**
         * Render the Product Sale Price Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
        /**
         * Build Sale Price ToolTip
         *
         * @return string
         */
        private function salePriceTooltip() : string
        {
        }
    }
    /**
     * MultilingualPress override WooCommerce product short description
     */
    class ShortDescription
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
        /**
         * Print the default editor scripts to the page to be able to reinitialize the wp editor
         * after a relationship event occur.
         *
         * @see \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\RelationshipUpdater::handle()
         */
        private function printDefaultEditorScripts()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * Class FieldsAwareOfProductType
     */
    class FieldsAwareOfProductType
    {
        const OPTIONS = [\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_OVERRIDE_VARIATIONS, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_GROUPED_PRODUCTS, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_PRODUCT_URL, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_PRODUCT_URL_BUTTON_TEXT];
        /**
         * Check if the same product type is needed based on the give values and the options
         *
         * @param array $values
         * @return bool
         */
        public static function needSameProductType(array $values) : bool
        {
        }
    }
    /**
     * Class MetaboxAction
     * @package Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product
     */
    final class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        const RELATIONSHIP_TYPE = 'post';
        const POST_TYPE = 'product';
        const DEFAULT_PRODUCT_TYPE = 'simple';
        const PRODUCT_TYPE_TAXONOMY_NAME = 'product_type';
        const PRODUCT_TYPE_FIELD_NAME = 'product-type';
        const PRODUCT_GALLERY_META_KEY = 'product_image_gallery';
        // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
        const ACTION_METABOX_BEFORE_UPDATE_REMOTE_PRODUCT = 'multilingualpress.metabox_before_update_remote_product';
        const ACTION_METABOX_AFTER_UPDATE_REMOTE_PRODUCT = 'multilingualpress.metabox_after_update_remote_product';
        const ACTION_METABOX_AFTER_SAVE_REMOTE_PRODUCT_VARIATIONS = 'multilingualpress.metabox_after_save_remote_product_variations';
        // phpcs:enable
        /**
         * @var array
         */
        private static $calledCount = [];
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var Post\SourcePostSaveContext
         */
        private $sourcePostContext;
        /**
         * @var ActivePostTypes
         */
        private $postTypes;
        /**
         * @var Post\RelationshipContext
         */
        private $postRelationshipContext;
        /**
         * @var MetaboxFieldsHelper
         */
        private $fieldsHelper;
        /**
         * @var MetaboxFields
         */
        private $metaboxFields;
        /**
         * @var Attachment\Copier
         */
        private $attachmentCopier;
        private $notice;
        /**
         * MetaboxAction constructor
         *
         * @param Post\RelationshipContext $postRelationshipContext
         * @param ActivePostTypes $postTypes
         * @param ContentRelations $contentRelations
         * @param Attachment\Copier $attachmentCopier
         * @param MetaboxFields $metaboxFields
         * @param PersistentAdminNotices $notice
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $postRelationshipContext, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Attachment\Copier $attachmentCopier, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields $metaboxFields, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notice)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * Do the operation necessary to store the data for the current product.
         *
         * @param Request $request
         * @param ProductRelationSaveHelper $relationshipHelper
         * @param PersistentAdminNotices $notices
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        private function doSaveOperation(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $relationshipHelper, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * Set grouped products to remote site by retrieve the related products
         * by the source one.
         *
         * @param ProductRelationSaveHelper $relationshipHelper
         * @param WC_Product $sourceProduct
         * @param WC_Product $remoteProduct
         * @return bool
         */
        private function maybeSetGroupedProducts(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $relationshipHelper, \WC_Product $sourceProduct, \WC_Product $remoteProduct) : bool
        {
        }
        /**
         * Set the upsells products for the remote product
         *
         * @param ProductRelationSaveHelper $relationshipHelper
         * @param WC_Product $sourceProduct
         * @param WC_Product $remoteProduct
         * @return bool
         */
        private function maybeSetUpsellsProducts(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $relationshipHelper, \WC_Product $sourceProduct, \WC_Product $remoteProduct) : bool
        {
        }
        /**
         * Set the inventory fields for the remote product
         *
         * @param WC_Product $remoteProduct
         * @param array<string> $values a map of product inventory field keys to values
         * @throws WC_Data_Exception
         */
        protected function assignInventoryFields(\WC_Product $remoteProduct, array $values)
        {
        }
        /**
         * Set the product sku
         *
         * @param WC_Product $product
         * @param string $sku
         * @throws WC_Data_Exception
         */
        protected function maybeSetSku(\WC_Product $product, string $sku)
        {
        }
        /**
         * Get a map of changed inventory field keys to values
         *
         * @param WC_Product $sourceProduct
         * @param array<string> $changedFields The list of changed field meta keys
         * @param array<string> $productFields a map of product field keys to values
         * @return array<string> a map of product inventory field keys to values
         */
        protected function changedInventoryFields(\WC_Product $sourceProduct, array $changedFields, array $productFields) : array
        {
        }
        /**
         * Set the cross sells product for the remote product by retrieve the related products
         * by the source one.
         *
         * @param ProductRelationSaveHelper $relationshipHelper
         * @param WC_Product $sourceProduct
         * @param WC_Product $remoteProduct
         * @return bool
         */
        private function maybeSetCrossellsProducts(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $relationshipHelper, \WC_Product $sourceProduct, \WC_Product $remoteProduct) : bool
        {
        }
        /**
         * Set Product Gallery Ids
         *
         * @param WC_Product $remoteProduct
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param Request $request
         * @return bool
         */
        private function setProductGalleryIds(\WC_Product $remoteProduct, int $sourceSiteId, int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
        /**
         * Attach Product Gallery Images to their own product
         *
         * @param WC_Product $product
         */
        private function attachGalleryImagesToProduct(\WC_Product $product)
        {
        }
        /**
         * Set Product Url and Button text if product type is an external one
         *
         * @param WC_Product $remoteProduct
         * @param string $url
         * @param string $buttonText
         * @return bool
         */
        private function maybeSetProductUrlAndButtonText(\WC_Product $remoteProduct, string $url, string $buttonText) : bool
        {
        }
        /**
         * Set the remote product attributes
         *
         * @param WC_Product $sourceProduct
         * @param WC_Product $remoteProduct
         * @param ProductRelationSaveHelper $relationshipHelper
         * @return bool
         */
        private function setRemoteProductAttributes(\WC_Product $sourceProduct, \WC_Product $remoteProduct, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $relationshipHelper) : bool
        {
        }
        /**
         * Store the remote product variations.
         *
         * @param WC_Product $remoteProduct
         * @param array $sourceVariations
         * @param ProductRelationSaveHelper $relationshipHelper
         * @param array $sourceProductVariationAttachmentData
         * @return bool
         */
        private function saveRemoteProductVariations(\WC_Product $remoteProduct, array $sourceVariations, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $relationshipHelper, array $sourceProductVariationAttachmentData) : bool
        {
        }
        /**
         * Retrieve the variations products.
         *
         * @param \WC_Product_Variable $product
         * @return array
         */
        private function variationProducts(\WC_Product_Variable $product) : array
        {
        }
        /**
         * Duplicate attributes and create taxonomy and terms into the remote site if needed.
         *
         * @param ProductRelationSaveHelper $helper
         * @param Post\RelationshipContext $context
         * @param WC_Product $product
         * @return array
         */
        private function duplicateAttributes(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \WC_Product $product) : array
        {
        }
        /**
         * Clone source variations by setting up custom attribute terms.
         *
         * @param ProductRelationSaveHelper $helper
         * @param \WC_Product_Variation $sourceVariation
         * @param array $remoteAttributeTerms
         * @param WC_Product $remoteProduct
         * @param array $sourceProductVariationAttachmentData
         * @return \WC_Product_Variation
         */
        private function cloneVariationWithRemoteTerms(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\ProductRelationSaveHelper $helper, \WC_Product_Variation $sourceVariation, array $remoteAttributeTerms, \WC_Product $remoteProduct, array $sourceProductVariationAttachmentData) : \WC_Product_Variation
        {
        }
        /**
         * Clone Product Attribute.
         *
         * @param \WC_Product_Attribute $sourceAttribute
         * @param array $options
         * @return \WC_Product_Attribute
         * @throws RuntimeException
         */
        private function duplicateProductAttributeWithCustomOptions(\WC_Product_Attribute $sourceAttribute, array $options) : \WC_Product_Attribute
        {
        }
        /**
         * Set downloadable files if the product is downloadable
         *
         * @param WC_Product $sourceProduct
         * @param WC_Product $remoteProduct
         * @return bool
         */
        private function maybeSetDownloadableFiles(\WC_Product $sourceProduct, \WC_Product $remoteProduct) : bool
        {
        }
        /**
         * Copy downloadable Settings if the product is downloadable
         *
         * @param WC_Product $sourceProduct
         * @param WC_Product $remoteProduct
         * @return bool
         */
        private function maybeSetDownloadableSettings(\WC_Product $sourceProduct, \WC_Product $remoteProduct) : bool
        {
        }
        /**
         * Check if the current request should be processed by save().
         *
         * @param Post\SourcePostSaveContext $context
         * @return bool
         */
        private function isValidSaveRequest(\Inpsyde\MultilingualPress\TranslationUi\Post\SourcePostSaveContext $context) : bool
        {
        }
        /**
         * Retrieve the source context for current post type
         *
         * @param Request $request
         * @return Post\SourcePostSaveContext
         */
        private function sourceContext(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : \Inpsyde\MultilingualPress\TranslationUi\Post\SourcePostSaveContext
        {
        }
        /**
         * Grab all fields from the tab
         *
         * @param Request $request
         * @return array
         */
        private function allFieldsValues(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * Retrieves all field values
         *
         * @param MetaboxTab $tab
         * @param Request $request
         * @return array
         */
        private function tabFieldsValues(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxTab $tab, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * May be the product type have to be the same
         *
         * @param array $values
         * @return bool
         */
        private function maybeOverrideProductType(array $values) : bool
        {
        }
        /**
         * @param array $sourceProductVariations
         * @return array
         */
        private function getSourceProductVariationAttachmentData(array $sourceProductVariations) : array
        {
        }
        /**
         * @param string $taxonomyName
         * @return int
         * @throws RuntimeException
         */
        protected function createProductAttribute(string $taxonomyName) : int
        {
        }
        /**
         * Get The list of changed meta keys from request
         *
         * @param Request $request
         * @return array<string> The list of changed field meta keys
         */
        protected function changedFields(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    interface PostMetaboxField
    {
        /**
         * @return string
         */
        public function key();
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext);
        /**
         * @param Request $request
         * @param MetaboxFieldsHelper $helper
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper);
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool;
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * MultilingualPress WooCommerce Metabox Field Interface
     */
    interface WooCommerceMetaboxField extends \Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField
    {
    }
    /**
     * MultilingualPress WooCommerce Metabox Field
     *
     * This class is a proxy to Post\MetaboxField
     */
    final class MetaboxField implements \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\WooCommerceMetaboxField
    {
        /**
         * @var PostMetaboxField
         */
        private $metaboxField;
        /**
         * MetaboxField constructor.
         * @param PostMetaboxField $metaboxField
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField $metaboxField)
        {
        }
        /**
         * @inheritdoc
         */
        public function key()
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @inheritdoc
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool
        {
        }
    }
    /**
     * MultilingualPress WooCommerce Metabox Fields
     */
    class MetaboxFields
    {
        const TAB = 'tab-product';
        const FIELD_PRODUCT_URL = 'product_url';
        const FIELD_PRODUCT_URL_BUTTON_TEXT = 'button_text';
        const FIELD_OVERRIDE_PRODUCT_TYPE = 'override_product_type';
        const FIELD_OVERRIDE_PRODUCT_GALLERY = 'override_product_gallery';
        const FIELD_OVERRIDE_VARIATIONS = 'override_attribute_variations';
        const FIELD_OVERRIDE_ATTRIBUTES = 'override_attributes';
        const FIELD_OVERRIDE_DOWNLOADABLE_FILES = 'override_downloadable_files';
        const FIELD_OVERRIDE_DOWNLOADABLE_SETTINGS = 'override_downloadable_settings';
        const FIELD_OVERRIDE_INVENTORY_SETTINGS = 'override_inventory_settings';
        const FIELD_REGULAR_PRICE = 'regular_price';
        const FIELD_SALE_PRICE = 'sale_price';
        const FIELD_PRODUCT_SHORT_DESCRIPTION = 'product_short_description';
        const FIELD_PURCHASE_NOTE = 'purchase_note';
        const FIELD_SKU = 'sku';
        const FIELD_MANAGE_STOCK = 'manage_stock';
        const FIELD_SOLD_INDIVIDUALLY = 'sold_individually';
        const FIELD_STOCK = 'stock';
        const FIELD_BACKORDERS = 'backorders';
        const FIELD_STOCK_STATUS = 'stock_status';
        const FIELD_LOW_STOCK_AMOUNT = 'low_stock_amount';
        const FIELD_GROUPED_PRODUCTS = 'grouped_products';
        const FIELD_CROSSELLS_PRODUCTS = 'crossells_products';
        const FIELD_UPSELLS_PRODUCTS = 'upsells_products';
        /**
         * @var WooCommerceMetaboxFields
         */
        private $wooCommerceFields;
        /**
         * MetaboxFields constructor.
         * @param WooCommerceMetaboxFields $wooCommerceFields
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\WooCommerceMetaboxFields $wooCommerceFields)
        {
        }
        /**
         * Retrieve all fields for the WooCommerce metabox tab.
         *
         * @return MetaboxTab[]
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function allFieldsTabs() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    /**
     * Interface for any kind of custom Metabox tabs.
     */
    interface MetaboxFillable
    {
        /**
         * The id of the metabox tab.
         *
         * @return string
         */
        public function id() : string;
        /**
         * The label to show to the tab header.
         *
         * @return string
         */
        public function label() : string;
        /**
         * The fields collection for the current tab.
         *
         * @return MetaboxField[]
         */
        public function fields() : array;
        /**
         * If the metabox tab is enabled or not.
         *
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool;
        /**
         * Render the metabox markup.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext);
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * MultilingualPress MetaboxTab for Product
     */
    final class MetaboxTab implements \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFillable
    {
        const ACTION_BEFORE_METABOX_UI_PANEL = 'multilingualpress.before_metabox_panel';
        const ACTION_AFTER_METABOX_UI_PANEL = 'multilingualpress.after_metabox_panel';
        const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
        const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
        const FILTER_TRANSLATION_UI_SHOW_CONTENT = 'multilingualpress.translation_ui_show_content';
        /**
         * @var string
         */
        private $id;
        /**
         * @var MetaboxField[]
         */
        private $fields;
        /**
         * @var string
         */
        private $label;
        public function __construct(string $id, string $label, \Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField ...$fields)
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function label() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function fields() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Render MultilingualPress custom fields for product.
         *
         * @param MetaboxFieldsHelper $helper
         * @param Post\RelationshipContext $relationshipContext
         */
        private function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * Class SettingsView
     */
    class PanelView
    {
        /**
         * @var MetaboxField[]
         */
        private $settings;
        /**
         * SettingsView constructor.
         * @param callable ...$settings
         */
        public function __construct(callable ...$settings)
        {
        }
        /**
         * Render the settings fields.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Render setting and fields.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        private function renderSettings(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Render Tabs header.
         *
         * @param RelationshipContext $relationshipContext
         */
        private function renderTabs(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Retrieve the data tabs settings.
         *
         * @return array
         */
        private function dataTabs() : array
        {
        }
        /**
         * Sort the tabs based on user callback.
         *
         * @param array $left
         * @param array $right
         * @return int
         */
        private function sortTabs(array $left, array $right) : int
        {
        }
        /**
         * Build the class attribute for the tab.
         *
         * @param string $key
         * @param array $tab
         * @return string
         */
        private function tabClassAttribute(string $key, array $tab) : string
        {
        }
    }
    /**
     * Class ProductRelationSaveHelper
     */
    class ProductRelationSaveHelper
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * ProductRelationSaveHelper constructor
         *
         * @param Request $request
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param array $attributes
         * @return array
         */
        public function filterProductCustomAttributes(array $attributes) : array
        {
        }
        /**
         * @param array $attributes
         * @return array
         */
        public function filterProductAttributesTerms(array $attributes) : array
        {
        }
        /**
         * Retrieve related remote terms and create them in the remote site if necessary.
         *
         * @param array $sourceTermsIds
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param string $taxonomyName
         * @return array
         *
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function mayRelateTerms(array $sourceTermsIds, int $sourceSiteId, int $remoteSiteId, string $taxonomyName) : array
        {
        }
        /**
         * Get the remote product based on the product type value given in the current request
         *
         * @param int $sourceSiteId
         * @param int $sourceProductId
         * @param int $remoteSiteId
         * @param bool $overrideProductType
         * @return \WC_Product
         * @throws \DomainException
         */
        public function remoteProduct(int $sourceSiteId, int $sourceProductId, int $remoteSiteId, bool $overrideProductType) : \WC_Product
        {
        }
        /**
         * Retrieve the related products by a specific remote site
         *
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param array $productsIds
         * @return array
         */
        public function relatedProductsForSiteId(int $sourceSiteId, int $remoteSiteId, array $productsIds) : array
        {
        }
        /**
         * @param Post\RelationshipContext $context
         * @param \WC_Product_Variation $sourceVariation
         * @return array
         */
        public function relatedAttributeTerms(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \WC_Product_Variation $sourceVariation) : array
        {
        }
        /**
         * Relate terms between source and remote site
         *
         * @param int $sourceSiteId
         * @param int $sourceTermId
         * @param int $remoteSiteId
         * @param int $remoteTermId
         */
        protected function relateTerms(int $sourceSiteId, int $sourceTermId, int $remoteSiteId, int $remoteTermId)
        {
        }
        /**
         * Create a term if not exists in the current site.
         *
         * @param int $remoteTermId
         * @param string $sourceTermName
         * @param string $taxonomyName
         * @return mixed
         *
         * @phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        protected function maybeTerm(int $remoteTermId, string $sourceTermName, string $taxonomyName)
        {
        }
    }
    /**
     *  WooCommerce Settings Fields
     */
    final class SettingView
    {
        /**
         * @var string
         */
        private $name;
        /**
         * @var MetaboxField[]
         */
        private $fields;
        /**
         * Setting constructor.
         * @param string $name
         * @param MetaboxField ...$fields
         */
        public function __construct(string $name, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxField ...$fields)
        {
        }
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Retrieve the setting container attribute id.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        private function id(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : string
        {
        }
    }
    /**
     * MultilingualPress Metabox Fields for WooCommerce Panel
     */
    class WooCommerceMetaboxFields
    {
        /**
         * Build the WooCommerce General metabox fields
         *
         * @return array
         */
        public function generalSettingFields() : array
        {
        }
        /**
         * Build the WooCommerce Invetory metabox fields
         *
         * @return array
         */
        public function inventorySettingFields() : array
        {
        }
        /**
         * Build the WooCommerce Advanced metabox fields
         *
         * @return array
         */
        public function advancedSettingFields() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Review\Field {
    class CommentMetaboxReviewRating implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        /**
         * @var string
         */
        private $key;
        /**
         * @var string
         */
        protected $label;
        /**
         * @var MetaboxFieldsHelperFactoryInterface
         */
        protected $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key() : string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(bool $hasRemoteComment = false) : string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext) : void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper
        {
        }
        /**
         * Get the rating meta value of a given review from a given site.
         *
         * @param int $siteId The site ID.
         * @param int $commentId The review ID.
         * @return int The review rating meta value.
         */
        protected function reviewRatingMetaValue(int $siteId, int $commentId) : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Activation {
    /**
     * Activator implementation using a network option.
     */
    class Activator
    {
        const OPTION = 'multilingualpress_activation';
        /**
         * @var callable[]
         */
        private $callbacks = [];
        /**
         * Takes care of pending plugin activation tasks.
         *
         * @return bool
         */
        public function handlePendingActivation() : bool
        {
        }
        /**
         * Performs anything to handle the plugin activation.
         *
         * @return bool
         */
        public function handleActivation() : bool
        {
        }
        /**
         * Registers the given callback.
         *
         * @param callable $callback
         * @param bool $prepend
         * @return Activator
         */
        public function registerCallback(callable $callback, bool $prepend = false) : \Inpsyde\MultilingualPress\Activation\Activator
        {
        }
    }
    /**
     * Service provider for all activation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @return void
         */
        private function setupActivator(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Api {
    class ContentRelationshipMeta implements \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface
    {
        /**
         * @var wpdb
         */
        protected $wpdb;
        /**
         * @var RelationshipMetaTable
         */
        protected $relationshipMetaTable;
        /**
         * @var ContentRelations
         */
        protected $contentRelations;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Database\Table\RelationshipMetaTable $relationshipMetaTable, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function updateRelationshipMeta(int $relationshipId, string $metaKey, $metaValue) : void
        {
        }
        /**
         * @inheritDoc
         */
        public function deleteRelationshipMeta(int $relationshipId) : bool
        {
        }
        /**
         * @inheritDoc
         */
        public function relationshipMetaValue(int $relationshipId, string $metaKey) : string
        {
        }
        /**
         * @inheritDoc
         */
        public function relationshipMetaValueByPostId(int $postId, string $metaKey) : string
        {
        }
    }
    /**
     * Service provider for all API objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function integrateCache(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function integrateRelationsCache(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function integrateContentRelationsCache(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function integrateTranslationCache(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
    /**
     * Translations API implementation.
     */
    final class Translations implements \Inpsyde\MultilingualPress\Framework\Api\Translations
    {
        const FILTER_SEARCH_TRANSLATIONS = 'multilingualpress.search_translations';
        /**
         * @var string
         */
        const SEARCH_CACHE_KEY = 'translations';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var Languages
         */
        private $languages;
        /**
         * @var NullTranslator
         */
        private $nullTranslator;
        /**
         * @var WordpressContext
         */
        private $wordpressContext;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @var Translator[]
         */
        private $translators = [];
        /**
         * @var Facade
         */
        private $cache;
        /**
         * @var CacheSettingsRepository
         */
        private $cacheSettingsRepository;
        /**
         * @param SiteRelations $siteRelations
         * @param ContentRelations $contentRelations
         * @param Languages $languages
         * @param WordpressContext $wordpressContext
         * @param Facade $cache
         * @param CacheSettingsRepository $cacheSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Api\Languages $languages, \Inpsyde\MultilingualPress\Framework\WordpressContext $wordpressContext, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function searchTranslations(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : array
        {
        }
        /**
         * @inheritdoc
         */
        public function registerTranslator(\Inpsyde\MultilingualPress\Framework\Translator\Translator $translator, string $type) : bool
        {
        }
        /**
         * @param TranslationSearchArgs $args
         * @return Translation[]
         */
        private function buildTranslations(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : array
        {
        }
        /**
         * @param int[] $siteIds
         * @param Language[] $languages
         * @param int[] $relations
         * @param string $type
         * @param TranslationSearchArgs $args
         * @return Translation[]
         */
        private function buildTranslationsForSiteIds(array $siteIds, array $languages, array $relations, string $type, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : array
        {
        }
        /**
         * @param Language $language
         * @param int $remoteSiteId
         * @param int[] $contentRelations
         * @param string $type
         * @param TranslationSearchArgs $args
         * @return Translation
         */
        private function buildTranslationDataForSiteId(\Inpsyde\MultilingualPress\Framework\Language\Language $language, int $remoteSiteId, array $contentRelations, string $type, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * Returns the translation data for a request that is for a related content element.
         *
         * @param Translation $currentTranslation
         * @param int $remoteSiteId
         * @param TranslationSearchArgs $args
         * @param string $type
         * @return Translation
         */
        private function translationForRelatedContent(\Inpsyde\MultilingualPress\Framework\Api\Translation $currentTranslation, int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args, string $type) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * Returns the translation data for a request that is not for a related content element.
         *
         * @param Translation $currentTranslation
         * @param int $remoteSiteId
         * @param TranslationSearchArgs $args
         * @param string $type
         * @return Translation
         */
        private function translationForNotRelatedContent(\Inpsyde\MultilingualPress\Framework\Api\Translation $currentTranslation, int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args, string $type) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return NullTranslator
         */
        private function nullTranslator() : \Inpsyde\MultilingualPress\Framework\Translator\NullTranslator
        {
        }
        /**
         * @param string $type
         * @return Translator
         */
        private function translatorForType(string $type) : \Inpsyde\MultilingualPress\Framework\Translator\Translator
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli {
    /**
     * @psalm-type Arg = array{type: string, name: string, description?: string, optional?: bool, options?: list<string>}
     * @psalm-type Doc = array{shortdesc?: string, synopsis?: list<Arg>, when?: string, longdesc?: string}
     */
    interface WpCliCommand
    {
        /**
         * The handler of
         * {@link https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/ WP_CLI::add_command}
         * implementation.
         *
         * @param array<string> $args The list of positional arguments
         * @param array<string, scalar> $associativeArgs A map of associative argument names to values
         * @return void
         * @throws WP_CLI\ExitException
         */
        public function handler(array $args, array $associativeArgs) : void;
        /**
         * The command documentation.
         *
         * @psalm-return Doc A map of
         * {@link https://make.wordpress.org/cli/handbook/references/documentation-standards/ command doc} names to values
         * @return array<string, string|array> A map of
         * {@link https://make.wordpress.org/cli/handbook/references/documentation-standards/ command doc} names to values
         */
        public function docs() : array;
        /**
         * The Name of the command
         *
         * @return string The Name of the command
         */
        public function name() : string;
    }
}
namespace Inpsyde\MultilingualPress\Api\WpCliCommands {
    /**
     * WP-CLI Set Language.
     */
    class SetLanguage implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        /**
         * @var WpCliCommandsHelper
         */
        protected $wpCliCommandsHelper;
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @var array<string> A list of available MLP language BCP-47 codes
         */
        private $availableMlpLanguages;
        /**
         * SetLanguage constructor.
         *
         * @param SiteSettingsRepository $repository
         * @param array<string> $availableMlpLanguages A list of available MLP language BCP-47 codes
         * @param WpCliCommandsHelper $wpCliCommandsHelper
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository, array $availableMlpLanguages, \Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper)
        {
        }
        /**
         * @inheritDoc
         */
        public function name() : string
        {
        }
        /**
         * The handler of
         * {@link https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/ WP_CLI::add_command}
         * implementation
         *
         * @param array<string> $args The list of positional arguments
         * @param array<string, scalar> $associativeArgs A map of associative argument names to values
         * @psalm-param array{site-id: string, language: string} $associativeArgs
         * A map of associative argument names to values
         * @return void
         * @throws WP_CLI\ExitException
         */
        public function handler(array $args, array $associativeArgs) : void
        {
        }
        /**
         * @inheritDoc
         */
        public function docs() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Api {
    /**
     * Content relations API implementation using the WordPress database object.
     */
    final class WpdbContentRelations implements \Inpsyde\MultilingualPress\Framework\Api\ContentRelations
    {
        /**
         * @var ActivePostTypes
         */
        private $activePostTypes;
        /**
         * @var ActiveTaxonomies
         */
        private $activeTaxonomies;
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var RelationshipsTable
         */
        private $relationshipsTable;
        /**
         * @var ContentRelationsTable
         */
        private $contentRelationshipTable;
        /**
         * @var Facade
         */
        private $cache;
        /**
         * @var CacheSettingsRepository
         */
        private $cacheSettingsRepository;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @var RelationshipMetaTable
         */
        protected $relationshipMetaTable;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Database\Table\ContentRelationsTable $contentRelationshipTable, \Inpsyde\MultilingualPress\Database\Table\RelationshipsTable $relationshipsTable, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $activeTaxonomies, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Database\Table\RelationshipMetaTable $relationshipMetaTable)
        {
        }
        /**
         * @inheritdoc
         */
        public function createRelationship(array $contentIds, string $type) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteAllRelationsForInvalidContent(string $type) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteAllRelationsForInvalidSites() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteAllRelationsForSite(int $siteId) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteRelation(array $contentIds, string $type) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function duplicateRelations(int $sourceSiteId, int $targetSiteId) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function contentId(int $relationshipId, int $siteId) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function contentIdForSite(int $siteId, int $contentId, string $type, int $targetSiteId) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function contentIds(int $relationshipId) : array
        {
        }
        /**
         * @inheritdoc
         */
        public function relations(int $siteId, int $contentId, string $type) : array
        {
        }
        /**
         * @inheritdoc
         */
        public function relationshipId(array $contentIds, string $type, bool $create = false) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function hasSiteRelations(int $siteId, string $type = '') : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function relateAllPosts(int $sourceSite, int $targetSite) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function relateAllTerms(int $sourceSite, int $targetSite) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function relateAllComments(int $sourceSite, int $targetSite) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function saveRelation(int $relationshipId, int $siteId, int $contentId) : bool
        {
        }
        /**
         * Creates a new relationship for the given type.
         *
         * @param string $type
         * @return int
         * @throws NonexistentTable
         */
        private function createRelationshipForType(string $type) : int
        {
        }
        /**
         * Deletes the relation for the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @param bool $delete
         * @return bool
         * @throws NonexistentTable
         */
        private function deleteRelationForSite(int $relationshipId, int $siteId, bool $delete = true) : bool
        {
        }
        /**
         * Removes the relationship as well as all relations with the given relationship ID.
         *
         * @param int $relationshipId
         * @return bool
         * @throws NonexistentTable
         */
        private function deleteRelationship(int $relationshipId) : bool
        {
        }
        /**
         * Returns the IDs of all existing content elements of the given type in the current site.
         *
         * @param string $type
         * @return int[]
         */
        private function existingContentIds(string $type) : array
        {
        }
        /**
         * Returns the IDs of the posts to relate for the current site.
         *
         * @return int[]
         */
        private function postIdsToRelate() : array
        {
        }
        /**
         * Returns the IDs of the comments to relate for the given site.
         *
         * @param int $siteId The site ID for which to get the comments.
         * @return int[] A list of comment IDs.
         */
        protected function commentIdsToRelate(int $siteId) : array
        {
        }
        /**
         * Returns the relationship ID for the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return int
         */
        private function multipleRelationshipIdFor(array $contentIds, string $type) : int
        {
        }
        /**
         * Returns the relationship ID for the given arguments.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @return int
         * @throws NonexistentTable
         */
        private function singleRelationshipIdFor(int $siteId, int $contentId, string $type) : int
        {
        }
        /**
         * Returns the relationship IDs for the site with the given ID.
         *
         * @param int $siteId
         * @return int[]
         * @throws NonexistentTable
         */
        private function relationshipIdsBySiteId(int $siteId) : array
        {
        }
        /**
         * Returns the relationship IDs for the given type.
         *
         * @param string $type
         * @return int[]
         * @throws NonexistentTable
         */
        private function relationshipIdsByType(string $type) : array
        {
        }
        /**
         * Return the content type for the relationship with the given ID.
         *
         * @param int $relationshipId
         * @return string
         * @throws NonexistentTable
         */
        private function relationshipType(int $relationshipId) : string
        {
        }
        /**
         * Returns the IDs of the terms to relate for the current site.
         *
         * @return int[]
         */
        private function termTaxonomyIdsToRelate() : array
        {
        }
        /**
         * Checks if the site with the given ID has any relations of the given content type.
         *
         * @param int $siteId
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        private function hasSiteRelationsOfType(int $siteId, string $type) : bool
        {
        }
        /**
         * Inserts a new relation with the given values.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @param int $contentId
         * @return bool
         * @throws NonexistentTable
         */
        private function insertRelation(int $relationshipId, int $siteId, int $contentId) : bool
        {
        }
    }
    /**
     * Languages API implementation using the WordPress database object.
     */
    final class WpdbLanguages implements \Inpsyde\MultilingualPress\Framework\Api\Languages
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var string[]
         */
        private $fields;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @var Table
         */
        private $table;
        /**
         * @var LanguageFactory
         */
        private $languageFactory;
        /**
         * @param \wpdb $wpdb
         * @param Table $table
         * @param SiteSettingsRepository $siteSettingsRepository
         * @param LanguageFactory $languageFactory
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory $languageFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteLanguage(int $id) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function allLanguages() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function allAssignedLanguages() : array
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function languageBy(string $column, $value) : \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * @inheritdoc
         */
        public function insertLanguage(array $languageData) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function updateLanguage(int $id, array $languageData) : bool
        {
        }
        /**
         * @param array $languageData
         * @return array
         */
        private function ensureRequiredData(array $languageData) : array
        {
        }
        /**
         * Return language ID if the language identified by language data exists already, 0 otherwise.
         *
         * @param array $languageData
         * @return int
         * @throws NonexistentTable
         */
        private function languageIdFromData(array $languageData) : int
        {
        }
        /**
         * Returns a new language object, instantiated with the given data.
         *
         * @param array $data
         * @return Language
         */
        private function languageFromData(array $data) : \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * Returns an array with column names as keys and the individual printf specification as value.
         *
         * The're a lot more specifications, but we don't need more than telling a string from an int.
         *
         * @param Table $table
         * @return string[]
         */
        private function extractFieldSpecificationsFromTable(\Inpsyde\MultilingualPress\Framework\Database\Table $table) : array
        {
        }
        /**
         * Returns an array with the according specifications for all fields included in the given language.
         *
         * @param array $language
         * @return array
         */
        private function fieldSpecifications(array $language) : array
        {
        }
    }
    /**
     * Site relations API implementation using the WordPress database object.
     */
    final class WpdbSiteRelations implements \Inpsyde\MultilingualPress\Framework\Api\SiteRelations
    {
        /**
         * @var wpdb
         */
        private $wpdb;
        /**
         * @var Table
         */
        private $table;
        /**
         * @var Facade
         */
        private $cache;
        /**
         * @var CacheSettingsRepository
         */
        private $cacheSettingsRepository;
        /**
         * @param \wpdb $wpdb
         * @param Table $table
         * @param Facade $cache
         * @param CacheSettingsRepository $cacheSettingsRepository
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteRelation(int $sourceSite, int $targetSite = 0) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function allRelations() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function relatedSiteIds(int $siteId, bool $includeSite = false) : array
        {
        }
        /**
         * @inheritdoc
         */
        public function insertRelations(int $baseSiteId, array $siteIds) : int
        {
        }
        /**
         * @inheritdoc
         */
        public function relateSites(int $baseSiteId, array $siteIds) : int
        {
        }
        /**
         * Returns a (value1, value2) syntax string according to the given site IDs.
         *
         * @param int $site1
         * @param int $site2
         * @return string
         */
        private function valuePair(int $site1, int $site2) : string
        {
        }
        /**
         * Returns a formatted array with site relations included in the given query results.
         *
         * @param string[] $rows
         * @return int[][]
         */
        private function siteRelationsFromQueryResults(array $rows) : array
        {
        }
        /**
         * @return array
         */
        private function allSiteRelationsCache() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Asset {
    /**
     * Factory for various asset objects.
     */
    class AssetFactory
    {
        /**
         * @var string
         */
        private $internalScriptPath;
        /**
         * @var string
         */
        private $internalScriptUrl;
        /**
         * @var string
         */
        private $internalStylePath;
        /**
         * @var string
         */
        private $internalStyleUrl;
        /**
         * @param Locations $locations
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Locations $locations)
        {
        }
        /**
         * Returns a new script object, instantiated according to the given arguments.
         *
         * @param string $handle
         * @param string $file
         * @param string[] $dependencies
         * @param string|null $version
         * @return Script
         */
        public function createInternalScript(string $handle, string $file, array $dependencies = [], string $version = null) : \Inpsyde\MultilingualPress\Framework\Asset\Script
        {
        }
        /**
         * Returns a new style object, instantiated according to the given arguments.
         *
         * @param string $handle
         * @param string $file
         * @param string[] $dependencies
         * @param string|null $version
         * @param string $media
         * @return Style
         */
        public function createInternalStyle(string $handle, string $file, array $dependencies = [], string $version = null, string $media = 'all') : \Inpsyde\MultilingualPress\Framework\Asset\Style
        {
        }
    }
    /**
     * Service provider for all assets objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
    /**
     * Default script data type implementation.
     */
    final class StandardWpScript implements \Inpsyde\MultilingualPress\Framework\Asset\Script
    {
        /**
         * @var array[]
         */
        private $data = [];
        /**
         * @var string[]
         */
        private $dependencies;
        /**
         * @var string
         */
        private $handle;
        /**
         * @var AssetLocation
         */
        private $location;
        /**
         * @var string|null
         */
        private $version;
        /**
         * @param string $handle
         * @param AssetLocation $location
         * @param array $dependencies
         * @param string|null $version
         */
        public function __construct(string $handle, \Inpsyde\MultilingualPress\Framework\Asset\AssetLocation $location, array $dependencies = [], string $version = null)
        {
        }
        /**
         * @inheritdoc
         */
        public function dependencies() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function handle() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function location() : \Inpsyde\MultilingualPress\Framework\Asset\AssetLocation
        {
        }
        /**
         * @inheritdoc
         * @return string|null
         */
        public function version()
        {
        }
        /**
         * @inheritdoc
         */
        public function __toString() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function addData(string $jsObjectName, array $jsObjectData) : \Inpsyde\MultilingualPress\Framework\Asset\Script
        {
        }
        /**
         * Clears the data so it won't be output another time.
         *
         * @return Script
         */
        public function clearData() : \Inpsyde\MultilingualPress\Framework\Asset\Script
        {
        }
        /**
         * Returns all data to be made available for the script.
         *
         * @return array[]
         */
        public function data() : array
        {
        }
    }
    /**
     * Default style data type implementation.
     */
    final class StandardWpStyle implements \Inpsyde\MultilingualPress\Framework\Asset\Style
    {
        /**
         * @var string[]
         */
        private $dependencies;
        /**
         * @var string
         */
        private $handle;
        /**
         * @var string
         */
        private $media;
        /**
         * @var AssetLocation
         */
        private $location;
        /**
         * @var string|null
         */
        private $version;
        /**
         * @param string $handle
         * @param AssetLocation $location
         * @param array $dependencies
         * @param string|null $version
         * @param string $media
         */
        public function __construct(string $handle, \Inpsyde\MultilingualPress\Framework\Asset\AssetLocation $location, array $dependencies = [], string $version = null, string $media = 'all')
        {
        }
        /**
         * @inheritdoc
         */
        public function dependencies() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function handle() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function location() : \Inpsyde\MultilingualPress\Framework\Asset\AssetLocation
        {
        }
        /**
         * @inheritdoc
         * @return string|null
         */
        public function version()
        {
        }
        /**
         * @inheritdoc
         */
        public function __toString() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function addConditional(string $conditional) : \Inpsyde\MultilingualPress\Framework\Asset\Style
        {
        }
        /**
         * @inheritdoc
         */
        public function media() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Attachment {
    /**
     * Class AttachmentData
     */
    class AttachmentData
    {
        /**
         * @var \WP_Post
         */
        private $post;
        /**
         * @var array
         */
        private $meta;
        /**
         * @var string
         */
        private $filePath;
        /**
         * AttachmentData constructor.
         * @param \WP_Post $post
         * @param array $meta
         * @param string $filePath
         */
        public function __construct(\WP_Post $post, array $meta, string $filePath)
        {
        }
        /**
         * @return \WP_Post
         */
        public function post() : \WP_Post
        {
        }
        /**
         * @return array
         */
        public function meta() : array
        {
        }
        /**
         * @return string
         */
        public function filePath() : string
        {
        }
    }
    /**
     * Class Collection
     * @package Inpsyde\MultilingualPress\Attachment
     */
    class Collection
    {
        const DEFAULT_LIMIT = 0;
        const DEFAULT_OFFSET = 0;
        const META_KEY_ATTACHMENTS = '_wp_attachment_metadata';
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * Attachments constructor.
         * @param \wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Extracts all registered attachment paths from the database as an array with directories
         * relative to uploads as keys, and arrays of file paths as values.
         *
         * Only files referenced in the database are trustworthy, and will therefore get copied.
         *
         * @param int $offset
         * @param int $limit
         * @return array
         */
        public function list(int $offset = self::DEFAULT_OFFSET, int $limit = self::DEFAULT_LIMIT) : array
        {
        }
        /**
         * @return int
         */
        public function count() : int
        {
        }
        /**
         * @param stdClass $metadata
         * @return array
         */
        private function filesPaths(\stdClass $metadata) : array
        {
        }
        /**
         * Get the Attachment backup file.
         *
         * When the image is edited in WordPress media editor, the original image will be backed up and
         * stored in _wp_attachment_backup_sizes meta, so the users can restore the original image later.
         * When copying the site attachments to a new site with MLP and "Based On Site" option we also need to check the
         * backup files and copy them as well, so in a new site the users will be also able to restore the original images.
         *
         * @param int $postId The attachment post ID
         * @return string The backed up file.
         */
        protected function backupFile(int $postId) : string
        {
        }
    }
    /**
     * MultilingualPress Attachment Copier
     */
    class Copier
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var Filesystem
         */
        private $filesystem;
        /**
         * @param \wpdb $wpdb
         * @param Filesystem $filesystem
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Filesystem $filesystem)
        {
        }
        /**
         * Copy attachments from source site to the give remote site using a list of attachment ids
         *
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param array $sourceAttachmentIds
         * @return array
         */
        public function copyById(int $sourceSiteId, int $remoteSiteId, array $sourceAttachmentIds) : array
        {
        }
        /**
         * Copy attachments from source site to remote site using source attachments data
         *
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param array $sourceAttachmentsData
         * @return array
         */
        public function copyByAttachmentsData(int $sourceSiteId, int $remoteSiteId, array $sourceAttachmentsData) : array
        {
        }
        /**
         * Copy attachment file to the remote upload dir and create a new attachment post
         *
         * @param int $remoteSiteId
         * @param AttachmentData[] $sourceAttachmentsData
         * @return array
         */
        private function copyToRemoteSite(int $remoteSiteId, \Inpsyde\MultilingualPress\Attachment\AttachmentData ...$sourceAttachmentsData) : array
        {
        }
        /**
         * Update the remote attachment post meta data with data provided by the given source attachment
         *
         * @param AttachmentData $sourceAttachmentData
         * @param int $remoteAttachmentId
         * @return int
         */
        private function updateAttachmentPostMeta(\Inpsyde\MultilingualPress\Attachment\AttachmentData $sourceAttachmentData, int $remoteAttachmentId) : int
        {
        }
        /**
         * Create an attachment post by the attachment path
         *
         * @param AttachmentData $sourceAttachmentData
         * @param string $remoteAttachmentRealPath
         * @param string $remoteAttachmentUrl
         * @return int
         */
        private function createAttachmentPostByPath(\Inpsyde\MultilingualPress\Attachment\AttachmentData $sourceAttachmentData, string $remoteAttachmentRealPath, string $remoteAttachmentUrl) : int
        {
        }
        /**
         * Retrieve the attachments post and files path
         * The items contains the post and the real path of the attachment
         *
         * @param int[] $attachmentIds
         * @param string $uploadDir
         * @return AttachmentData[]
         */
        private function sourceAttachments(array $attachmentIds, string $uploadDir) : array
        {
        }
        /**
         * Copy the attachment meta from the source give post to the remote attachment
         *
         * @param AttachmentData $sourceAttachmentData
         * @param int $remoteAttachmentId
         */
        private function copyMetaFromSourceAttachment(\Inpsyde\MultilingualPress\Attachment\AttachmentData $sourceAttachmentData, int $remoteAttachmentId)
        {
        }
        /**
         * Retrieve the meta by the given attachment post
         *
         * @param \WP_Post $attachment
         * @return array
         */
        private function attachmentMeta(\WP_Post $attachment) : array
        {
        }
        /**
         * Check if an attachment post is a valid attachment
         *
         * @param \WP_Post $attachment
         * @return bool
         */
        private function isLocalAttachment(\WP_Post $attachment) : bool
        {
        }
        /**
         * Retrieve the path by the give attachment id
         *
         * @param int $attachmentId
         * @return string
         */
        private function attachmentPath(int $attachmentId) : string
        {
        }
        /**
         * Switch blog if needed
         *
         * @param int $remoteSiteId
         * @return int
         */
        private function maybeSwitchSite(int $remoteSiteId) : int
        {
        }
        /**
         * Restore blog if needed
         *
         * @param int $originalSiteId
         * @return bool
         */
        private function maybeRestoreSite(int $originalSiteId) : bool
        {
        }
        /**
         * Ensure attachment ids are valid integer values
         *
         * @param array $attachmentIds
         * @return array
         */
        private function ensureAttachmentIds(array $attachmentIds) : array
        {
        }
        /**
         * Require functions to work with attachments
         *
         * @return void
         */
        private function requireAttachmentFunctions()
        {
        }
        /**
         * Retrieve the attachment id of the existing attachment based on the file name
         *
         * @param string $attachmentPath
         * @return int
         */
        private function existingAttachmentId(string $attachmentPath) : int
        {
        }
    }
    /**
     * Class DataBaseDataReplacer
     * @package Inpsyde\MultilingualPress\Attachment
     */
    class DatabaseDataReplacer
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        const FILTER_TABLES = 'multilingualpress.database_data_replacer_tables';
        /**
         * @var TableStringReplacer
         */
        private $tableStringReplacer;
        /**
         * @var wpdb
         */
        private $wpdb;
        /**
         * @var BasePathAdapter
         */
        private $basePathAdapter;
        /**
         * UrlDataBaseReplacer constructor.
         * @param wpdb $wpdb
         * @param TableStringReplacer $tableStringReplacer
         * @param BasePathAdapter $basePathAdapter
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\TableStringReplacer $tableStringReplacer, \Inpsyde\MultilingualPress\Framework\BasePathAdapter $basePathAdapter)
        {
        }
        /**
         * Updates attachment URLs according to the given arguments.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         */
        public function replaceUrlsForSites(int $sourceSiteId, int $targetSiteId)
        {
        }
    }
    /**
     * Class Duplicator
     * @package Inpsyde\MultilingualPress\Attachment
     */
    class Duplicator
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        const FILTER_ATTACHMENTS_PATHS = 'multilingualpress.attachments_to_target_paths';
        /**
         * @var BasePathAdapter
         */
        private $basePathAdapter;
        /**
         * @var Filesystem
         */
        private $filesystem;
        /**
         * @param BasePathAdapter $basePathAdapter
         * @param Filesystem $filesystem
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\BasePathAdapter $basePathAdapter, \Inpsyde\MultilingualPress\Framework\Filesystem $filesystem)
        {
        }
        /**
         * Copies all attachment files of the site with given ID to the current site.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         * @param array $attachmentsPaths
         * @return bool
         */
        public function duplicateAttachmentsFromSite(int $sourceSiteId, int $targetSiteId, array $attachmentsPaths) : bool
        {
        }
        /**
         * Copies all given files from one site to another.
         *
         * @param string $sourceDir
         * @param array $filepaths
         * @param string $destinationDir
         * @return bool
         */
        private function copyDir(string $sourceDir, array $filepaths, string $destinationDir) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Auth {
    /**
     * Class ServiceProvider
     * @package Inpsyde\MultilingualPress\Auth
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * @inheritDoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Cache {
    /**
     * Class NavMenuItemSerializer
     * @package Inpsyde\MultilingualPress\Cache
     */
    /**
     * Class NavMenuItemSerializer
     * @package Inpsyde\MultilingualPress\Cache
     */
    class NavMenuItemsSerializer
    {
        const ALLOWED_MENU_ITEM_FILTER = 'mlp.cache.allowed-nav-menu-items';
        /**
         * @var string[]
         */
        const POST_ALLOWED_PROPERTIES = ['ID', 'filter'];
        /**
         * @var string []
         */
        const MENU_ITEM_ALLOWED_PROPERTIES = [['menu_item_parent', 'int', 0], ['db_id', 'int', 0], ['object_id', 'int', 0], ['object', 'string', ''], ['type', 'string', ''], ['type_label', 'string', ''], ['title', 'string', ''], ['url', 'string', ''], ['target', 'string', ''], ['attr_title', 'string', ''], ['description', 'string', ''], ['classes', 'array', ''], ['xfn', 'string', ''], ['current', 'bool', false], ['current_item_ancestor', 'bool', false], ['current_item_parent', 'bool', false]];
        /**
         * @var \WP_Post[]
         */
        private $unserialized;
        /**
         * @var array[]
         */
        private $serialized;
        /**
         * @param \WP_Post[] $items
         * @return NavMenuItemsSerializer
         */
        public static function fromWpPostItems(\WP_Post ...$items) : \Inpsyde\MultilingualPress\Cache\NavMenuItemsSerializer
        {
        }
        /**
         * @param array[] $items
         * @return NavMenuItemsSerializer
         */
        public static function fromSerialized(array ...$items) : \Inpsyde\MultilingualPress\Cache\NavMenuItemsSerializer
        {
        }
        /**
         * @param array|null $unserialized
         * @param array|null $serialized
         */
        private function __construct(array $unserialized = null, array $serialized = null)
        {
        }
        /**
         * @return \WP_Post[]
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function unserialize() : array
        {
        }
        /**
         * @return array[]
         */
        public function serialize() : array
        {
        }
        /**
         * @param \WP_Post $post
         * @return array
         */
        private function splitPostProperties(\WP_Post $post) : array
        {
        }
        /**
         * @param array $array
         * @param string $key
         * @param string $type
         * @param $default
         * @return mixed
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        private function extractValue(array $array, string $key, string $type, $default)
        {
        }
        /**
         * @return array
         */
        private function menuItemProperties() : array
        {
        }
        private static function filterAllowedProperties() : array
        {
        }
    }
    /**
     * Service provider for all cache objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        const CACHE_SETTINGS_NONCE = 'update_internal_cache_settings';
        /**
         * @inheritdoc
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerSettings(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        public function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * MultilingualPress "Alternative language title" site setting.
     */
    final class AltLanguageTitleSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var string
         */
        private $option;
        /**
         * @var SettingsRepository
         */
        private $repository;
        /**
         * @param string $option
         * @param Nonce $nonce
         * @param SettingsRepository $repository
         */
        public function __construct(string $option, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar\SettingsRepository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Hreflang site setting.
     */
    final class HreflangSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var array<SettingOptionInterface>
         */
        private $options;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * Hreflang site setting constructor.
         *
         * @param array<SettingOptionInterface> $options
         * @param SiteRelations $siteRelations
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(array $options, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * Render the xDefault selectbox
         *
         * @param int $siteId
         * @param SettingOptionInterface $option
         */
        protected function renderXDefault(int $siteId, \Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface $option) : void
        {
        }
        /**
         * Render the Display Type radio buttons
         *
         * @param int $siteId
         * @param SettingOptionInterface $option
         */
        protected function renderDisplayType(int $siteId, \Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface $option) : void
        {
        }
        /**
         * Retrieve all the related sites according to the given parameter
         *
         * @param int $siteId
         * @return array
         */
        private function relatedSites(int $siteId) : array
        {
        }
        /**
         * Create the setting option name
         *
         * @param string $optionName The option name
         * @return string The setting option name
         */
        protected function settingOptionName(string $optionName) : string
        {
        }
    }
    /**
     * Class LanguageInstaller
     */
    class LanguageInstaller
    {
        /**
         * @param string $languageCode The WordPress language code
         */
        public function install(string $languageCode)
        {
        }
    }
    /**
     * MultilingualPress "Language" site setting.
     */
    final class LanguageSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-site-language';
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * Renders the option tags.
         *
         * @param int $siteId
         */
        private function renderOptions(int $siteId)
        {
        }
        /**
         * Returns the current MultilingualPress or WordPress language for the site with the given ID.
         *
         * @param int $siteId
         * @return string
         */
        private function currentSiteLanguage(int $siteId) : string
        {
        }
    }
    class LanguagesAjaxSearch
    {
        const ACTION = 'multilingualpress_search_languages';
        const SEARCH_PARAM = 'search';
        /**
         * @var Request
         */
        private $request;
        /**
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return void
         */
        public function handle()
        {
        }
        /**
         * @param string $search
         * @param Language $language
         * @return array
         */
        private function foundItem(string $search, \Inpsyde\MultilingualPress\Framework\Language\Language $language) : array
        {
        }
    }
    class LicenseSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Activator
         */
        private $activator;
        /**
         * @param Activator $activator
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\License\Api\Activator $activator, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function render()
        {
        }
        /**
         * @param array $licenseOption
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function activateView(array $licenseOption)
        {
        }
        /**
         * @param array $licenseOption
         */
        protected function deactivateView(array $licenseOption)
        {
        }
        /**
         * @param string $licenseApiKey
         * @return string
         */
        protected function displayLastDigits(string $licenseApiKey) : string
        {
        }
        /**
         * @param string $attr
         * @return void Echo the attribute
         */
        private function inputNameAttr(string $attr)
        {
        }
    }
    class LicenseSettingsUpdater
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Activator
         */
        private $activator;
        /**
         * @param Activator $activator
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\License\Api\Activator $activator, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
        /**
         * @param Request $request
         * @return array
         */
        private function licenseFromRequest(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * @param string $requestType
         * @param License $license
         * @return array
         */
        private function requestTypeActivation(string $requestType, \Inpsyde\MultilingualPress\License\License $license) : array
        {
        }
    }
    /**
     * Module settings tab view.
     */
    final class ModuleSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        const ACTION_IN_MODULE_LIST = 'multilingualpress.in_module_list';
        const FILTER_SHOW_MODULE = 'multilingualpress.show_module';
        /**
         * @var ModuleManager
         */
        private $moduleManager;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param ModuleManager $moduleManager
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
        /**
         * Renders the markup for the given module.
         *
         * @param Module $module
         */
        private function renderModule(\Inpsyde\MultilingualPress\Framework\Module\Module $module)
        {
        }
    }
    /**
     * Module settings updater.
     */
    class ModuleSettingsUpdater
    {
        const ACTION_SAVE_MODULES = 'multilingualpress.save_modules';
        const NAME_MODULE_SETTINGS = 'multilingualpress_modules';
        /**
         * @var ModuleManager
         */
        private $moduleManager;
        /**
         * @var array
         */
        private $modules = [];
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param ModuleManager $moduleManager
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Updates the plugin settings according to the data in the request.
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
        /**
         * Updates a single module according to the data in the request.
         *
         * @param string $moduleId
         */
        private function updateModule(string $moduleId)
        {
        }
    }
    /**
     * New site settings section view model implementation.
     */
    final class NewSiteSettings implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel
    {
        const SECTION_ID = 'mlp-new-site-settings';
        /**
         * @var SiteSettingView
         */
        private $view;
        /**
         * @param SiteSettingView $view
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view)
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function renderView(int $siteId) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Plugin settings page view.
     */
    final class PluginSettingsPageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        const QUERY_ARG_TAB = 'tab';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var ServerRequest
         */
        private $request;
        /**
         * @var array
         */
        private $settingTabs;
        /**
         * @param Nonce $nonce
         * @param ServerRequest $request
         * @param array $settingTabs
         * @throws \UnexpectedValueException
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, array $settingTabs)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
        /**
         * Returns the slug of the active tab.
         *
         * @return string
         */
        private function currentlyActiveTab() : string
        {
        }
        /**
         * Renders the active tab content.
         */
        private function renderContent()
        {
        }
        /**
         * Renders the form.
         */
        private function renderForm()
        {
        }
        /**
         * Renders the tabbed navigation.
         */
        private function renderTabs()
        {
        }
        /**
         * Renders the given tab.
         *
         * @param SettingsPageTab $tab
         * @param string $slug
         * @param string $active
         */
        private function renderTab(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab $tab, string $slug, string $active)
        {
        }
        /**
         * Ensure the given array contains items that are instances of SettingsPageTab
         *
         * @param array $settingsTab
         * @throws UnexpectedValueException
         */
        private function assertSettingTabs(array $settingsTab)
        {
        }
    }
    /**
     * Plugin settings updater.
     */
    class PluginSettingsUpdater
    {
        const ACTION = 'update_multilingualpress_settings';
        const ACTION_UPDATE_PLUGIN_SETTINGS = 'multilingualpress.update_plugin_settings';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param Nonce $nonce
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Updates the plugin settings according to the data in the request.
         */
        public function updateSettings()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Pointers {
    /**
     * WordPress Internal Pointers manager.
     */
    class Pointers
    {
        const USER_META_KEY = '_dismissed_mlp_pointers';
        const ACTION_AFTER_POINTERS_CREATED = 'multilingualpress.after_pointers_created';
        /**
         * @var AssetManager
         */
        private $assetManager;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var Repository
         */
        private $repository;
        /**
         * @param Request $request
         * @param Repository $repository
         * @param AssetManager $assetManager
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository $repository, \Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager)
        {
        }
        /**
         * @return void
         * @throws AssetException
         */
        public function createPointers()
        {
        }
        /**
         * @param array $pointers
         * @param string $ajaxAction
         * @return void
         * @throws AssetException
         */
        public function enqueuePointers(array $pointers, string $ajaxAction)
        {
        }
        /**
         * @return void
         */
        public function dismiss()
        {
        }
        /**
         * @param array $pointers
         * @param array $dismissedPointers
         * @return bool
         */
        private function currentPointersDismissed(array $pointers, array $dismissedPointers) : bool
        {
        }
    }
    /**
     * Pointers Repository.
     */
    class Repository
    {
        /**
         * @var array
         */
        private $pointers;
        /**
         * @var array
         */
        private $actions;
        /**
         * @param string $screen
         * @param string $key
         * @param string $target
         * @param string $next
         * @param array $nextTrigger
         * @param array $options
         * @return $this
         */
        public function registerForScreen(string $screen, string $key, string $target, string $next, array $nextTrigger, array $options) : \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository
        {
        }
        /**
         * @param string $screen
         * @param string $action
         * @return $this
         */
        public function registerActionForScreen(string $screen, string $action) : \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository
        {
        }
        /**
         * @param string $screen
         * @return array
         */
        public function forScreen(string $screen) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * Post type settings tab view.
     */
    final class PostTypeSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var PostTypeRepository
         */
        private $repository;
        /**
         * @param PostTypeRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
        /**
         * Returns the input ID for the given post type slug and settings field name.
         *
         * @param string $postTypeSlug
         * @param string $filedName
         * @return string
         */
        private function fieldId(string $postTypeSlug, string $filedName = '') : string
        {
        }
        /**
         * Returns the input name for the given post type slug and settings field name.
         *
         * @param string $slug
         * @param string $field
         * @return string
         */
        private function fieldName(string $slug, string $field) : string
        {
        }
        /**
         * Renders the table headings.
         */
        private function renderTableHeadings()
        {
        }
        /**
         * Renders a table row element according to the given data.
         *
         * @param \WP_Post_Type $postType
         * @param string $slug
         */
        private function renderTableRow(\WP_Post_Type $postType, string $slug)
        {
        }
    }
    /**
     * Post type settings updater.
     */
    class PostTypeSettingsUpdater
    {
        const SETTINGS_NAME = 'post_type_settings';
        const SETTINGS_FIELD_ACTIVE = 'active';
        const SETTINGS_FIELD_PERMALINKS = 'permalinks';
        const SETTINGS_FIELD_SKIN = 'ui';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var PostTypeRepository
         */
        private $repository;
        /**
         * @param PostTypeRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Updates the post type settings.
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
        /**
         * @param string $slug
         * @param array $settings
         * @return array
         */
        private function dataForPostType(string $slug, array $settings) : array
        {
        }
    }
    class PostTypeSlugSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-post-type-slugs';
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @var PostTypeRepository
         */
        private $postTypeRepository;
        /**
         * @var \WP_Post_Type
         */
        private $postType;
        /**
         * @param SiteSettingsRepository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $repository, \Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository, \WP_Post_Type $postType)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * @param string $postType
         * @return string
         */
        private function fieldName(string $postType) : string
        {
        }
    }
    class PostTypeSlugsSettingsRepository
    {
        const POST_TYPE_SLUGS = 'mlp_site_post_type_slugs';
        const OPTION = 'multilingualpress_post_type_slugs_translation';
        /**
         * Retrieve the post type slugs for the site with the given ID.
         *
         * @param int|null $siteId
         * @return array
         */
        public function postTypeSlugs(int $siteId = null) : array
        {
        }
        /**
         * Update the post type slugs for the site with the given ID.
         *
         * @param array $slugs
         * @param int|null $siteId
         * @return bool
         */
        public function updatePostTypeSlugs(array $slugs, int $siteId = null) : bool
        {
        }
        /**
         * @return array
         */
        private function allSettings() : array
        {
        }
        /**
         * @param array $slugs
         * @param int|null $siteId
         * @return bool
         */
        private function updateSetting(array $slugs, int $siteId = null) : bool
        {
        }
    }
    /**
     * Class PostTypeSlugsSettingsSectionView
     */
    final class PostTypeSlugsSettingsSectionView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        const ACTION_AFTER = 'multilingualpress.after_permalink_site_settings';
        const ACTION_BEFORE = 'multilingualpress.before_permalink_site_settings';
        /**
         * @var SiteSettingsSectionViewModel
         */
        private $model;
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId) : bool
        {
        }
    }
    /**
     * Class PostTypeSlugsSettingsTabView
     */
    final class PostTypeSlugsSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var SettingsPageTabData
         */
        private $data;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var SiteSettingView
         */
        private $view;
        /**
         * @param SettingsPageTabData $data
         * @param SiteSettingView $view
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
    }
    /**
     * Class PostTypeSlugsSettingsUpdateRequestHandler
     */
    class PostTypeSlugsSettingsUpdateRequestHandler
    {
        const ACTION = 'update_multilingualpress_post_type_slugs_site_settings';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var SiteSettingsUpdatable
         */
        private $updater;
        /**
         * @param PostTypeSlugsSettingsUpdater $updater
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsUpdater $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest()
        {
        }
    }
    /**
     * Class PostTypeSlugsSettingsUpdater
     */
    class PostTypeSlugsSettingsUpdater
    {
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param PostTypeSlugsSettingsRepository $repository
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @param int $siteId
         */
        public function updateSettings(int $siteId)
        {
        }
        /**
         * Update the Translation of Post Type Slugs for the site with the given ID according to request.
         *
         * @param int $siteId
         */
        private function updatePostTypeSlugs(int $siteId)
        {
        }
    }
    /**
     * MultilingualPress "Relationships" site setting.
     */
    final class RelationshipsSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-site-relations';
        /**
         * @var SiteSettingsRepository
         */
        private $settings;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @param SiteSettingsRepository $settings
         * @param SiteRelations $siteRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $settings, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * Render the relationships bulk actions.
         *
         * @return void
         */
        private function renderRelationshipsBulkActions()
        {
        }
        /**
         * Renders the relationships.
         *
         * @param int $baseSiteId
         * @param array $relatedIds
         * @return void
         */
        private function renderRelationships(int $baseSiteId, array $relatedIds)
        {
        }
    }
    /**
     * Class Screen
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class Screen
    {
        private static $screen;
        /**
         * @return bool
         */
        public static function isNetworkSite() : bool
        {
        }
        /**
         * @return bool
         */
        public static function isNetworkNewSite() : bool
        {
        }
        /**
         * @return bool
         */
        public static function isMultilingualPressSettings() : bool
        {
        }
        /**
         * @return bool
         */
        public static function isEditPostsTable() : bool
        {
        }
        /**
         * @return bool
         */
        public static function isEditPost() : bool
        {
        }
        /**
         * @return bool
         */
        public static function isEditSite() : bool
        {
        }
        /**
         * @return bool
         */
        private static function currentScreen() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache {
    /**
     * Class CacheOptionNamesValidator
     * @package Inpsyde\MultilingualPress\Core\Admin\Settings\Cache
     */
    class CacheSettingNamesValidator
    {
        const ALLOWED_OPTIONS = [\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_GROUP_API_NAME => [\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_SEARCH_TRANSLATIONS_API_NAME, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_CONTENT_IDS_API_NAME, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_RELATIONS_API_NAME, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_HAS_SITE_RELATIONS_API_NAME, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_ALL_RELATIONS_API_NAME, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_RELATED_SITE_IDS_API_NAME], \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_GROUP_DATABASE_NAME => [\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_ALL_TABLES_DATABASE_NAME], \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_GROUP_NAV_MENU_NAME => [\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions::OPTION_ITEM_FILTER_NAV_MENU_NAME]];
        /**
         * @param array $settings
         * @return bool
         */
        public function allowed(array $settings) : bool
        {
        }
    }
    /**
     * Class CacheSettingsOptions
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsOptions
    {
        const OPTION_GROUP_API_NAME = 'api';
        const OPTION_GROUP_DATABASE_NAME = 'database';
        const OPTION_GROUP_NAV_MENU_NAME = 'nav_menu';
        const OPTION_SEARCH_TRANSLATIONS_API_NAME = 'api.translation';
        const OPTION_CONTENT_IDS_API_NAME = 'api.content_ids';
        const OPTION_RELATIONS_API_NAME = 'api.content_relations';
        const OPTION_HAS_SITE_RELATIONS_API_NAME = 'api.has_site_relations';
        const OPTION_ALL_RELATIONS_API_NAME = 'api.all_relations';
        const OPTION_RELATED_SITE_IDS_API_NAME = 'api.related_site_ids';
        const OPTION_ALL_TABLES_DATABASE_NAME = 'database.table_list';
        const OPTION_ITEM_FILTER_NAV_MENU_NAME = 'nav_menu.item_filter';
        /**
         * Retrieve Default Options
         *
         * Default options are also the list of the options it self not just default values.
         *
         * @return array
         */
        public function defaults() : array
        {
        }
        /**
         * Retrieve Information about the options such as
         * - Group Name
         * - Label for specific option
         * - Description for specific option
         *
         * @return array
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function info() : array
        {
        }
    }
    /**
     * Class CacheSettingsOptions
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsOptionsView
    {
        /**
         * @var CacheSettingsRepository
         */
        private $repository;
        /**
         * @var CacheSettingsOptions
         */
        private $cacheSettingsOptions;
        /**
         * CacheSettingsOptionsView constructor.
         * @param CacheSettingsRepository $repository
         * @param CacheSettingsOptions $cacheSettingsOptions
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $repository, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions)
        {
        }
        /**
         * Render the Options Markup
         *
         * @return void
         */
        public function render()
        {
        }
        /**
         * Render a Group of Options
         *
         * @param string $name
         * @param array $group
         */
        protected function renderGroup(string $name, array $group)
        {
        }
    }
    /**
     * Class CacheSettingsRepository
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsRepository
    {
        const OPTION_NAME = 'multilingualpress_internal_cache_setting';
        /**
         * @var CacheSettingsOptions
         */
        private $settingsOptions;
        /**
         * @var CacheSettingNamesValidator
         */
        private $cacheSettingNamesValidator;
        /**
         * CacheSettingsRepository constructor.
         * @param CacheSettingsOptions $cacheSettingsOptions
         * @param CacheSettingNamesValidator $cacheSettingNamesValidator
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingNamesValidator $cacheSettingNamesValidator)
        {
        }
        /**
         * Retrieve All Options
         *
         * @return array
         */
        public function all() : array
        {
        }
        /**
         * Get Single Cache Setting
         *
         * @param string $group
         * @param string $key
         * @return bool
         */
        public function get(string $group, string $key) : bool
        {
        }
        /**
         * Update Settings
         *
         * @param array $settings
         * @return bool
         * @throws DomainException
         */
        public function update(array $settings) : bool
        {
        }
        /**
         * Fill Options With Values From Database
         *
         * @return array
         */
        protected function optionsFromDatabase() : array
        {
        }
    }
    /**
     * Class CacheSettingsTabView
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var CacheSettingsOptionsView
         */
        private $options;
        /**
         * CacheSettingsTabView constructor.
         * @param CacheSettingsOptionsView $options
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptionsView $options, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritDoc
         */
        public function render()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Settings {
    /**
     * Interface SettingsUpdater
     * @package Inpsyde\MultilingualPress\Core\Admin\Settings
     */
    interface SettingsUpdater
    {
        /**
         * Update Settings
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool;
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache {
    /**
     * Class CacheSettingsUpdater
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsUpdater implements \Inpsyde\MultilingualPress\Core\Admin\Settings\SettingsUpdater
    {
        /**
         * @var CacheSettingsRepository
         */
        private $cacheSettingsRepository;
        /**
         * @var Auth
         */
        private $auth;
        /**
         * @var CacheSettingsOptions
         */
        private $cacheSettingsOptions;
        /**
         * CacheSettingsUpdater constructor.
         * @param CacheSettingsRepository $cacheSettingsRepository
         * @param Auth $auth
         * @param CacheSettingsOptions $cacheSettingsOptions
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Framework\Auth\Auth $auth, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions)
        {
        }
        /**
         * @inheritDoc
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
        /**
         * Retrieve Values From Request
         *
         * @param Request $request
         * @return array
         */
        protected function retrieveValueFromRequest(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * Normalize Values By Converting Strings into Boolean
         *
         * The submitted value for checkboxes it's usually a 'on' or nothing,
         * we don't want to have an 'on' value into the database because there's no
         * off for them, simply non submitted values are no in $_POST request so we
         * do not store them into database.
         *
         * @param array $settings
         * @return array
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        protected function normalizeValues(array $settings) : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Settings {
    /**
     * Add MultilingualPress Settings into WordPress site settings screen
     */
    class WordPressSettingsScreen
    {
        /**
         * @var array
         */
        private $settings;
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @param array<SiteSettingViewModel> $settings The list of settings
         * @param SiteSettingsRepository $repository
         */
        public function __construct(array $settings, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository)
        {
        }
        /**
         * Create the MultilingualPress settings in WordPress site settings screen
         *
         * @see https://developer.wordpress.org/reference/functions/add_settings_field/ add_settings_field
         */
        public function addSettings()
        {
        }
        /**
         * Update the MultilingualPress language
         *
         * When the WordPress settings are updated, we also need to update MultilingualPress language.
         * The MultilingualPress Language setting is added in WordPress General Settings screen.
         *
         * @see https://developer.wordpress.org/reference/hooks/update_option/ update_option
         *
         * @param string $option The option name
         * @param $oldValue (mixed) The old option value.
         * @param $value (mixed) The new option value.
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function saveSettings(string $option, $oldValue, $value)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * Site settings section view model implementation.
     */
    final class SiteSettings implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel
    {
        const ID = 'mlp-site-settings';
        /**
         * @var SiteSettingView
         */
        private $view;
        /**
         * @var AssetManager
         */
        private $assetManager;
        /**
         * SiteSettings constructor.
         * @param SiteSettingView $view
         * @param AssetManager $assetManager
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view, \Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager)
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : string
        {
        }
        /**
         * @inheritdoc
         * @throws AssetException
         */
        public function renderView(int $siteId) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    class SiteSettingsRepository
    {
        use \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepositoryTrait;
        const KEY_LANGUAGE = 'lang';
        const NAME_LANGUAGE = 'mlp_site_language';
        const NAME_LANGUAGE_TYPE = 'mlp_site_language_type';
        const NAME_RELATIONSHIPS = 'mlp_site_relations';
        const NAME_HREFLANG = 'multilingualpress_hreflang';
        const NAME_HREFLANG_XDEFAULT = 'xdefault';
        const NAME_HREFLANG_DISPLAY_TYPE = 'display_type';
        const OPTION = 'multilingualpress_site_settings';
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @var Facade
         */
        private $cache;
        /**
         * @param SiteRelations $siteRelations
         * @param Facade $cache
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache)
        {
        }
        /**
         * Returns an array with the IDs of all sites with an assigned language,
         * minus the given IDs, if any.
         *
         * @param int[] $exclude
         * @return int[]
         */
        public function allSiteIds(array $exclude = []) : array
        {
        }
        /**
         * Returns the site language of the site with the given ID, or the current site.
         *
         * @param int|null $siteId
         * @return string
         */
        public function siteLanguageTag(int $siteId = null) : string
        {
        }
        /**
         * Sets the language for the site with the given ID, or the current site.
         *
         * @param string $language
         * @param int|null $siteId
         * @return bool
         */
        public function updateLanguage(string $language, int $siteId = null) : bool
        {
        }
        /**
         * Sets the relationships for the site with the given ID, or the current site.
         *
         * @param int[]
         * @param int|null $baseSiteId
         * @return bool
         */
        public function relate(array $siteIds, int $baseSiteId = null) : bool
        {
        }
        /**
         * Updates Hreflang settings values.
         * @param array $hreflangSettings
         * @param int|null $siteId
         * @return bool
         */
        public function updateHreflangSettings(array $hreflangSettings, int $siteId = null) : bool
        {
        }
        /**
         * Get the value of Hreflang setting option
         *
         * @param int $siteId
         * @param string $optionName The Hreflang setting option name
         * @return string The value of Hreflang setting option
         */
        public function hreflangSettingForSite(int $siteId, string $optionName) : string
        {
        }
    }
    /**
     * Settings page view for the MultilingualPress site settings tab.
     */
    final class SiteSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var SettingsPageTabData
         */
        private $data;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var SiteSettingView
         */
        private $view;
        /**
         * @param SettingsPageTabData $data
         * @param SiteSettingView $view
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
    }
    /**
     * Request handler for site settings update requests.
     */
    class SiteSettingsUpdateRequestHandler
    {
        const ACTION = 'update_multilingualpress_site_settings';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var SiteSettingsUpdatable
         */
        private $updater;
        /**
         * @param SiteSettingsUpdatable $updater
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest()
        {
        }
    }
    /**
     * Site settings updater.
     */
    class SiteSettingsUpdater implements \Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable
    {
        const ACTION_DEFINE_INITIAL_SETTINGS = 'multilingualpress.define_initial_site_settings';
        const ACTION_UPDATE_SETTINGS = 'multilingualpress.update_site_settings';
        const VALUE_LANGUAGE_NONE = '-1';
        const NAME_SEARCH_ENGINE_VISIBILITY = 'mlp_search_engine_visibility';
        /**
         * @var SiteSettingsRepository
         */
        private $repository;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var LanguageInstaller
         */
        private $languageInstaller;
        /**
         * @param SiteSettingsRepository $repository
         * @param Request $request
         * @param LanguageInstaller $languageInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Core\Admin\LanguageInstaller $languageInstaller)
        {
        }
        /**
         * @inheritdoc
         */
        public function defineInitialSettings(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(int $siteId)
        {
        }
        /**
         * Returns the language value from the request.
         *
         * @return string
         */
        private function targetLanguage() : string
        {
        }
        /**
         * Updates the language for the site with the given ID according to request.
         *
         * @param int $siteId
         */
        private function updateLanguage(int $siteId)
        {
        }
        /**
         * Updates the relationships for the site with the given ID according to request.
         *
         * @param int $siteId
         */
        private function updateRelationships(int $siteId)
        {
        }
        /**
         * Updates the HrefLang settings.
         * @param int $siteId
         */
        private function updateHreflangSettings(int $siteId)
        {
        }
        /**
         * Updates the WordPress language for the site with the given ID according to request.
         *
         * @param int $siteId
         */
        private function updateWpLang(int $siteId)
        {
        }
        /**
         * Adapts the search engine visibility according to the setting included in the request.
         *
         * @param int $siteId
         */
        private function handleSearchEngineVisibility(int $siteId)
        {
        }
    }
    /**
     * Taxonomy settings tab view.
     */
    final class TaxonomySettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var TaxonomyRepository
         */
        private $repository;
        /**
         * @param TaxonomyRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render()
        {
        }
        /**
         * Returns the input ID for the given taxonomy slug and settings field name.
         *
         * @param string $slug
         * @param string $field
         * @return string
         */
        private function fieldId(string $slug, string $field = '') : string
        {
        }
        /**
         * Returns the input name for the given taxonomy slug and settings field name.
         *
         * @param string $slug
         * @param string $field
         * @return string
         */
        private function fieldName(string $slug, string $field) : string
        {
        }
        /**
         * Renders the table headings.
         */
        private function renderTableHeadings()
        {
        }
        /**
         * Renders a table row element according to the given data.
         *
         * @param \WP_Taxonomy $taxonomy
         * @param string $slug
         */
        private function renderTableRow(\WP_Taxonomy $taxonomy, string $slug)
        {
        }
    }
    /**
     * Taxonomy settings updater.
     */
    class TaxonomySettingsUpdater
    {
        const SETTINGS_NAME = 'taxonomy_settings';
        const SETTINGS_FIELD_ACTIVE = 'active';
        const SETTINGS_FIELD_SKIN = 'ui';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var TaxonomyRepository
         */
        private $repository;
        /**
         * @param TaxonomyRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Updates the taxonomy settings.
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : bool
        {
        }
        /**
         * @param string $slug
         * @param array $settings
         * @return array
         */
        private function dataForTaxonomy(string $slug, array $settings) : array
        {
        }
    }
    /**
     * WordPress "Language" site setting.
     */
    class WordPressLanguageSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'locale';
        /**
         * @var string
         */
        private $wordPressLanguageSettingMarkup;
        /**
         * @param string $wordPressLanguageSettingMarkup
         */
        public function __construct(string $wordPressLanguageSettingMarkup)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        protected function description() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Entity {
    /**
     * Simple read-only storage for post types active for MultilingualPress.
     */
    final class ActivePostTypes
    {
        const FILTER_ACTIVE_POST_TYPES = 'multilingualpress.active_post_types';
        /**
         * @var string[]
         */
        private $activePostTypeSlugs;
        /**
         * Returns the active post type slugs.
         *
         * @return string[]
         */
        public function names() : array
        {
        }
        /**
         * Returns the active post type objects.
         *
         * @return \WP_Post_Type[]
         */
        public function objects() : array
        {
        }
        /**
         * Checks if all given post type slugs are active.
         *
         * @param string[] ...$postTypeSlugs
         * @return bool
         */
        public function arePostTypesActive(string ...$postTypeSlugs) : bool
        {
        }
    }
    /**
     * Simple read-only storage for taxonomies active for MultilingualPress.
     */
    final class ActiveTaxonomies
    {
        const FILTER_ACTIVE_TAXONOMIES = 'multilingualpress.active_taxonomies';
        /**
         * @var array
         */
        private $activeTaxonomyNames;
        /**
         * Returns the allowed taxonomy names.
         *
         * @return string[]
         */
        public function names() : array
        {
        }
        /**
         * Returns the allowed taxonomy objects.
         *
         * @return \WP_Taxonomy[]
         */
        public function objects() : array
        {
        }
        /**
         * Returns true if given taxonomy names are allowed.
         *
         * @param string[] ...$taxonomySlugs
         * @return bool
         */
        public function areTaxonomiesActive(string ...$taxonomySlugs) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Frontend {
    /**
     * Alternate language controller.
     */
    class AltLanguageController
    {
        const FILTER_HREFLANG_TYPE = 'multilingualpress.hreflang_type';
        /**
         * @var int
         */
        private $type;
        public function __construct()
        {
        }
        /**
         * Registers the given renderer according to the given arguments.
         *
         * @param AltLanguageRenderer $renderer
         * @param string $action
         * @param int $priority
         * @param int $acceptedArgs
         * @return bool
         */
        public function registerRenderer(\Inpsyde\MultilingualPress\Core\Frontend\AltLanguageRenderer $renderer, string $action, int $priority = 10, int $acceptedArgs = 1) : bool
        {
        }
    }
    /**
     * Interface for all alternate language renderer implementations.
     */
    interface AltLanguageRenderer
    {
        const TYPE_HTTP_HEADER = 1;
        const TYPE_HTML_LINK_TAG = 2;
        /**
         * Returns the output type.
         *
         * @return int
         */
        public function type() : int;
        /**
         * Renders all available alternate languages.
         *
         * @param array ...$args
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function render(...$args);
    }
    /**
     * Alternate language HTML link tag renderer implementation.
     */
    final class AltLanguageHtmlLinkTagRenderer implements \Inpsyde\MultilingualPress\Core\Frontend\AltLanguageRenderer
    {
        public const FILTER_HREFLANG = 'multilingualpress.hreflang_html_link_tag';
        public const FILTER_RENDER_HREFLANG = 'multilingualpress.render_hreflang';
        protected const KSES_TAGS = ['link' => ['href' => true, 'hreflang' => true, 'rel' => true]];
        /**
         * @var AlternateLanguages
         */
        private $alternateLanguages;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @param AlternateLanguages $alternateLanguages
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Frontend\AlternateLanguages $alternateLanguages, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Renders all alternate languages as HTML link tags into the HTML head.
         *
         * @param array ...$args
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable WordPressVIPMinimum.Security.ProperEscapingFunction.notAttrEscAttr
         */
        public function render(...$args)
        {
        }
        /**
         * Returns the output type.
         *
         * @return int
         */
        public function type() : int
        {
        }
        /**
         * Retrieves the xDefault language tag(isoCode | bcp47tag).
         *
         * @param array<string, string> $alternateLanguages The map of connected language tags to their URLs.
         * @return string The xDefault language tag(isoCode | bcp47tag).
         * @throws NonexistentTable
         */
        private function xDefaultLanguage(array $alternateLanguages) : string
        {
        }
    }
    /**
     * Alternate language HTTP header renderer implementation.
     */
    final class AltLanguageHttpHeaderRenderer implements \Inpsyde\MultilingualPress\Core\Frontend\AltLanguageRenderer
    {
        const FILTER_HREFLANG = 'multilingualpress.hreflang_http_header';
        const FILTER_RENDER_HREFLANG = 'multilingualpress.render_hreflang';
        /**
         * @var AlternateLanguages
         */
        private $alternateLanguages;
        /**
         * @param AlternateLanguages $alternateLanguages
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Frontend\AlternateLanguages $alternateLanguages)
        {
        }
        /**
         * Renders all available alternate languages as Link HTTP headers.
         *
         * @param array ...$args
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function render(...$args)
        {
        }
        /**
         * Returns the output type.
         *
         * @return int
         */
        public function type() : int
        {
        }
    }
    /**
     * Alternate languages data object.
     */
    class AlternateLanguages implements \IteratorAggregate
    {
        const FILTER_HREFLANG_POST_STATUS = 'multilingualpress.hreflang_post_status';
        const FILTER_HREFLANG_TRANSLATIONS = 'multilingualpress.hreflang_translations';
        const FILTER_HREFLANG_URL = 'multilingualpress.hreflang_url';
        /**
         * @var SiteSettingsRepository
         */
        protected $siteSettingsRepository;
        /**
         * @var Translations
         */
        private $api;
        /**
         * @var string[]
         */
        private $urls;
        /**
         * @param Translations $api
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $api, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function getIterator() : \Traversable
        {
        }
        /**
         * Takes care that the alternate language URLs are available for use.
         */
        private function ensureUrls()
        {
        }
        /**
         * @param Translation $translation
         * @return string
         */
        public function getHreflangCode(\Inpsyde\MultilingualPress\Framework\Api\Translation $translation) : string
        {
        }
    }
    /**
     * Post type link URL filter.
     */
    final class PostTypeLinkUrlFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        /**
         * @var PostTypeRepository
         */
        private $postTypeRepository;
        /**
         * @param PostTypeRepository $postTypeRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository)
        {
        }
        /**
         * Filters the post type link URL and returns a query-based representation, if set for the
         * according post type.
         *
         * @param string $postLink
         * @param \WP_Post $post
         * @return string
         */
        public function unprettifyPermalink(string $postLink, \WP_Post $post) : string
        {
        }
        /**
         * Checks if the given post is a draft or pending.
         *
         * @param \WP_Post $post
         * @return bool
         */
        private function isDraftOrPending(\WP_Post $post) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core {
    /**
     * MultilingualPress-specific locations implementation.
     */
    class Locations
    {
        const TYPE_PATH = 'path';
        const TYPE_URL = 'url';
        /**
         * @var string[][]
         */
        private $locations = [];
        /**
         * Adds a new location according to the given arguments.
         *
         * @param string $name
         * @param string $path
         * @param string $url
         * @return Locations
         */
        public function add(string $name, string $path, string $url) : \Inpsyde\MultilingualPress\Core\Locations
        {
        }
        /**
         * Returns the location data according to the given arguments.
         *
         * @param string $name
         * @param string $type
         * @return string
         */
        public function valueFor(string $name, string $type) : string
        {
        }
        /**
         * Checks if a location with the given name exists.
         *
         * @param string $name
         * @return bool
         */
        public function contain(string $name) : bool
        {
        }
    }
    /**
     * MultilingualPress Modules Deactivator
     */
    class ModuleDeactivator
    {
        /**
         * @var ModuleManager
         */
        private $moduleManager;
        /**
         * ModuleDeactivator constructor
         *
         * @param ModuleManager $moduleManager
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        /**
         * Deactivate WooCommerce Module
         */
        public function deactivateWooCommerce()
        {
        }
    }
    /**
     * Type-safe post type repository implementation.
     */
    final class PostTypeRepository
    {
        const DEFAULT_SUPPORTED_POST_TYPES = ['page', 'post'];
        const FIELD_ACTIVE = 'active';
        const FIELD_PERMALINK = 'permalink';
        const OPTION = 'multilingualpress_post_types';
        const FILTER_PUBLIC_POST_TYPES = 'multilingualpress.public_post_types';
        const FILTER_ALL_AVAILABLE_POST_TYPES = 'multilingualpress.all_post_types';
        const FILTER_SUPPORTED_POST_TYPES = 'multilingualpress.supported_post_types';
        /**
         * @var \WP_Post_Type[]
         */
        private $postTypes;
        /**
         * Returns all post types that MultilingualPress is able to support.
         *
         * @return \WP_Post_Type[]
         */
        public function allAvailablePostTypes() : array
        {
        }
        /**
         * Returns all post types supported by MultilingualPress.
         *
         * @return string[]
         */
        public function supportedPostTypes() : array
        {
        }
        /**
         * Checks if the post type with the given slug is active.
         *
         * @param string $slug
         * @return bool
         */
        public function isPostTypeActive(string $slug) : bool
        {
        }
        /**
         * Checks if the post type with the given slug is set to be query-based.
         *
         * @param string $slug
         * @return bool
         */
        public function isPostTypeQueryBased(string $slug) : bool
        {
        }
        /**
         * Sets post type support according to the given settings.
         *
         * @param array $postTypes
         * @return bool
         */
        public function supportPostTypes(array $postTypes) : bool
        {
        }
        /**
         * Removes the support for all post types.
         *
         * @return bool
         */
        public function removeSupportForAllPostTypes() : bool
        {
        }
        /**
         * Returns a two-items array, where the first is a boolean indicating if
         * settings are found in database, the second is actual settings array.
         * Help disguising on-purpose empty array in db from a no-result.
         *
         * @return array
         */
        private function allSettings() : array
        {
        }
        /**
         * @param string $slug
         * @param string $field
         * @param mixed $default
         * @return array|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        private function settingFor(string $slug, string $field, $default = null)
        {
        }
    }
    /**
     * Service provider for all Core objects.
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        const FILTER_PLUGIN_LOCALE = 'plugin_locale';
        const FILTER_AVAILABLE_POST_TYPE_FOR_SETTINGS = 'multilingualpress.post_type_slugs_settings';
        const FILTER_HTTP_CLIENT_CONFIG = 'multilingualpress.http_client_config';
        public const FILTER_ADMIN_ALLOWED_SCRIPT_PAGES = 'multilingualpress.allowed_admin_script_pages';
        const ACTION_BUILD_TABS = 'multilingualpress.build_tabs';
        const WORDPRESS_LANGUAGE_SETTING_MARKUP = 'wordpress.language_setting_markup';
        const MESSAGE_TYPE_FACTORIES = 'message_type_factories';
        /**
         * @inheritdoc
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerCore(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerFrontend(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @throws Throwable
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws Throwable
         */
        private function bootstrapCore(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws AssetException
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        private function bootstrapAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws Throwable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        private function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         *
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         * @throws Throwable
         */
        private function bootstrapFrontEnd(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Prevents collision if MLP v2 is installed and wp-content folder contains a mo file for v2.
         *
         * @param Container $container
         */
        private function loadTextDomain(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Build the Post Type Slug Site Setting.
         *
         * @param Container $container
         * @return array
         */
        private function postTypeSlugSiteSettings(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : array
        {
        }
        /**
         * @param Container $container
         * @throws Throwable
         */
        private function bootstrapSettingsPages(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param string $currentPage
         * @return bool
         */
        private function isMultilingualPressSettingsPage(string $currentPage) : bool
        {
        }
        /**
         * @param SiteDataDeletor $siteDataDeletor
         * @return void
         * @throws Throwable
         */
        private function handleDeleteSiteAction(\Inpsyde\MultilingualPress\Core\SiteDataDeletor $siteDataDeletor)
        {
        }
    }
    /**
     * Interface for settings repository
     * @psalm-type Setting = array{active?: int, ui?: string}
     */
    interface SettingsRepository
    {
        /**
         * Get all the settings of current repository stored in site options.
         *
         * Returns a two-items array, where the first is a boolean indicating if settings are found in database,
         * the second is actual settings array. Help distinguish on-purpose empty array in db from a no-result.
         *
         * @return array<int, bool|array>
         * @psalm-return  array{0: bool, 1: array<string, Setting>}
         */
        public function allSettings() : array;
    }
    /**
     * Deletes all plugin-specific data when a site is deleted.
     */
    class SiteDataDeletor
    {
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @param ContentRelations $contentRelations
         * @param SiteRelations $siteRelations
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Deletes all plugin-specific data of the site with the given ID.
         *
         * @param \WP_Site $oldSite
         * @throws NonexistentTable
         */
        public function deleteSiteData(\WP_Site $oldSite)
        {
        }
    }
    // phpcs:disable WordPress.PHP.StrictInArray.MissingArguments
    /**
     * Class TaxonomyRepository
     * @package Inpsyde\MultilingualPress\Core
     */
    class TaxonomyRepository implements \Inpsyde\MultilingualPress\Core\SettingsRepository
    {
        public const DEFAULT_SUPPORTED_TAXONOMIES = ['category', 'post_tag'];
        const FIELD_ACTIVE = 'active';
        const FIELD_SKIN = 'ui';
        const OPTION = 'multilingualpress_taxonomies';
        const FILTER_ALL_AVAILABLE_TAXONOMIES = 'multilingualpress.all_taxonomies';
        const FILTER_SUPPORTED_TAXONOMIES = 'multilingualpress.supported_taxonomies';
        /**
         * @var WP_Taxonomy[]
         */
        private $allAvailableTaxonomies;
        /**
         * Returns all taxonomies that MultilingualPress is able to support.
         *
         * @return WP_Taxonomy[]
         */
        public function allAvailableTaxonomies() : array
        {
        }
        /**
         * Returns the UI ID of the taxonomy with the given slug.
         *
         * @param string $slug
         * @return string
         */
        public function taxonomySkinId(string $slug) : string
        {
        }
        /**
         * Returns all taxonomies supported by MultilingualPress.
         *
         * @return string[]
         */
        public function supportedTaxonomies() : array
        {
        }
        /**
         * Checks if the taxonomy with the given slug is active.
         *
         * @param string $slug
         * @return bool
         */
        public function isTaxonomyActive(string $slug) : bool
        {
        }
        /**
         * Sets taxonomy support according to the given settings.
         *
         * @param array $taxonomies
         * @return bool
         */
        public function supportTaxonomies(array $taxonomies) : bool
        {
        }
        /**
         * Removes the support for all taxonomies.
         *
         * @return bool
         */
        public function removeSupportForAllTaxonomies() : bool
        {
        }
        /**
         * Retrieve all Registered Taxonomies
         *
         * @return WP_Taxonomy[]
         */
        protected function allAllowedTaxonomies() : array
        {
        }
        /**
         * @inheritDoc
         */
        public function allSettings() : array
        {
        }
        /**
         * @param string $slug
         * @param string $field
         * @param mixed $default
         * @return array|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        private function settingFor(string $slug, string $field, $default = null)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Customizer {
    interface SaveCustomizerDataInterface
    {
        /**
         * @param array $changeSetData
         */
        public function updateCustomizerMenuData(array $changeSetData);
    }
    class SaveCustomizerData implements \Inpsyde\MultilingualPress\Customizer\SaveCustomizerDataInterface
    {
        /**
         * if there are language items in the changed data of customizer then update menu item meta values
         * @param array $changeSetData
         */
        public function updateCustomizerMenuData(array $changeSetData)
        {
        }
        /**
         * Check if there are language items in the changed data of customizer
         *
         * @param array $data customizer's changed data item
         * @return bool
         */
        protected function isLanguageItemExists(array $data) : bool
        {
        }
        /**
         * update menu item meta values Which are necessary for passing the proper url when
         * wp_nav_menu_objects will be called in frontend
         *
         * @param array $data customizer's changed language data item
         */
        protected function updateMenuItemMeta(array $data)
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @param Container $container
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Database {
    /**
     * Service provider for all database objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function registerDbUtils(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function registerTables(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function integrateCache(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Database\Table {
    /**
     * Trait TableTrait
     *
     * @see Table
     */
    trait TableTrait
    {
        /**
         * @inheritdoc
         */
        public function exists() : bool
        {
        }
    }
    /**
     * Content relations table.
     */
    final class ContentRelationsTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        const COLUMN_CONTENT_ID = 'content_id';
        const COLUMN_RELATIONSHIP_ID = 'relationship_id';
        const COLUMN_SITE_ID = 'site_id';
        /**
         * @var string
         */
        private $prefix;
        /**
         * @param string $tablePrefix
         */
        public function __construct(string $tablePrefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema() : array
        {
        }
    }
    /**
     * Languages table.
     */
    final class ExternalSitesTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_SITE_URL = 'site_url';
        public const COLUMN_SITE_LANGUAGE_NAME = 'site_language_name';
        public const COLUMN_SITE_LANGUAGE_LOCALE = 'site_language_locale';
        public const COLUMN_REDIRECT = 'site_redirect';
        public const COLUMN_ENABLE_HREFLANG = 'enable_hreflang';
        public const COLUMN_DISPLAY_STYLE = 'display_style';
        public const COLUMN_ID = 'ID';
        /**
         * @var string
         */
        protected $prefix;
        /**
         * @param string $prefix
         */
        public function __construct(string $prefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema() : array
        {
        }
    }
    /**
     * Languages table.
     */
    final class LanguagesTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        const COLUMN_CUSTOM_NAME = 'custom_name';
        const COLUMN_ENGLISH_NAME = 'english_name';
        const COLUMN_BCP_47_TAG = 'http_code';
        const COLUMN_ID = 'ID';
        const COLUMN_ISO_639_1_CODE = 'iso_639_1';
        const COLUMN_ISO_639_2_CODE = 'iso_639_2';
        const COLUMN_ISO_639_3_CODE = 'iso_639_3';
        const COLUMN_LOCALE = 'locale';
        const COLUMN_NATIVE_NAME = 'native_name';
        const COLUMN_RTL = 'is_rtl';
        /**
         * @var string
         */
        private $prefix;
        /**
         * @param string $prefix
         */
        public function __construct(string $prefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema() : array
        {
        }
    }
    class RelationshipMetaTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_RELATIONSHIP_ID = 'relationship_id';
        public const COLUMN_META_KEY = 'meta_key';
        public const COLUMN_META_VALUE = 'meta_value';
        /**
         * @var string
         */
        private $prefix;
        public function __construct(string $tablePrefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema() : array
        {
        }
    }
    /**
     * Relationships table.
     */
    final class RelationshipsTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        const COLUMN_ID = 'id';
        const COLUMN_TYPE = 'type';
        /**
         * @var string
         */
        private $prefix;
        /**
         * @param string $tablePrefix
         */
        public function __construct(string $tablePrefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema() : array
        {
        }
    }
    /**
     * Site relations table.
     */
    final class SiteRelationsTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        const COLUMN_ID = 'ID';
        const COLUMN_SITE_1 = 'site_1';
        const COLUMN_SITE_2 = 'site_2';
        /**
         * @var string
         */
        private $prefix;
        /**
         * @param string $prefix
         */
        public function __construct(string $prefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent() : array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Factory {
    /**
     * Service provider for all factories.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Installation {
    /**
     * MultilingualPress installation checker.
     */
    class InstallationChecker
    {
        /**
         * @var SystemChecker
         */
        private $checker;
        /**
         * @var PluginProperties
         */
        private $properties;
        /**
         * @param SystemChecker $checker
         * @param PluginProperties $properties
         */
        public function __construct(\Inpsyde\MultilingualPress\Installation\SystemChecker $checker, \Inpsyde\MultilingualPress\Framework\PluginProperties $properties)
        {
        }
        /**
         * Checks the installation for compliance with the system requirements and return one of the
         * SystemChecker status flags.
         *
         * @return int
         */
        public function check() : int
        {
        }
        /**
         * Returns an array with the installed and the current version of MultilingualPress.
         *
         * @return SemanticVersionNumber[]
         */
        private function versions() : array
        {
        }
    }
    /**
     * MultilingualPress installer.
     */
    class Installer
    {
        /**
         * @var TableInstaller
         */
        private $tableInstaller;
        /**
         * @param TableInstaller $tableInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Database\TableInstaller $tableInstaller)
        {
        }
        /**
         * Installs the given tables.
         *
         * @param Table[] ...$tables
         */
        public function installTables(\Inpsyde\MultilingualPress\Framework\Database\Table ...$tables)
        {
        }
    }
    /**
     * Deactivates plugins network-wide by matching (partial) base names against all active plugins.
     */
    class NetworkPluginDeactivator
    {
        /**
         * Deactivates the given plugins network-wide.
         *
         * @param string[] $plugins
         * @return string[]
         */
        public function deactivatePlugins(string ...$plugins) : array
        {
        }
        /**
         * @param string[] $activePlugins
         * @param string[] $targetPlugins
         * @return array
         */
        private function filterOutNotActive(array $activePlugins, string ...$targetPlugins) : array
        {
        }
    }
    /**
     * Deactivates specific plugin.
     */
    class PluginDeactivator
    {
        /**
         * @var string[]
         */
        private $errors;
        /**
         * @var string
         */
        private $pluginBaseName;
        /**
         * @var string
         */
        private $pluginName;
        /**
         * @param string $pluginBaseName
         * @param string $pluginName
         * @param string[] $errors
         */
        public function __construct(string $pluginBaseName, string $pluginName, array $errors = [])
        {
        }
        /**
         * Deactivates the plugin, and renders an according admin notice.
         */
        public function deactivatePlugin()
        {
        }
        /**
         * Renders an admin notice informing about the plugin deactivation,
         * including potential error messages.
         */
        private function renderAdminNotice()
        {
        }
    }
    /**
     * Service provider for all Installation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
         * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer
         */
        private function registerCheckers(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
         * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer
         */
        private function registerInstallers(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param int $status
         * @param SemanticVersionNumber $installedVersion
         * @param Container $container
         */
        private function doInstallOrUpdate(int $status, \Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $installedVersion, \Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Migrates the new added tables if they doesn't exist.
         *
         * @param Table[] $tables The list of tables.
         * @throws InvalidTable
         */
        protected function runTableMigration(array $tables, \Inpsyde\MultilingualPress\Framework\Database\TableInstaller $tableInstaller) : void
        {
        }
        /**
         * @param Installer $installer
         * @param Table[] ...$tables
         */
        private function doInstall(\Inpsyde\MultilingualPress\Installation\Installer $installer, \Inpsyde\MultilingualPress\Framework\Database\Table ...$tables)
        {
        }
        /**
         * @param NetworkPluginDeactivator $deactivator
         * @param Updater $updater
         * @param SemanticVersionNumber $installedVersion
         */
        private function doUpdate(\Inpsyde\MultilingualPress\Installation\NetworkPluginDeactivator $deactivator, \Inpsyde\MultilingualPress\Installation\Updater $updater, \Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $installedVersion)
        {
        }
        /**
         * Update all exists sites language if language for mlp isn't set
         *
         * @param SiteSettingsRepository $repository
         *
         * @return void
         */
        private function insertSitesLanguages(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository)
        {
        }
        /**
         * When plugin is installed, the support for default taxonomies should be enabled automatically
         *
         * @param TaxonomyRepository $repository
         * @return void
         */
        protected function enableSupportForDefaultTaxonomies(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $repository) : void
        {
        }
    }
    class SiteRelationsChecker
    {
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @param SiteRelations $siteRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        /**
         * Checks if there are at least two sites related to each other, and renders an admin notice if not.
         *
         * @return bool
         */
        public function checkRelations() : bool
        {
        }
    }
    /**
     * Performs various system-specific checks.
     */
    class SystemChecker
    {
        const FILTER_FORCE_CHECK = 'multilingualpress.force_system_check';
        const ACTION_CHECKED_VERSION = 'multilingualpress.checked_version';
        const WRONG_PAGE_FOR_CHECK = 1;
        const INSTALLATION_OK = 2;
        const PLUGIN_DEACTIVATED = 3;
        const VERSION_OK = 4;
        const NEEDS_INSTALLATION = 5;
        const NEEDS_UPGRADE = 6;
        const LEGACY_DETECTED = 7;
        const MINIMUM_PHP_VERSION = '7.0.0';
        const MINIMUM_WORDPRESS_VERSION = '4.8.3';
        /**
         * @var string[]
         */
        private $errors = [];
        /**
         * @var PluginProperties
         */
        private $pluginProperties;
        /**
         * @var SiteRelationsChecker
         */
        private $siteRelationsChecker;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        /**
         * @param PluginProperties $pluginProperties
         * @param SiteRelationsChecker $siteRelationsChecker
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\PluginProperties $pluginProperties, \Inpsyde\MultilingualPress\Installation\SiteRelationsChecker $siteRelationsChecker, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Checks the installation for compliance with the system requirements.
         *
         * @return int
         */
        public function checkInstallation() : int
        {
        }
        /**
         * Checks the installed plugin version.
         *
         * @param SemanticVersionNumber $installedMlpVersion
         * @param SemanticVersionNumber $currentMlpVersion
         * @return int
         */
        public function checkVersion(\Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $installedMlpVersion, \Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $currentMlpVersion) : int
        {
        }
        /**
         * Checks if an old version of MLP is installed in the system.
         * @return void
         */
        public function checkLegacyVersion()
        {
        }
        /**
         * Checks if the current WordPress version is the required version higher, and collects
         * potential error messages.
         */
        private function checkWordpressVersion()
        {
        }
        /**
         * Checks if this is a multisite installation, and collects potential error messages.
         */
        private function checkMultisite()
        {
        }
        /**
         * Checks if MultilingualPress has been activated network-wide, and collects
         * potential error messages.
         */
        private function checkPluginActivation()
        {
        }
        /**
         * Checks if the context is valid.
         *
         * @return bool
         */
        private function isContextValid() : bool
        {
        }
    }
    /**
     * MultilingualPress uninstaller.
     */
    class Uninstaller
    {
        /**
         * @var int[]
         */
        private $siteIds;
        /**
         * @var TableInstaller
         */
        private $tableInstaller;
        /**
         * @param TableInstaller $tableInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Database\TableInstaller $tableInstaller)
        {
        }
        /**
         * Uninstalls the given tables.
         *
         * @param Table[] $tables
         * @return int
         */
        public function uninstallTables(array $tables) : int
        {
        }
        /**
         * Deletes all MultilingualPress network options.
         *
         * @param string[] $options
         * @return int
         */
        public function deleteNetworkOptions(array $options) : int
        {
        }
        /**
         * Deletes all MultilingualPress post meta.
         *
         * @param string[] $keys
         * @param int[] $siteIds
         * @return bool
         */
        public function deletePostMeta(array $keys, array $siteIds = []) : bool
        {
        }
        /**
         * Deletes all MultilingualPress options for the given (or all) sites.
         *
         * @param string[] $options
         * @param int[] $siteIds
         * @return int
         */
        public function deleteSiteOptions(array $options, array $siteIds = []) : int
        {
        }
        /**
         * Deletes all MultilingualPress user meta.
         *
         * @param string[] $keys
         */
        public function deleteUserMeta(array $keys)
        {
        }
        /**
         * @param array $siteOptions
         * @param array $userMeta
         */
        public function deleteOnboardingData(array $siteOptions, array $userMeta)
        {
        }
        /**
         * Unschedule all MLP events
         *
         * When the plugin is uninstalled, we need to remove all the scheduled events
         *
         * @param array<string> $events The array of the hook names for which the events should be unscheduled
         */
        public function deleteScheduledEvents(array $events)
        {
        }
        /**
         * Returns an array with all site IDs.
         *
         * @return int[]
         */
        private function siteIds() : array
        {
        }
    }
    /**
     * Updates any installed plugin data to the current version.
     */
    class Updater
    {
        /**
         * @var PluginProperties
         */
        private $pluginProperties;
        /**
         * @param PluginProperties $pluginProperties
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\PluginProperties $pluginProperties)
        {
        }
        /**
         * Updates any installed plugin data to the current version.
         *
         * @param SemanticVersionNumber $installedVersion
         */
        public function update(\Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $installedVersion)
        {
        }
        /**
         * Will perform the necessary rewrites when the plugin is upgraded.
         *
         * When the plugin is upgraded, we need to fix the permalink rewrites.
         *
         * @see https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ upgrader_process_complete
         *
         * @param \WP_Upgrader $upgraderObject
         * @param array $options
         */
        public function rewriteRulesAfterPluginUpgrade(\WP_Upgrader $upgraderObject, array $options)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Language {
    /**
     * Language wrapping data shipped with MLP.
     */
    final class EmbeddedLanguage implements \Inpsyde\MultilingualPress\Framework\Language\Language
    {
        const KEY_BCP_47_TAG = 'bcp47';
        const KEY_CODE = 'code';
        const KEY_ALT_CODE = 'alt-code';
        const KEY_ISO_639_1 = 'iso-639-1';
        const KEY_ISO_639_2 = 'iso-639-2';
        const KEY_ISO_639_3 = 'iso-639-3';
        const KEY_ISO_NAME = 'iso-name';
        const KEY_LANGUAGE = 'language';
        const KEY_NATIVE_NAME = 'native-name';
        const KEY_ENGLISH_NAME = 'english-name';
        const KEY_RTL = 'rtl';
        const KEY_TYPE = 'type';
        const TYPE_LANGUAGE = 'language';
        const TYPE_LOCALE = 'locale';
        const TYPE_VARIANT = 'variant';
        /**
         * @var Language
         */
        private $language;
        /**
         * @var string
         */
        private $isoName;
        /**
         * @var string
         */
        private $type = '';
        /**
         * @var string
         */
        private $parentLanguageCode = '';
        /**
         * @param array $jsonData
         * @return EmbeddedLanguage
         */
        public static function fromJsonData(array $jsonData) : \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * @param Language $language
         */
        public function __construct(\Inpsyde\MultilingualPress\Language\Language $language)
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : int
        {
        }
        /**
         * @inheritdoc
         */
        public function isRtl() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function englishName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function nativeName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoCode(string $which = self::ISO_SHORTEST) : string
        {
        }
        /**
         * @inheritdoc
         */
        public function bcp47tag() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function locale() : string
        {
        }
        public function type() : string
        {
        }
        public function parentLanguageTag() : string
        {
        }
        /**
         * The method will change the language variant locale from lang_LANG_Variant to lang_LANG
         *
         * @param string $locale of the language variant
         * @return string changed locale for language variant
         */
        public static function changeLanguageVariantLocale(string $locale) : string
        {
        }
        /**
         * The method will change the language variant from lang-LANG-Variant to lang-LANG
         *
         * @param string $language of the language variant
         * @return string changed language
         */
        public static function changeLanguageVariant(string $language) : string
        {
        }
    }
    /**
     * Basic language data type implementation.
     */
    final class Language implements \Inpsyde\MultilingualPress\Framework\Language\Language
    {
        const DEFAULTS = [\Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_ID => 0, \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_BCP_47_TAG => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_LOCALE => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_ISO_639_1_CODE => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_ISO_639_2_CODE => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_ISO_639_3_CODE => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_ENGLISH_NAME => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_NATIVE_NAME => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_CUSTOM_NAME => '', \Inpsyde\MultilingualPress\Database\Table\LanguagesTable::COLUMN_RTL => false, \Inpsyde\MultilingualPress\Language\EmbeddedLanguage::KEY_TYPE => 'locale'];
        /**
         * @var array
         */
        private $data;
        /**
         * @var string
         */
        private $isoName;
        /**
         * @param array $data
         */
        public function __construct(array $data)
        {
        }
        /**
         * @inheritdoc
         */
        public function id() : int
        {
        }
        /**
         * @inheritdoc
         */
        public function isRtl() : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function name() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function englishName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function nativeName() : string
        {
        }
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function isoName() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoCode(string $which = self::ISO_SHORTEST) : string
        {
        }
        /**
         * @inheritdoc
         */
        public function bcp47tag() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function locale() : string
        {
        }
        /**
         * @inheritdoc
         */
        public function type() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\License\Api {
    class Activator
    {
        const WC_API = 'wc-am-api';
        /**
         * @var array
         */
        private $apiConfiguration;
        /**
         * @param array $apiConfiguration
         */
        public function __construct(array $apiConfiguration)
        {
        }
        /**
         * @param License $license
         * @return array
         */
        public function activate(\Inpsyde\MultilingualPress\License\License $license) : array
        {
        }
        /**
         * @param License $license
         * @return array
         */
        public function deactivate(\Inpsyde\MultilingualPress\License\License $license) : array
        {
        }
        /**
         * @param License $license
         * @return array
         */
        public function status(\Inpsyde\MultilingualPress\License\License $license) : array
        {
        }
    }
    class Updater
    {
        const WC_API = 'wc-am-api';
        /**
         * @var array
         */
        private $pluginData;
        /**
         * @var array
         */
        private $apiConfiguration;
        /**
         * @var License
         */
        private $license;
        /**
         * @param array $pluginProperties
         * @param array $apiConfiguration
         * @param License $license
         */
        public function __construct(array $pluginProperties, array $apiConfiguration, \Inpsyde\MultilingualPress\License\License $license)
        {
        }
        /**
         * @param stdClass $transient
         * @return stdClass
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function updateCheck(\stdClass $transient)
        {
        }
        /**
         * @param bool|mixed $result
         * @param string $action
         * @param stdClass $args
         * @return bool|mixed
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function pluginInformation($result, string $action, \stdClass $args)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\License {
    class License
    {
        private $productId;
        private $apiKey;
        private $instance;
        private $status;
        public function __construct(string $productId, string $apiKey, string $instance, string $status)
        {
        }
        public function productId() : string
        {
        }
        public function apiKey() : string
        {
        }
        public function instance() : string
        {
        }
        public function status() : string
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * MultilingualPress front controller.
     */
    final class MultilingualPress
    {
        const ACTION_BOOTSTRAPPED = 'multilingualpress.bootstrapped';
        const ACTION_REGISTER_MODULES = 'multilingualpress.register_modules';
        const OPTION_VERSION = 'multilingualpress_version';
        /**
         * @var Container
         */
        private $container;
        /**
         * @var ServiceProvidersCollection
         */
        private $serviceProviders;
        /**
         * @param Container $container
         * @param ServiceProvidersCollection $serviceProviders
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Service\Container $container, \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection $serviceProviders)
        {
        }
        /**
         * Bootstraps MultilingualPress.
         *
         * @return bool
         * @throws \RuntimeException
         */
        public function bootstrap() : bool
        {
        }
        /**
         * @return bool
         */
        private function isPluginActivated() : bool
        {
        }
        /**
         * Checks if the current request needs MultilingualPress to register any modules.
         *
         * @return bool
         */
        private function needsModules() : bool
        {
        }
        /**
         * Registers all modules.
         */
        private function registerModules()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu {
    /**
     * Handler for nav menu AJAX requests.
     */
    class AjaxHandler
    {
        const ACTION = 'multilingualpress_add_languages_to_nav_menu';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var ItemRepository
         */
        private $repository;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param Nonce $nonce
         * @param ItemRepository $repository
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\NavMenu\ItemRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Handles the AJAX request and sends an appropriate response.
         */
        public function handle()
        {
        }
        /**
         * @return array
         */
        private function siteIdsFromRequest() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu\BlockTypes {
    /**
     * @psalm-type flagDisplayTypeValues = 'only_language'|'flag_and_text'|'only_flag'
     * @psalm-type siteId = int
     * @psalm-type languageInfo = array{name: string, url: string, flagUrl: string}
     * @psalm-type siteLanguage = <siteId, languageInfo>
     * @psalm-type languageMenuContext = array{languages: list<siteLanguage>, flagDisplayType: flagDisplayTypeValues}
     */
    class LanguageMenuContextFactory implements \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface
    {
        /**
         * @var Translations
         */
        protected $translations;
        /**
         * @var FlagFactory
         */
        protected $flagFactory;
        public function __construct(\Inpsyde\MultilingualPress\Api\Translations $translations, \Inpsyde\MultilingualPress\SiteFlags\Flag\Factory $flagFactory)
        {
        }
        /**
         * @inheritDoc
         * @psalm-return languageMenuContext The context.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function createContext(array $attributes) : array
        {
        }
        /**
         * Returns the flag url of given site.
         *
         * @param int $siteId The site ID.
         * @return string The flag url.
         */
        protected function siteFlagUrl(int $siteId) : string
        {
        }
        /**
         * Returns the translation of a given site.
         *
         * @param int $siteId The site ID.
         * @return ?Translation
         */
        protected function siteTranslation(int $siteId) : ?\Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu\CopyNavMenu\Ajax {
    class CopyNavMenuSettingsView
    {
        const ACTION = 'multilingualpress_copy_nav_menu_settings_view';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handle AJAX request.
         * @throws NonexistentTable
         */
        public function handle()
        {
        }
        /**
         * Render a select of menu names
         * @throws NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function generateCopyNavMenuSettingsMarkup() : string
        {
        }
        /**
         * Will return assigned location names of given menu
         *
         * @param WP_Term $menu WP_Term object for Menu
         * @return array of menu location names
         */
        protected function assignedMenuLocationNames(\WP_Term $menu) : array
        {
        }
        /**
         * Generate the copy menu select-box label markup
         *
         * @return string the markup of the copy menu select-box label
         */
        protected function selectLabelMarkup() : string
        {
        }
        /**
         * Generate the hidden input with the site id value of remote site
         *
         * @param int $siteId The remote site id
         *
         * @return string the markup of the hidden input with remote site id value
         */
        protected function hiddenSiteIdFieldMarkup(int $siteId) : string
        {
        }
        /**
         * Generate the hidden input with the current menu id
         *
         * @return string the markup of the hidden input with the current menu id
         */
        protected function hiddenCurrentMenuNameFieldMarkup() : string
        {
        }
        /**
         * Generate the markup of the select options with remote site menu's id as the option value
         * and the option name with combination of remote site menu's name and location if is assigned
         *
         * @param int $menuTermId The menu id of the remote site
         * @param string $menuName The manu name of the remote site
         * @param array $assignedMenuLocationNames the assigned menu location of the menu
         *
         * @return string the markup of the select-box options
         */
        protected function selectOptionMarkup(int $menuTermId, string $menuName, array $assignedMenuLocationNames) : string
        {
        }
        /**
         * Generate the markup of the select options group with the data-site_id to which the menus belong,
         * with the site name as the group label and the options with the menus which belong to that site
         *
         * @param int $siteId The remote site id
         * @param string $selectGroupOptionsMarkup The markup of the options of the group
         *
         * @return string the markup of the Group of options
         * @throws NonexistentTable
         */
        protected function selectOptionGroupMarkup(int $siteId, string $selectGroupOptionsMarkup) : string
        {
        }
        /**
         * Generate the Nonce field markup
         *
         * @return string the markup of the Nonce field
         */
        protected function nonceFieldMarkup() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu\CopyNavMenu {
    /**
     * Handler for nav menu AJAX requests.
     */
    class CopyNavMenu
    {
        /**
         * The Request param names
         */
        const REQUEST_VALUE_NAME_FOR_MENU_TO_COPY = 'mlp_menu_to_copy';
        const REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID = 'remote_site_id';
        const REQUEST_VALUE_NAME_FOR_CURRENT_MENU_NAME = 'current_menu_name';
        /**
         * MLP language menu item configs
         */
        const LANGUAGE_MENU_ITEM_META_KEY_SITE_ID = '_blog_id';
        const LANGUAGE_MENU_ITEM_META_KEY_ITEM_TYPE = '_menu_item_type';
        const LANGUAGE_MENU_ITEM_TYPE = 'mlp_language';
        /**
         * Configs to determinate and update parent menu item of copied menu
         */
        const REMOTE_MENU_ITEM_ID = 'remote_menu_item_id';
        const MENU_ITEM_META_KEY_PARENT_MENU_ITEM = '_menu_item_menu_item_parent';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @param Nonce $nonce
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Handles the copy of navigation menu from remote site
         * Will get the values from Request
         * Will delete the current menu items
         * Will copy the menu items from remote site
         */
        public function handleCopyNavMenu()
        {
        }
        /**
         * Will return the value from request with the giben param name
         *
         * @param string $requestParamName The name of the Request param,
         * can be either self::REQUEST_VALUE_NAME_FOR_MENU_TO_COPY or
         * self::REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID or
         * self::REQUEST_VALUE_NAME_FOR_CURRENT_MENU_ID
         * @return string the value of request param
         */
        protected function getValueFromRequest(string $requestParamName) : string
        {
        }
        /**
         * Retrieves all menu items of a navigation menu.
         *
         * @param int $menuId The id of the menu to get
         * @return false|array $items Array of menu items, otherwise false.
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        protected function getMenuItems(int $menuId)
        {
        }
        /**
         * Will delete the menu items of given menu
         *
         * @param int $menuId The menu id from which the items should be deleted
         */
        protected function deleteMenuItems(int $menuId)
        {
        }
        /**
         * Will Copy the Menu Items from remote site for selected menu
         *
         * @param array $remoteMenu The Remote menu which is selected to be copied
         * @param int $remoteSiteId The Remote site id to which the selected menu to be copied belongs
         * @param int $sourceMenuId The Source menu id to whcih the items should be copied
         * @throws NonexistentTable
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        protected function copyMenuItems(array $remoteMenu, int $remoteSiteId, int $sourceMenuId)
        {
        }
        /**
         * Will update the necessary metadata for mlp_language type menu items
         *
         * @param WP_Post $remoteMenuItem The menu item object from remote site
         * @param int $remoteSiteId The remote site id from where the menu item is copied
         * @param int $sourceMenuItemDbId The copied source menu item db id
         */
        protected function updateSourceLanguageMenuItemMeta(\WP_Post $remoteMenuItem, int $remoteSiteId, int $sourceMenuItemDbId)
        {
        }
        /**
         * Will generate the menu item data which should be created because of the menu copy
         * If there is a connected post in source site then it's data will be taken, otherwise
         * will be created a custom menu item with the url to remote post.
         *
         * @param WP_Post $remoteMenuItem The remote menu item which should be copied
         * @param int $sourceContentId The source post id, if exist it can be used to grab
         * additional info from it instead of taking from remote menu item
         * @return array of generated menu item data
         */
        protected function generateNewMenuItemData(\WP_Post $remoteMenuItem, int $sourceContentId) : array
        {
        }
        /**
         * Check if menu item has parent
         *
         * @param int $parentMenuItemId the menu item id to check
         * @return bool true/false if menu item has parent or no
         */
        protected function hasParentMenuItem(int $parentMenuItemId) : bool
        {
        }
        /**
         * The method will update the parent menu item ids for the given menu
         *
         * @param int $menuId The menu Id for which to check and update parent menu item ids
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        protected function updateParentMenuItems(int $menuId)
        {
        }
        /**
         * Will create a new navigation menu
         *
         * @param string $namePrefix The name prefix of new menu
         * @return int created menu ID
         */
        protected function createNewNavMenu(string $namePrefix) : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu {
    /**
     * Deletes nav menu items.
     */
    class ItemDeletor
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @param \wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Deletes all remote MultilingualPress nav menu items linking to the (to-be-deleted) site with
         * the given ID.
         *
         * @param \WP_Site $oldSite
         * @return int
         */
        public function deleteItemsForDeletedSite(\WP_Site $oldSite) : int
        {
        }
    }
    /**
     * Filters nav menu items and passes the proper URL.
     */
    class ItemFilter
    {
        public const ITEMS_FILTER_CACHE_KEY = 'filter_items';
        public const ACTION_PREPARE_ITEM = 'multilingualpress.prepare_nav_menu_item';
        public const FILTER_SHOULD_PRESERVE_URL_PARAMS = 'multilingualpress.should_preserve_url_params';
        /**
         * @var ItemRepository
         */
        private $repository;
        /**
         * @var Translations
         */
        private $translations;
        /**
         * @var Facade
         */
        private $cache;
        /**
         * @var CacheSettingsRepository
         */
        private $cacheSettingsRepository;
        /**
         * @var SiteSettingsRepository
         */
        private $siteSettingsRepository;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $translations, \Inpsyde\MultilingualPress\NavMenu\ItemRepository $repository, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Filters the nav menu items.
         *
         * @param WP_Post[] $items
         * @return WP_Post[]
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         */
        public function filterItems(array $items) : array
        {
        }
        /**
         * Delete given post if its remote site ID does not exist anymore.
         *
         * @param WP_Post $item
         * @return bool
         */
        private function maybeDeleteObsoleteItem(\WP_Post $item) : bool
        {
        }
        /**
         * Assigns the remote URL and fires an action hook.
         *
         * @param WP_Post $item
         * @param Translation[] $translations
         * @return bool
         * @throws Throwable
         */
        private function prepareItem(\WP_Post $item, array $translations) : bool
        {
        }
        /**
         * Returns the remote URL and the translation object for the item.
         *
         * @param Translation[] $translations
         * @param int $siteId The site ID.
         * @return array
         */
        private function itemDetails(array $translations, int $siteId) : array
        {
        }
        /**
         * @param array $navItems
         * @return array
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         */
        private function filterItemsCache(array $navItems) : array
        {
        }
        /**
         * @param WP_Post[] $items
         * @return bool
         */
        protected function itemsExists(array $items) : bool
        {
        }
        /**
         * @param array $translations
         * @param ItemRepository $repository
         * @param SiteSettingsRepository $settingsRepository
         * @return void
         * @throws Throwable
         */
        public function hookToMenuLink(array $translations, \Inpsyde\MultilingualPress\NavMenu\ItemRepository $repository, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $settingsRepository) : void
        {
        }
    }
    class ItemRepository
    {
        const META_KEY_SITE_ID = '_blog_id';
        const META_KEY_ITEM_TYPE = '_menu_item_type';
        const ITEM_TYPE = 'mlp_language';
        const FILTER_MENU_LANGUAGE_NAME = 'multilingualpress.nav_menu_language_name';
        /**
         * @var int[]
         */
        private $siteIds = [];
        /**
         * Returns the according items for the sites with the given IDs.
         *
         * @param int $menuId
         * @param int[] $siteIds
         * @return \WP_Post[]
         */
        public function itemsForSites(int $menuId, int ...$siteIds) : array
        {
        }
        /**
         * Returns the site ID for the nav menu item with the given ID.
         *
         * @param int $itemId
         * @return int
         */
        public function siteIdOfMenuItem(int $itemId) : int
        {
        }
        /**
         * Ensures that an item according to the given arguments exists in the database.
         *
         * @param int $menuId
         * @param int $siteId
         * @param string $languageName
         * @return \WP_Post|null
         */
        private function ensureItem(int $menuId, int $siteId, string $languageName)
        {
        }
        /**
         * Prepares the given item for use.
         *
         * @param \WP_Post|\stdClass $item
         * @param int $siteId
         * @return \WP_Post
         */
        private function prepareItem(\WP_Post $item, int $siteId) : \WP_Post
        {
        }
    }
    /**
     * Languages meta box view.
     */
    class LanguagesMetaboxView
    {
        const ID = 'mlp-languages';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Renders the HTML.
         */
        public function render()
        {
        }
        /**
         * Renders all language items.
         */
        private function renderLanguageCheckboxes()
        {
        }
        /**
         * Renders a single item according to the given arguments.
         *
         * @param string $languageName
         * @param int $siteId
         */
        private function renderLanguageCheckbox(string $languageName, int $siteId)
        {
        }
        /**
         * Renders the button controls HTML.
         */
        private function renderButtonControls()
        {
        }
        /**
         * Returns the URL for the "Select All" link.
         *
         * @return string
         */
        private function selectAllUrl() : string
        {
        }
    }
    /**
     * @psalm-type relatedSites = array{id: int, name: string}
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        const NONCE_ACTION = 'add_languages_to_nav_menu';
        const NONCE_COPY_NAV_MENU_ACTION = 'copy_nav_menu';
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        private function bootstrapAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function integrateCache(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param ItemDeletor $itemDeletor
         * @return void
         * @throws \Throwable
         */
        private function handleDeleteSiteAction(\Inpsyde\MultilingualPress\NavMenu\ItemDeletor $itemDeletor)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Onboarding {
    /**
     * Onboarding messages
     */
    class Notice
    {
        /**
         * @var State
         */
        private $onboardingState;
        /**
         * @param State $onboardingState
         */
        public function __construct(\Inpsyde\MultilingualPress\Onboarding\State $onboardingState)
        {
        }
        /**
         * Creates onboarding message content.
         *
         * @param string $onboardingState
         * @return \stdClass
         */
        public function onboardingMessageContent(string $onboardingState) : \stdClass
        {
        }
        /**
         * @param string $message
         * @param string $buttonText
         * @param string $buttonLink
         * @return string
         */
        private function appendButtonToMessage(string $message, string $buttonText, string $buttonLink) : string
        {
        }
        /**
         * @return array
         */
        private function forSingleSite() : array
        {
        }
        /**
         * @return array
         */
        public function forMoreThanOneSite() : array
        {
        }
        /**
         * @return array
         */
        private function forSettings() : array
        {
        }
        /**
         * @return array
         */
        private function forPosts() : array
        {
        }
        /**
         * @return array
         */
        private function forMultilingualPressSettings() : array
        {
        }
        /**
         * @return array
         */
        private function forEditPostScreen() : array
        {
        }
        /**
         * @return array
         */
        private function end() : array
        {
        }
        /**
         * @return array
         */
        private function nullNoticedata() : array
        {
        }
    }
    /**
     * Onboarding messages manager.
     */
    class Onboarding
    {
        const OPTION_ONBOARDING_DISMISSED = 'onboarding_dismissed';
        const OPTION_LANGUAGE_SETTINGS_CHANGED_DISMISSED = 'language_settings_changed_dismissed';
        /**
         * @var AssetManager
         */
        private $assetManager;
        /**
         * @var Notice
         */
        private $onboardingMessages;
        /**
         * @var State
         */
        private $onboardingState;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var SiteRelations
         */
        private $siteRelations;
        /**
         * @param AssetManager $assetManager
         * @param SiteRelations $siteRelations
         * @param Request $request
         * @param State $onboardingState
         * @param Notice $onboardingMessages
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Onboarding\State $onboardingState, \Inpsyde\MultilingualPress\Onboarding\Notice $onboardingMessages)
        {
        }
        /**
         * @return void
         * @throws AssetException
         * @throws NonexistentTable
         */
        public function messages()
        {
        }
        /**
         * @return void
         */
        public function handleDismissOnboardingMessage()
        {
        }
        /**
         * @return void
         */
        public function handleAjaxDismissOnboardingMessage()
        {
        }
        /**
         * @return bool
         */
        private function mayDisplayMessage() : bool
        {
        }
        /**
         * @return void
         * @throws AssetException
         */
        private function enqueueAssets()
        {
        }
        /**
         * Handle onboarding of new language settings.
         *
         * Since we are not overriding the default WordPress language setting we need to show a message about updated
         * features. Besides that when the plugin is updated we need to check existing value for default WordPress language
         * setting and if it doesn't match the MLP language setting value then we need to override it with MLP language
         * setting value. This has to be done only once so that the users will not loose their frontend language.
         * This Functionality will be removed after next release.
         *
         * @throws NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         */
        public function handleLanguageSettings()
        {
        }
    }
    /**
     * Service provider for Onboarding
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @return void
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        private function registerPointersForScreen(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @return void
         */
        private function registerPointersActionForScreen(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @return void
         */
        private function registerAssets(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @return void
         */
        private function handleDismissOnboardingMessage(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @return void
         */
        private function dismissPointersForNewUsers()
        {
        }
        /**
         * @param Container $container
         */
        private function dismissPointersOnAjaxCalls(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
    /**
     * Onboarding state manager.
     */
    class State
    {
        const OPTION_NAME = 'onboarding_state';
        const STATE_SITES = 'sites';
        const STATE_SETTINGS = 'settings';
        const STATE_POST = 'post';
        const STATE_END = 'end';
        /**
         * Update onboarding state based on site relations and screen.
         * @param string $onboardingState
         * @param array $siteRelations
         * @return string
         */
        public function update(string $onboardingState, array $siteRelations) : string
        {
        }
        /**
         * @return string
         */
        public function read() : string
        {
        }
        /**
         * @return string
         */
        private function updateForSettings() : string
        {
        }
        /**
         * @return string
         */
        private function updateForPost() : string
        {
        }
        /**
         * @return string
         */
        private function finish() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule\Action {
    /**
     * Class ActionException
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class ActionException extends \Exception
    {
        /**
         * @param Schedule $schedule
         * @return ActionException
         */
        public static function becauseScheduleCannotBeDeleted(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule) : \Inpsyde\MultilingualPress\Schedule\Action\ActionException
        {
        }
        /**
         * @param string $scheduleId
         * @return ActionException
         */
        public static function forInvalidScheduleId(string $scheduleId) : \Inpsyde\MultilingualPress\Schedule\Action\ActionException
        {
        }
        /**
         * @param string $hook
         * @param Schedule $schedule
         * @return ActionException
         */
        public static function becauseUnscheduleHookFailForSchedule(string $hook, \Inpsyde\MultilingualPress\Schedule\Schedule $schedule) : \Inpsyde\MultilingualPress\Schedule\Action\ActionException
        {
        }
    }
    /**
     * Interface ActionTask
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    interface ActionTask
    {
        /**
         * @return void
         * @throws RuntimeException if execution fails
         */
        public function execute();
    }
    /**
     * Trait ResponseTrait
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    trait ResponseTrait
    {
        /**
         * @var array
         */
        private $successMessages;
        /**
         * @param string $actionName
         * @param array $errors
         */
        protected function sendResponseFor(string $actionName, array $errors)
        {
        }
        /**
         * @param string $actionName
         * @return string
         */
        protected function successMessage(string $actionName) : string
        {
        }
        /**
         * @param array $errors
         * @return string
         */
        protected function errorMessage(array $errors) : string
        {
        }
        /**
         * @param array $messages
         * @return string
         */
        protected function reduceMessages(array $messages) : string
        {
        }
        /**
         * @return void
         * @uses die
         */
        protected function die()
        {
        }
    }
    /**
     * Trait ActionsProcessorTrait
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    trait ActionsProcessorTrait
    {
        use \Inpsyde\MultilingualPress\Schedule\Action\ResponseTrait;
        /**
         * @var array
         */
        private $tasks;
        /**
         * @var MessageFactoryInterface
         */
        private $messageFactory;
        /**
         * @var string
         */
        private $actionNameKey;
        /**
         * @param ServerRequest $request
         * @throws \Exception
         */
        protected function process(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * @param ServerRequest $request
         * @return string
         */
        protected function actionNameFrom(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request) : string
        {
        }
    }
    /**
     * Trait AuthorizationTrait
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    trait AuthorizationTrait
    {
        /**
         * @var Context
         */
        private $context;
        /**
         * @var WpNonce|MockObject
         */
        private $nonce;
        /**
         * @var string
         */
        private $userCapability;
        /**
         * @return bool
         */
        protected function isUserAuthorized() : bool
        {
        }
    }
    /**
     * Class RemoveActionTask
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class RemoveActionTask implements \Inpsyde\MultilingualPress\Schedule\Action\ActionTask
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        /**
         * @var Scheduler
         */
        private $scheduler;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var string
         */
        private $scheduleIdName;
        /**
         * @var string
         */
        private $scheduleHook;
        /**
         * ScheduleRemoveAction constructor.
         * @param Request $request
         * @param Scheduler $scheduler
         * @param string $scheduleIdName
         * @param string $scheduleHook
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, string $scheduleIdName, string $scheduleHook)
        {
        }
        /**
         * @inheritDoc
         * @throws Throwable
         */
        public function execute()
        {
        }
        /**
         * Execute Tasks
         * @throws ActionException
         */
        protected function executeTasks()
        {
        }
        /**
         * Delete Schedule
         *
         * Remove the schedule Id from the list of the schedule id in the database
         *
         * @throws ActionException
         */
        protected function deleteSchedule()
        {
        }
        /**
         * Retrieve the Schedule Id from the request
         *
         * @return Schedule
         * @throws ActionException
         */
        protected function schedule() : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * Retrieve the schedule Id from the current Request
         *
         * @return string
         */
        protected function scheduleIdFromRequest() : string
        {
        }
        /**
         * Clean Scheduled Events
         *
         * Un-schedule all of the events for the copy attachment
         *
         * @return void
         * @throws ActionException
         */
        protected function cleanScheduledEvents()
        {
        }
    }
    /**
     * Class ScheduleActionRequestHandler
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class ScheduleActionRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Schedule\Action\AuthorizationTrait;
        use \Inpsyde\MultilingualPress\Schedule\Action\ActionsProcessorTrait;
        const ACTION_AFTER_ACTION_DISPATCHED = 'multilingualpress.after_schedule_action_dispatched';
        const ACTION_NO_SCHEDULE_ACTION_DISPATCHED = 'multilingualpress.no_schedule_action_dispatched';
        /**
         * ActionsRequestHandler constructor.
         * @param Nonce $nonce
         * @param array $tasks
         * @param MessageFactoryInterface $messageFactory
         * @param Context $context
         * @param array $successMessages
         * @param string $actionName
         * @param string $userCapability
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, array $tasks, \Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface $messageFactory, \Inpsyde\MultilingualPress\Framework\Nonce\Context $context, array $successMessages, string $actionName, string $userCapability)
        {
        }
        /**
         * @inheritDoc
         * @param ServerRequest $request
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule {
    /**
     * Used to simplify AJAX interaction with scheduler.
     *
     * It provides two methods to get URLs of:
     * - an AJAX endpoint to generate a schedule for given hook, steps and args
     * - an AJAX endpoint to get information about a schedule of given ID.
     * Both URLS are nonced.
     *
     * The class also ships the handler for the two AJAX actions, registered by the service provider to
     * respond to them.
     */
    class AjaxScheduleHandler
    {
        const ACTION_SCHEDULE = 'multilingualpress_ajax_cron_schedule';
        const ACTION_INFO = 'multilingualpress_ajax_cron_schedule_info';
        const FILTER_AJAX_SCHEDULE_DELAY = 'multilingualpress.ajax_schedule_delay';
        const MODE_PUBLIC = 'public';
        const MODE_RESTRICTED = 'restricted';
        const SCHEDULE_ID = 'schedule-id';
        const SCHEDULE_STEPS = 'schedule-steps';
        const SCHEDULE_HOOK = 'schedule-hook';
        const SCHEDULE_ARGS = 'schedule-args';
        /**
         * @var Scheduler
         */
        private $scheduler;
        /**
         * @var NonceFactory
         */
        private $factory;
        /**
         * @param Scheduler $scheduler
         * @param NonceFactory $factory
         */
        public function __construct(\Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Framework\Factory\NonceFactory $factory)
        {
        }
        /**
         * Generate an AJAX URL whose handler will generate a new schedule.
         *
         * Steps, hook, and args, necessary to build the schedule can be passed here to be already
         * included in the URL, or can be ignored here and passed later with the request.
         *
         * @param int $steps
         * @param string $hook
         * @param array|null $args
         * @param string $mode
         * @return string
         */
        public function scheduleAjaxUrl(int $steps = null, string $hook = null, array $args = null, string $mode = self::MODE_RESTRICTED) : string
        {
        }
        /**
         * Generate an AJAX URL whose handler will provide information for a schedule.
         *
         * Schedule ID, necessary to retrieve the schedule can be passed here to be already
         * included in the URL, or can be ignored here and passed later with the request.
         *
         * @param string $scheduleId
         * @param string $mode
         * @return string
         */
        public function scheduleInfoAjaxUrl(string $scheduleId = null, string $mode = self::MODE_RESTRICTED) : string
        {
        }
        /**
         * Handler for both AJAX actions managed by this class.
         *
         * Checks action and nonce, then dispatch the request to the specific handling method.
         *
         * @param ServerRequest $request
         * @param Context|null $context
         * @return void
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, \Inpsyde\MultilingualPress\Framework\Nonce\Context $context = null)
        {
        }
        /**
         * Handling method for information about a schedule.
         *
         * @param ServerRequest $request
         */
        private function sendScheduleInfo(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * Handling method for schedule generation.
         *
         * @param ServerRequest $request
         */
        private function createNewSchedule(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule\Delay {
    interface Delay
    {
        /**
         * Calculates the delay in seconds that should be used to setup the cron event for each index,
         * providing the total steps and schedule arguments as context.
         *
         * @param int $index
         * @param int $total
         * @param array|null $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null) : int;
    }
    /**
     * Class AverageMicrosecondsDuration
     * @package Inpsyde\MultilingualPress\Schedule\Delay
     */
    final class AverageMicrosecondsDuration implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        const AVERAGE_MICROSECONDS_DEFAULT = 500;
        /**
         * @var int
         */
        private $secondsPerStep;
        /**
         * Creates an instance with default average of 500 microseconds, which means delay added will be
         * 1 seconds every 2000 steps.
         *
         * @return AverageMicrosecondsDuration
         */
        public static function default() : \Inpsyde\MultilingualPress\Schedule\Delay\AverageMicrosecondsDuration
        {
        }
        /**
         * @param int $microseconds
         */
        public function __construct(int $microseconds)
        {
        }
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         *
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null) : int
        {
        }
    }
    final class MaxDelay implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        /**
         * @var int
         */
        private $maxDelay;
        /**
         * @param int $maxDelay
         */
        public function __construct(int $maxDelay)
        {
        }
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null) : int
        {
        }
    }
    /**
     * Class OneSecondEveryGivenSteps
     * @package Inpsyde\MultilingualPress\Schedule\Delay
     */
    final class OneSecondEveryGivenSteps implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        const EVERY_STEPS_DEFAULT = 300;
        /**
         * @var int
         */
        private $everySteps;
        /**
         * @return OneSecondEveryGivenSteps
         */
        public static function default() : \Inpsyde\MultilingualPress\Schedule\Delay\OneSecondEveryGivenSteps
        {
        }
        /**
         * @param int $everySteps
         */
        public function __construct(int $everySteps)
        {
        }
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null) : int
        {
        }
    }
    final class Zero implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null) : int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule {
    /**
     * Class Schedule
     * @package Inpsyde\MultilingualPress\Schedule
     */
    class Schedule
    {
        const STARTED = 'started';
        const RUNNING = 'running';
        const DONE = 'done';
        const TIMEZONE = 'UTC';
        /**
         * @var string
         */
        private $id;
        /**
         * @var \DateTimeInterface
         */
        private $started;
        /**
         * @var int
         */
        private $allSteps;
        /**
         * @var int
         */
        private $stepsDone;
        /**
         * @var string
         */
        private $status;
        /**
         * @var \DateTimeInterface|null
         */
        private $lastUpdate;
        /**
         * @var \DateTimeInterface|null
         */
        private $estimated;
        /**
         * @var Delay\Delay
         */
        private $delay = null;
        /**
         * @var array
         */
        private $args;
        /**
         * Create a new multi-step schedule.
         *
         * @param int $steps
         * @param Delay\Delay|null $delay
         * @param array $args
         * @return Schedule
         */
        public static function newMultiStepInstance(int $steps, \Inpsyde\MultilingualPress\Schedule\Delay\Delay $delay = null, array $args = []) : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * Create a new single-step schedule.
         *
         * @return Schedule
         */
        public static function newMonoStepInstance() : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @param array $data
         * @return Schedule
         * @throws \RuntimeException
         */
        public static function fromArray(array $data) : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @param string $id
         * @param \DateTimeInterface $started
         * @param array $args
         * @param int $steps
         * @param int $stepsDone
         */
        private function __construct(string $id, \DateTimeInterface $started, array $args = [], int $steps = 1, int $stepsDone = 0)
        {
        }
        /**
         * @return string
         */
        public function id() : string
        {
        }
        /**
         * @return \DateTimeInterface
         */
        public function startedOn() : \DateTimeInterface
        {
        }
        /**
         * @return \DateTimeInterface
         */
        public function lastUpdate() : \DateTimeInterface
        {
        }
        /**
         * @return \DateTimeInterface|null
         */
        public function estimatedFinishTime()
        {
        }
        /**
         * @return string
         */
        public function estimatedRemainingTime() : string
        {
        }
        /**
         * @return bool
         */
        public function isMultiStep() : bool
        {
        }
        /**
         * @return int
         */
        public function stepToFinish() : int
        {
        }
        /**
         * @return bool
         */
        public function isDone() : bool
        {
        }
        /**
         * Force schedule to done status
         *
         * @return Schedule
         */
        public function done() : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @return Schedule
         */
        public function nextStep() : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @return array
         */
        public function toArray() : array
        {
        }
        /**
         * @return Delay\Delay
         */
        public function delay() : \Inpsyde\MultilingualPress\Schedule\Delay\Delay
        {
        }
    }
    /**
     * Make easier the schedule (via cron) of asynchronous tasks.
     *
     * For example:
     *
     * ```
     * $scheduler = new Scheduler();
     *
     * $ids = someFunctionThatCalculatesPostIdsToProcess();
     *
     * if ($ids && !get_option('my-schedule-is-running')) {
     *
     *     $scheduleId = $scheduler->newSchedule(count($ids), 'my-schedule-hook', $ids);
     *
     *     update_option('my-schedule-is-running', $scheduleId);
     * }
     *
     * add_action('my-schedule-hook', function ($scheduleArgs) use ($scheduler) {
     *
     *     list(
     *         $schedule,  // A Schedule object
     *         $step,      // The (zero-based) index for the step running
     *         $args       // The array of args for the schedule
     *     ) = $scheduler->parseScheduleHookParam($scheduleArgs);
     *
     *     if (!$schedule
     *         || get_option('my-schedule-is-running') !== $schedule->id()
     *         || $schedule->isDone()
     *         || empty($args[$step])
     *     ) {
     *         return;
     *     }
     *
     *     // Something like "X minutes", "or X hours", or "1 minute", properly translated.
     *     // For the first step it will be "Unknown" (translated).
     *     echo 'Estimated remaining time: ' . $schedule->estimatedRemainingTime();
     *
     *     $postToProcess = get_post($args[$step]);
     *
     *     // ... Process post here...
     *
     *     $scheduler->stepDone($schedule);  // This is **REQUIRED**
     *
     *     // In case what we just completed was the last step...
     *     if ($schedule->isDone()) {
     *         delete_option('my-schedule-is-running');
     *
     *         // We're doing this manually, but even if we don't, it'll be done via cron in 24 hours
     *         $scheduler->cleanup($schedule);
     *     }
     * }
     * ```
     *
     * So the hook is fired once per each step, passing the current step index as hook argument.
     *
     * The schedule object (among others) has a method to inform about its status, and a method to
     * inform about the estimated remaining time.
     *
     * Every time `$scheduler->stepDone` is called, the schedule is updated increasing the count of done
     * steps and a "last update" property is updated so that estimated remaining time can be calculated.
     * Plus the schedule can be marked as done when all steps are completed.
     *
     * Note that every time `$scheduler->newSchedule()` is called a *new* schedule is created, this is
     * why in the example the schedule id is stored in an option: to avoid to schedule more processes.
     *
     * @package Inpsyde\MultilingualPress\Schedule
     */
    class Scheduler
    {
        const OPTION = 'multilingualpress_cron_schedules';
        const ACTION_CLEANUP = 'multilingualpress.done-schedule-cleanup';
        const ACTION_SCHEDULED = 'multilingualpress.cron-scheduled';
        /**
         * Creates a new multi-step schedule.
         *
         * @param int $stepsCount
         * @param string $hook
         * @param array $args
         * @param Delay\Delay|null $delay
         * @return string
         */
        public function newSchedule(int $stepsCount, string $hook, array $args = [], \Inpsyde\MultilingualPress\Schedule\Delay\Delay $delay = null) : string
        {
        }
        /**
         * Tells scheduler that a step for given schedule just completed.
         *
         * @param Schedule $schedule
         * @return Schedule
         */
        public function stepDone(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule) : \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * Cleanups schedule of given ID.
         *
         * Use responsibly, will break running schedules.
         * `Scheduler::cleanupIfDone` can be used to only cleanup a schedule if completed.
         *
         * @param Schedule $schedule
         * @return bool
         */
        public function cleanup(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule) : bool
        {
        }
        /**
         * Cleanups schedule of given ID only if done.
         *
         * This is run via cron 24h after the schedule is marked as complete.
         *
         * @param string $scheduleId
         * @return bool
         */
        public function cleanupIfDone(string $scheduleId) : bool
        {
        }
        /**
         * @param \stdClass $param
         * @return array
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function parseScheduleHookParam($param) : array
        {
        }
        /**
         * Loads a Schedule object form its ID.
         *
         * @param string $scheduleId
         * @return Schedule|null
         */
        public function scheduleById(string $scheduleId)
        {
        }
        /**
         * @param Schedule $schedule
         * @param int $steps
         * @param string $hook
         * @param array $args
         * @return bool
         */
        private function persist(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule, int $steps = null, string $hook = null, array $args = null) : bool
        {
        }
    }
    /**
     * Service provider for all schedule objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstraps the registered services.
         *
         * @param Container $container
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication {
    /**
     * Handles (de)activation of all active plugins.
     */
    class ActivePlugins
    {
        /**
         * Fires the plugin activation hooks for all active plugins.
         *
         * @return int
         */
        public function activate() : int
        {
        }
        /**
         * Deactivates all plugins.
         *
         * @return bool
         */
        public function deactivate() : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\Schedule {
    class AttachmentDuplicatorHandler
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        /**
         * @var SiteScheduleOption
         */
        private $option;
        /**
         * @var Attachment\Duplicator
         */
        private $attachmentDuplicator;
        /**
         * @var Scheduler
         */
        private $scheduler;
        /**
         * @var Attachment\Collection
         */
        private $attachmentCollection;
        /**
         * @var Attachment\DatabaseDataReplacer
         */
        private $dataBaseDataReplacer;
        /**
         * AttachmentDuplicatorHandler constructor.
         * @param SiteScheduleOption $option
         * @param Attachment\Duplicator $attachmentDuplicator
         * @param Attachment\Collection $attachmentCollection
         * @param Scheduler $scheduler
         * @param Attachment\DatabaseDataReplacer $dataBaseDataReplacer
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $option, \Inpsyde\MultilingualPress\Attachment\Duplicator $attachmentDuplicator, \Inpsyde\MultilingualPress\Attachment\Collection $attachmentCollection, \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Attachment\DatabaseDataReplacer $dataBaseDataReplacer)
        {
        }
        /**
         * Handle the cron job request by copy an entire directory of attachments.
         * The $step identify the current directory to copy within the uploads directory.
         *
         * @wp-hook AttachmentDuplicatorScheduler::SCHEDULE_HOOK
         *
         * @param \stdClass $scheduleArgs
         *
         * @return bool
         * @throws Throwable
         */
        public function handle(\stdClass $scheduleArgs) : bool
        {
        }
        /**
         * Retrieve the List of the Attachments for the Source Site
         *
         * @param int $sourceSiteId
         * @param int $step
         * @return array
         */
        private function sourceAttachmentList(int $sourceSiteId, int $step) : array
        {
        }
        /**
         * @param \stdClass $args
         * @param array $attachments
         * @return bool
         */
        private function duplicate(\stdClass $args, array $attachments) : bool
        {
        }
        /**
         * Force the status for the given schedule to Done and clean up the scheduler
         *
         * @param Schedule $schedule
         */
        private function forceScheduleDoneStatus(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule)
        {
        }
    }
    /**
     * Class AttachmentDuplicatorScheduler
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class AttachmentDuplicatorScheduler
    {
        const FILTER_DEFAULT_COLLECTION_LIMIT = 'multilingualpress.attachment_duplicator_default_limit';
        const DEFAULT_COLLECTION_LIMIT = 100;
        const SCHEDULE_HOOK = 'multilingualpress.site_attachments_duplicator';
        /**
         * @var SiteScheduleOption
         */
        private $option;
        /**
         * @var Attachment\Collection
         */
        private $attachmentsCollection;
        /**
         * @var Scheduler
         */
        private $scheduler;
        /**
         * AttachmentDuplicatorScheduler constructor.
         * @param SiteScheduleOption $option
         * @param Attachment\Collection $attachmentsCollection
         * @param Scheduler $scheduler
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $option, \Inpsyde\MultilingualPress\Attachment\Collection $attachmentsCollection, \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler)
        {
        }
        /**
         * Schedule a new set of cron jobs to copy source site attachments into the new given site.
         *
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @throws UnexpectedValueException
         */
        public function schedule(int $sourceSiteId, int $newSiteId)
        {
        }
    }
    /**
     * Class MaybeScheduleAttachmentDuplication
     * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule
     */
    class MaybeScheduleAttachmentDuplication
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var AttachmentDuplicatorScheduler
         */
        private $attachmentDuplicatorScheduler;
        /**
         * MaybeScheduleAttachmentDuplication constructor.
         * @param Request $request
         * @param AttachmentDuplicatorScheduler $attachmentDuplicatorScheduler
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\SiteDuplication\Schedule\AttachmentDuplicatorScheduler $attachmentDuplicatorScheduler)
        {
        }
        /**
         * Schedule the attachment duplication if requested
         *
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @throws Throwable
         */
        public function maybeScheduleAttachmentsDuplication(int $sourceSiteId, int $newSiteId)
        {
        }
        /**
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @throws Throwable
         */
        protected function scheduleAttachmentDuplication(int $sourceSiteId, int $newSiteId)
        {
        }
    }
    /**
     * Class NewSiteScheduleTemplate
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class NewSiteScheduleTemplate
    {
        const ALLOWED_SCREEN_ID = 'site-new-network';
        /**
         * Render the template for the attachment schedule cron jobs
         * Used in the context of a new site to show information about the current status of the
         * attachment copy to the target site.
         *
         * @wp-hook admin_footer
         *
         * @return void
         */
        public function render()
        {
        }
        /**
         * Check against the current page. Ensuring it is the new site admin page
         *
         * @return bool
         */
        private function isNewSitePage() : bool
        {
        }
        /**
         * @return string
         */
        private function totalAttachmentsPart() : string
        {
        }
        /**
         * @return string
         */
        private function scheduleStepsTimeRemainingPart() : string
        {
        }
    }
    /**
     * Class RemoveAttachmentIdsTask
     * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule\Action
     */
    class RemoveAttachmentIdsTask implements \Inpsyde\MultilingualPress\Schedule\Action\ActionTask
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
        /**
         * @var SiteScheduleOption
         */
        private $siteScheduleOption;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var string
         */
        private $siteIdNameInRequest;
        /**
         * AttachmentsScheduleIdsRemoveAction constructor.
         * @param Request $request
         * @param SiteScheduleOption $siteScheduleOption
         * @param string $siteIdNameInRequest
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $siteScheduleOption, string $siteIdNameInRequest)
        {
        }
        /**
         * @inheritDoc
         * @throws Throwable
         */
        public function execute()
        {
        }
        /**
         * @return int
         * @throws UnexpectedValueException
         */
        protected function siteIdByRequest() : int
        {
        }
    }
    /**
     * Class ScheduleActionsNames
     * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule\Action
     */
    class ScheduleActionsNames
    {
        const STOP_ATTACHMENTS_COPY = 'stop_attachments_copy';
    }
    /**
     * Class ScheduleAssetManager
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class ScheduleAssetManager
    {
        const NAME_ATTACHMENT_SCHEDULE_ID = 'scheduleId';
        const NAME_SITE_ID = 'siteId';
        /**
         * @var SiteScheduleOption
         */
        private $siteScheduleOption;
        /**
         * @var AjaxScheduleHandler
         */
        private $ajaxScheduleHandler;
        /**
         * @var AssetManager
         */
        private $assetManager;
        /**
         * @var NonceFactory
         */
        private $scheduleActionsNonce;
        /**
         * ScheduleAssetManager constructor.
         * @param SiteScheduleOption $siteScheduleOption
         * @param AjaxScheduleHandler $ajaxScheduleHandler
         * @param AssetManager $assetManager
         * @param Nonce $scheduleActionsNonce
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $siteScheduleOption, \Inpsyde\MultilingualPress\Schedule\AjaxScheduleHandler $ajaxScheduleHandler, \Inpsyde\MultilingualPress\Framework\Asset\AssetManager $assetManager, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $scheduleActionsNonce)
        {
        }
        /**
         * Enqueue and Localize the main plugin script
         *
         * @return void
         * @throws AssetException
         */
        public function enqueueScript()
        {
        }
        /**
         * Retrieve the ajax schedule information url to call to obtain information about the current
         * status of the cron jobs
         *
         * @return string
         */
        protected function scheduleUrl() : string
        {
        }
        /**
         * @return array
         */
        protected function attachmentDuplicatorTranslations() : array
        {
        }
        /**
         * @return array
         */
        protected function attachmentDuplicatorActions() : array
        {
        }
    }
    /**
     * Class SiteScheduleOption
     *
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class SiteScheduleOption
    {
        use \Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
        const OPTION_SCHEDULE_IDS = 'multilingualpress.schedule_option_ids';
        /**
         * Create new schedule id for the given site
         *
         * @param int $siteId
         * @param string $scheduleId
         * @return bool
         * @throws UnexpectedValueException
         */
        public function createForSite(int $siteId, string $scheduleId) : bool
        {
        }
        /**
         * Retrieve the schedule id for the given site
         *
         * @param int $siteId
         * @return string
         * @throws UnexpectedValueException
         */
        public function readForSite(int $siteId) : string
        {
        }
        /**
         * Delete the schedule id for the given site
         *
         * @param int $siteId
         * @return bool
         * @throws UnexpectedValueException
         */
        public function deleteForSite(int $siteId) : bool
        {
        }
        /**
         * Retrieve all schedule
         *
         * @return array
         */
        public function allSchedule() : array
        {
        }
        /**
         * Update Schedule Id Option
         *
         * @param array $options
         * @return bool
         */
        private function updateScheduleId(array $options) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication {
    /**
     * Service provider for all site duplication objects.
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        const SITE_DUPLICATION_SUCCESS_ACTIONS_MESSAGES = 'siteDuplication.successActionsMessages';
        // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
        const SCHEDULE_ACTION_ATTACHMENTS_REMOVER_SERVICE = 'siteDuplication.scheduleActionAttachmentsRemover';
        const SCHEDULE_ACTION_ATTACHMENT_HANDLER_SERVICE = 'siteDuplication.scheduleActionAttachmentHandler';
        const SITE_DUPLICATION_ACTIONS = 'siteDuplication.actionsService';
        const FILTER_SUCCESS_ACTIONS_MESSAGES = 'multilingualpress.filter_success_actions_messages';
        const FILTER_SITE_DUPLICATION_ACTIONS = 'multilingualpress.site_duplication_actions';
        const SCHEDULE_ACTION_ATTACHMENTS_AJAX_HOOK_NAME = 'multilingualpress_site_duplicator_attachments_schedule_action';
        // phpcs:enable
        const SCHEDULE_ACTION_ATTACHMENTS_USER_REQUIRED_CAPABILITY = 'create_sites';
        const SCHEDULE_ACTION_ATTACHMENTS_NONCE_KEY = 'multilingualpress_attachment_duplicator_action';
        const MLP_TABLES = 'multilingualpress.mlpTables';
        const SITE_DUPLICATION_FILTER_MLP_TABLES = 'siteDuplication.filterMlpTables';
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @throws Throwable
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        protected function setupScriptsForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws Throwable
         */
        protected function duplicateSiteBackCompactBootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        protected function defineInitialSettingsBackCompactBootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @return Nonce
         */
        private function duplicateNonce(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : \Inpsyde\MultilingualPress\Framework\Nonce\Nonce
        {
        }
        /**
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        private function filterExcludedTables(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\Settings {
    /**
     * Site duplication "Plugins" setting.
     */
    final class ActivatePluginsSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-activate-plugins';
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Site duplication "Based on site" setting.
     */
    final class BasedOnSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var string
         */
        private $fieldId = 'mlp-base-site-id';
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @param \wpdb $db
         * @param Nonce $nonce
         */
        public function __construct(\wpdb $db, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
        /**
         * Renders the option tags.
         */
        private function renderSelectFieldOptions()
        {
        }
        /**
         * Returns all existing sites.
         *
         * @return string[][]
         */
        private function activeSites() : array
        {
        }
    }
    class ConnectCommentsSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        protected $inputId;
        public function __construct(string $inputId)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Site duplication "Connect Content" setting.
     */
    final class ConnectContentSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-connect-content';
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Class CopyAttachmentsSetting
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    final class CopyAttachmentsSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @inheritDoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritDoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Site duplication "Plugins" setting.
     */
    final class CopyUsersSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        private $id = 'mlp-copy-users';
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
    /**
     * Site duplication "Search Engine Visibility" setting.
     */
    final class SearchEngineVisibilitySetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        const FILTER_SEARCH_ENGINE_VISIBILITY = 'multilingualpress.search_engine_visibility';
        /**
         * @var string
         */
        private $fieldId = 'mlp-search-engine-visibility';
        /**
         * @inheritdoc
         */
        public function render(int $siteId)
        {
        }
        /**
         * @inheritdoc
         */
        public function title() : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication {
    /**
     * Handles duplication of a site.
     */
    class SiteDuplicator
    {
        const NAME_ACTIVATE_PLUGINS = 'mlp_activate_plugins';
        const NAME_BASED_ON_SITE = 'mlp_based_on_site';
        const NAME_OPTION_SITE_LANGUAGE = 'WPLANG';
        const NAME_SITE_RELATIONS = 'mlp_site_relations';
        const NAME_CONNECT_CONTENT = 'mlp_connect_content';
        const NAME_COPY_ATTACHMENTS = 'mlp_copy_attachments';
        const NAME_COPY_USERS = 'mlp_copy_users';
        public const NAME_CONNECT_COMMENTS = 'mlp_connect_comments';
        const DUPLICATE_ACTION_KEY = 'multilingualpress.duplicated_site';
        const FILTER_SITE_TABLES = 'multilingualpress.duplicate_site_tables';
        const FILTER_EXCLUDED_TABLES = 'multilingualpress.filter_excluded_tables';
        /**
         * @var ActivePlugins
         */
        private $activePlugins;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var \wpdb
         */
        private $wpdb;
        /**
         * @var Nonce
         */
        private $nonce;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var TableDuplicator
         */
        private $tableDuplicator;
        /**
         * @var TableList
         */
        private $tableList;
        /**
         * @var TableReplacer
         */
        private $tableReplacer;
        /**
         * @var ModuleManager
         */
        protected $moduleManager;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\TableList $tableList, \Inpsyde\MultilingualPress\Framework\Database\TableDuplicator $tableDuplicator, \Inpsyde\MultilingualPress\Framework\Database\TableReplacer $tableReplacer, \Inpsyde\MultilingualPress\SiteDuplication\ActivePlugins $activePlugins, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        /**
         * Duplicates a complete site to the new site just created.
         *
         * @param int $newSiteId
         * @return bool
         *
         * @throws Throwable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function duplicateSite(int $newSiteId) : bool
        {
        }
        /**
         * Returns the primary domain if domain mapping is active.
         *
         * @param int $sourceSiteId
         * @return string
         */
        private function mappedDomain(int $sourceSiteId) : string
        {
        }
        /**
         * Duplicates the tables of the given source site to the current site.
         *
         * @param array $tables
         * @param string $tablePrefix
         */
        private function duplicateTables(array $tables, string $tablePrefix)
        {
        }
        /**
         * @param int $sourceSiteId
         * @return array
         * @throws Throwable
         */
        protected function collectTables(int $sourceSiteId) : array
        {
        }
        /**
         * @param string $table
         * @param string $tablePrefix
         */
        private function duplicateTable(string $table, string $tablePrefix)
        {
        }
        /**
         * Sets the admin email address option to the given value.
         *
         * @param string $url
         * @param string $domain
         */
        private function updateUrls(string $url, string $domain)
        {
        }
        /**
         * Sets the admin email address option to the given value.
         *
         * Using update_option() would trigger a confirmation email to the new address, so we directly
         * manipulate the db.
         *
         * @param string $newAdminEmail
         */
        private function updateAdminEmail(string $newAdminEmail)
        {
        }
        /**
         * @param string $language The language we want to store into the db.
         *
         * @return void
         */
        private function updateSiteLanguage(string $language)
        {
        }
        /**
         * Renames the user roles option according to the given table prefix.
         *
         * @param string $newTablePrefix
         */
        private function renameUserRolesOption(string $newTablePrefix)
        {
        }
        /**
         * Adapts all active plugins according to the setting included in the request.
         */
        private function handlePlugins()
        {
        }
        /**
         * Triggers potential setup routines of the used theme.
         */
        private function handleTheme()
        {
        }
        /**
         * Sets up content relations between the source site and the new site.
         *
         * @param int $sourceSiteId
         * @param int $destinationSiteId
         */
        private function handleContentRelations(int $sourceSiteId, int $destinationSiteId)
        {
        }
        /**
         * If the appropriate option is selected then the users will be copied to the new site
         *
         * @param int $sourceSiteId the source site id which is selected in "Based on site" option
         * @param int $destinationSiteId the new created site id
         */
        protected function handleUsersCopy(int $sourceSiteId, int $destinationSiteId)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi {
    interface MetaboxFieldsHelperInterface
    {
        /**
         * Create the field ID from field key.
         *
         * @param string $fieldKey The field key.
         * @return string The field id.
         */
        public function fieldId(string $fieldKey) : string;
        /**
         * Create the field name from field key.
         *
         * @param string $fieldKey The field key.
         * @return string The field name.
         */
        public function fieldName(string $fieldKey) : string;
        /**
         * Get the value of a given field key from a given request.
         *
         * @param Request $request
         * @param string $fieldKey The field key.
         * @param null $default The default value.
         * @return mixed The request value.
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function fieldRequestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, string $fieldKey, $default = null);
    }
    class MetaboxFieldsHelper implements \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
    {
        public const NAME_PREFIX = 'multilingualpress';
        public const ID_PREFIX = 'multilingualpress-';
        /**
         * @var int
         */
        private $siteId;
        /**
         * @param int $siteId
         */
        public function __construct(int $siteId)
        {
        }
        /**
         * @param string $fieldKey
         * @return string
         */
        public function fieldId(string $fieldKey) : string
        {
        }
        /**
         * @param string $fieldKey
         * @return string
         */
        public function fieldName(string $fieldKey) : string
        {
        }
        /**
         * @param Request $request
         * @param string $fieldKey
         * @param null $default
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function fieldRequestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, string $fieldKey, $default = null)
        {
        }
    }
    /**
     * Can create helper for translation metabox fields.
     */
    interface MetaboxFieldsHelperFactoryInterface
    {
        /**
         * Creates a new metabox fields helper instance for given site.
         *
         * @param int $siteId The site ID.
         * @return MetaboxFieldsHelperInterface The new helper instance.
         * @throws RuntimeException If problem creating.
         */
        public function createMetaboxFieldsHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface;
    }
    class MetaboxFieldsHelperFactory implements \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createMetaboxFieldsHelper(int $siteId) : \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post\Ajax {
    class ContextBuilder
    {
        const SOURCE_SITE_PARAM = 'source_site_id';
        const SOURCE_POST_PARAM = 'source_post_id';
        const REMOTE_SITE_PARAM = 'remote_site_id';
        const REMOTE_POST_PARAM = 'remote_post_id';
        /**
         * @var Request
         */
        private $request;
        /**
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return RelationshipContext
         */
        public function build() : \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext
        {
        }
    }
    /**
     * Multilingualpress Relationship Ajax Updater for Posts
     */
    class RelationshipUpdater
    {
        const ACTION = 'multilingualpress_update_post_relationship';
        const TASK_PARAM = 'task';
        const TASK_METHOD_MAP = [\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_EXISTING => 'connectExistingPost', \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_REMOVE => 'disconnectPost', \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_NEW => 'newRelationPost'];
        /**
         * @var Request
         */
        private $request;
        /**
         * @var ContextBuilder
         */
        private $contextBuilder;
        /**
         * @var string
         */
        private $lastError = 'Unknown error.';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var ActivePostTypes
         */
        private $postTypes;
        /**
         * @var RelationshipPermission
         */
        private $relationshipPermission;
        /**
         * @param Request $request
         * @param ContextBuilder $contextBuilder
         * @param ContentRelations $contentRelations
         * @param ActivePostTypes $postTypes
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Handle AJAX request.
         *
         * @see RelationshipUpdater::connectExistingPost()
         * @see RelationshipUpdater::disconnectPost()
         * @see RelationshipUpdater::newRelationPost()
         */
        public function handle()
        {
        }
        /**
         * Connects the current post with an existing remote one.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        private function connectExistingPost(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : bool
        {
        }
        /**
         * New Relationship markup can be retrieved after the metabox is saved, in this case we simply
         * return true since the new post and the relationship already exists.
         *
         * This is used to fix the metabox not refreshing issue in Gutenberg.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        private function newRelationPost(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : bool
        {
        }
        /**
         * Disconnects the current post with the one given in the request.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        private function disconnectPost(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : bool
        {
        }
    }
    /**
     * @psalm-type postId = int
     * @psalm-type title = string
     */
    class Search
    {
        public const ACTION = 'multilingualpress_remote_post_search';
        public const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_post_search_arguments';
        /**
         * @var Request
         */
        private $request;
        /**
         * @var ContextBuilder
         */
        private $contextBuilder;
        /**
         * @var string
         */
        protected $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder, string $alreadyConnectedNotice)
        {
        }
        /**
         * Handle AJAX request.
         */
        public function handle()
        {
        }
        /**
         * Finds the post by given search query.
         *
         * @param string $searchQuery The search query.
         * @param RelationshipContext $context
         * @return array<int, string> A map of post ID to post title.
         * @psalm-return array<postId, title>
         * @throws NonexistentTable
         */
        public function findPosts(string $searchQuery, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * Checks if the post with given post ID is connected to any post from given site ID.
         *
         * @param int $postId The post ID.
         * @param int $siteId The site ID.
         * @return bool true if is connected, otherwise false.
         * @throws NonexistentTable
         */
        protected function isConnectedWithPostOfSite(int $postId, int $siteId) : bool
        {
        }
    }
    class Term
    {
        const ACTION = 'multilingualpress_remote_terms';
        const TAXONOMIES = 'taxonomies';
        /**
         * @var Request
         */
        private $request;
        /**
         * @var ContextBuilder
         */
        private $contextBuilder;
        /**
         * @param Request $request
         * @param ContextBuilder $contextBuilder
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder)
        {
        }
        /**
         * Handle AJAX request.
         */
        public function handle()
        {
        }
        /**
         * The Method is used to return current editing post taxonomy name from request
         *
         * @return array Taxonomy name of current editing post
         */
        protected function taxNameFromRequest() : array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post\Field {
    class Base
    {
        /**
         * @var string
         */
        private $key;
        /**
         * Relation constructor.
         * @param string $key
         */
        public function __construct(string $key)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param bool $hasRemotePost
         * @return string
         */
        private function label(bool $hasRemotePost) : string
        {
        }
    }
    class ChangedFields implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        protected const FILTER_FIELD_FIELDS_ARE_CHANGED = 'multilingualpress.field_changed_fields';
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class CopyContent
    {
        const FILTER_COPY_CONTENT_IS_CHECKED = 'multilingualpress.copy_content_is_checked';
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class CopyFeaturedImage
    {
        const FILTER_COPY_FEATURED_IMAGE_IS_CHECKED = 'multilingualpress.copy_featured_image_is_checked';
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class CopyTaxonomies
    {
        const FILTER_COPY_TAXONOMIES_IS_CHECKED = 'multilingualpress.copy_taxonomies_is_checked';
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class EditLink
    {
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class Excerpt
    {
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class Relation
    {
        const VALUES = [\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_NEW, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_EXISTING, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_REMOVE, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_LEAVE];
        protected const PREFIX_FOR_RELATION_MESSAGE_FILTER = 'multilingualpress.translation_ui.relation_message_';
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $key
         * @return string[]
         */
        private function idAndName(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $key) : array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         * @param RelationshipContext $context
         */
        protected function newPostField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         * @param RelationshipContext $context
         */
        protected function existingPostField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function removeConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param bool $hasRemotePost
         * @param RelationshipContext $context
         */
        protected function leaveConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, bool $hasRemotePost, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @return void
         */
        protected function searchRow(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @return void
         */
        protected function buttonRow()
        {
        }
    }
    class Status
    {
        const FILTER_TRANSLATION_UI_POST_STATUSES = 'multilingualpress.translation_ui_post_statuses';
        /**
         * @var array
         */
        protected static $statues;
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @return array
         */
        protected static function statuses() : array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class Taxonomies
    {
        const FILTER_SINGLE_TERM_TAXONOMIES = 'multilingualpress.single_term_taxonomies';
        const FILTER_TRANSLATION_UI_SELECT_THRESHOLD = 'multilingualpress.translation_ui_select_threshold';
        const FILTER_TRANSLATION_UI_USE_SELECT = 'multilingualpress.translation_ui_taxonomies_use_select';
        /**
         * @var \WP_Taxonomy
         */
        private $taxonomy;
        /**
         * @var \WP_Term[]
         */
        private $terms;
        /**
         * @param $value
         * @return int[]
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : array
        {
        }
        /**
         * @param \WP_Taxonomy $taxonomy
         * @param \WP_Term[] ...$terms
         */
        public function __construct(\WP_Taxonomy $taxonomy, \WP_Term ...$terms)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param array $assignedIds
         * @param string $idBase
         * @param string $name
         * @param string $type
         */
        private function renderSelect(array $assignedIds, string $idBase, string $name, string $type)
        {
        }
        /**
         * @param array $assignedIds
         * @param string $name
         * @param string $type
         * @param int $siteId
         */
        private function renderInputs(array $assignedIds, string $name, string $type, int $siteId)
        {
        }
        /**
         * @param int $termCount
         * @return string
         */
        private function inputType(int $termCount) : string
        {
        }
    }
    class TaxonomySlugs
    {
        const FILTER_FIELD_TAXONOMY_SLUGS = 'multilingualpress.field_taxonomy_slugs';
        /**
         * @param $value
         * @return int[]
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * A Walker_Category_Checklist to use radio instead of checkboxes when necessary, and to replace
     * the input name attribute and the category id attribute.
     *
     * @package Inpsyde\MultilingualPress\TranslationUi\Post\Field
     */
    class TaxonomyWalker extends \Walker_Category_Checklist
    {
        /**
         * @var string
         */
        private $name;
        /**
         * @var string
         */
        private $type;
        /**
         * @var int
         */
        private $siteId;
        /**
         * @param string $name
         * @param string $type
         * @param int $siteId
         */
        public function __construct(string $name, string $type, int $siteId)
        {
        }
        /**
         * @param $output
         * @param $category
         * @param int $depth
         * @param array $args
         * @param int $id
         *
         * phpcs:disable
         */
        public function start_el(&$output, $category, $depth = 0, $args = [], $id = 0)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    final class Metabox implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox
    {
        const RELATIONSHIP_TYPE = 'post';
        const ID_PREFIX = 'multilingualpress_post_translation_metabox_';
        const HOOK_PREFIX = 'multilingualpress.post_translation_metabox_';
        /**
         * @var int
         */
        private $sourceSiteId;
        /**
         * @var int
         */
        private $remoteSiteId;
        /**
         * @var ActivePostTypes
         */
        private $postTypes;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var RelationshipPermission
         */
        private $relationshipPermission;
        /**
         * @var MetaboxFieldsHelper
         */
        private $fieldsHelper;
        /**
         * @var RelationshipContext
         */
        private $relationshipContext;
        /**
         * @param int $sourceSiteSite
         * @param int $remoteSiteId
         * @param ActivePostTypes $postTypes
         * @param ContentRelations $contentRelations
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(int $sourceSiteSite, int $remoteSiteId, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Returns the site ID for the meta box
         *
         * @return int
         */
        public function siteId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info
        {
        }
        /**
         * @inheritdoc
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
        {
        }
        /**
         * @inheritdoc
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
        /**
         * Returns the meta box title for the site with the given ID
         *
         * @return string
         */
        private function buildBoxTitle() : string
        {
        }
        /**
         * Retrieve the context for the relationship
         *
         * @param \WP_Post $sourcePost
         * @return RelationshipContext
         */
        private function relationshipContext(\WP_Post $sourcePost) : \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext
        {
        }
    }
    /**
     * Class MetaboxAction
     */
    final class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
        const FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE = 'multilingualpress.taxonomies_slugs_before_remove';
        const FILTER_NEW_RELATE_REMOTE_POST_BEFORE_INSERT = 'multilingualpress.new_relate_remote_post_before_insert';
        const ACTION_METABOX_AFTER_RELATE_POSTS = 'multilingualpress.metabox_after_relate_posts';
        const ACTION_METABOX_BEFORE_UPDATE_REMOTE_POST = 'multilingualpress.metabox_before_update_remote_post';
        const ACTION_METABOX_AFTER_UPDATE_REMOTE_POST = 'multilingualpress.metabox_after_update_remote_post';
        // phpcs:enable
        /**
         * @var array
         */
        private static $calledCount = [];
        /**
         * @var MetaboxFields
         */
        private $fields;
        /**
         * @var MetaboxFieldsHelper
         */
        private $fieldsHelper;
        /**
         * @var RelationshipContext
         */
        private $relationshipContext;
        /**
         * @var ActivePostTypes
         */
        private $postTypes;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var SourcePostSaveContext
         */
        private $sourcePostContext;
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $fieldsHelper
         * @param RelationshipContext $relationshipContext
         * @param ActivePostTypes $postTypes
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $fieldsHelper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * @param Request $request
         * @return string
         */
        private function saveOperation(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : string
        {
        }
        /**
         * @param Request $request
         * @return SourcePostSaveContext
         */
        private function sourceContext(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : \Inpsyde\MultilingualPress\TranslationUi\Post\SourcePostSaveContext
        {
        }
        /**
         * Generate the remote post data
         *
         * @param array<string, scalar|null> $values A map of
         * {@link https://developer.wordpress.org/reference/classes/wp_post/ WP_Post} data field names to values
         * @param PostRelationSaveHelper $relationshipHelper
         * @return array<string, scalar|null> A map of
         * {@link https://developer.wordpress.org/reference/classes/wp_post/ WP_Post} data field names to values
         * @throws NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        private function generatePostData(array $values, \Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper $relationshipHelper) : array
        {
        }
        /**
         * @param string $operation
         * @param Request $request
         * @param PostRelationSaveHelper $relationshipHelper
         * @param PersistentAdminNotices $notices
         * @return bool
         */
        private function doSaveOperation(string $operation, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper $relationshipHelper, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * Check if the current request should be processed by save().
         *
         * @param SourcePostSaveContext $context
         * @return bool
         */
        private function isValidSaveRequest(\Inpsyde\MultilingualPress\TranslationUi\Post\SourcePostSaveContext $context) : bool
        {
        }
        /**
         * @param Request $request
         * @return array
         */
        private function allFieldsValues(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * @param MetaboxTab $tab
         * @param Request $request
         * @return array
         */
        private function tabFieldsValues(\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxTab $tab, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * @param string $operation
         * @param array $post
         * @param PostRelationSaveHelper $helper
         * @param Request $request
         * @param PersistentAdminNotices $notices
         * @return int
         */
        private function savePost(string $operation, array $post, \Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper $helper, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : int
        {
        }
        /**
         * @param array $taxonomyTerms
         * @param array $taxonomies
         */
        private function saveTaxonomyTerms(array $taxonomyTerms, array $taxonomies)
        {
        }
        /**
         * Changes post status if condition match
         *
         * @param string $status
         * @param bool $hasRemote
         * @return string
         */
        private function maybeChangePostStatus(string $status, bool $hasRemote) : string
        {
        }
        /**
         * Replace the reusable blocks.
         *
         * If reusable gutenberg block exists in source post content and if it is connected with the reusable block in
         * remote site, then we need to replace it's id with the id of remote block
         *
         * @param int $sourcePostId The source post id
         * @param string $sourcePostContent The source post content
         * @param int $sourceSiteId The source site id
         * @param int $remoteSiteId The remote site id
         * @return string The post content with replaced reusable block ids from remote site
         * @throws NonexistentTable
         */
        protected function handleReusableBlocks(int $sourcePostId, string $sourcePostContent, int $sourceSiteId, int $remoteSiteId) : string
        {
        }
        /**
         * Check if the field with given name is changed
         *
         * @param string $field The field name to check
         * @param array $changedFields The list of changed fields
         * @return bool Whether the field is changed
         */
        protected function isFieldChanged(string $field, array $changedFields) : bool
        {
        }
        /**
         * Check if the field with given name was not set when creating a new connection
         *
         * @param string $field The field name to check
         * @param bool $hasRemote whether the current post is already connected
         * @return bool Whether the field was not set when a new connection is created
         */
        protected function newRemotePostFieldIsEmpty(string $field, bool $hasRemote) : bool
        {
        }
    }
    final class MetaboxField implements \Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField
    {
        const ACTION_AFTER_TRANSLATION_UI_FIELD = 'multilingualpress.after_translation_ui_field';
        const ACTION_BEFORE_TRANSLATION_UI_FIELD = 'multilingualpress.before_translation_ui_field';
        const FILTER_TRANSLATION_UI_SHOW_FIELD = 'multilingualpress.translation_ui_show_field';
        /**
         * @var string
         */
        private $key;
        /**
         * @var callable
         */
        private $renderCallback;
        /**
         * @var callable
         */
        private $sanitizer;
        /**
         * @param string $key
         * @psalm-param callable(MetaboxFieldsHelper, RelationshipContext): void $renderCallback
         * @param callable $renderCallback
         * @param callable|null $sanitizer
         */
        public function __construct(string $key, callable $renderCallback, callable $sanitizer = null)
        {
        }
        /**
         * @return string
         */
        public function key() : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * @param Request $request
         * @param MetaboxFieldsHelper $helper
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool
        {
        }
    }
    class MetaboxFields
    {
        const TAB_BASE = 'tab-base';
        const TAB_EXCERPT = 'tab-excerpt';
        const TAB_MORE = 'tab-more';
        const TAB_RELATION = 'tab-relation';
        const TAB_TAXONOMIES = 'tab-taxonomies';
        const FIELD_RELATION = 'relationship';
        const FIELD_RELATION_NEW = 'new';
        const FIELD_RELATION_EXISTING = 'existing';
        const FIELD_RELATION_REMOVE = 'remove';
        const FIELD_RELATION_LEAVE = 'leave';
        const FIELD_RELATION_NOTHING = 'nothing';
        const FIELD_RELATION_SEARCH = 'search_post_id';
        const FIELD_EXCERPT = 'remote-excerpt';
        const FIELD_TITLE = 'remote-title';
        const FIELD_SLUG = 'remote-slug';
        const FIELD_STATUS = 'remote-status';
        const FIELD_COPY_FEATURED = 'remote-thumbnail-copy';
        const FIELD_COPY_CONTENT = 'remote-content-copy';
        const FIELD_COPY_TAXONOMIES = 'remote-taxonomies-copy';
        const FIELD_TAXONOMIES = 'remote-taxonomies';
        const FIELD_TAXONOMY_SLUGS = 'remote-taxonomy-slugs';
        const FIELD_EDIT_LINK = 'edit-link';
        const FIELD_CHANGED_FIELDS = 'changed-fields';
        const FILTER_TAXONOMIES_AND_TERMS_OF = 'multilingualpress.taxonomies_and_terms_of';
        const FILTER_MAX_NUMBER_OF_TERMS = 'multilingualpress.max_number_of_terms';
        /**
         * Get all existing taxonomies for the given post, including all existing terms.
         *
         * @param \WP_Post $post
         * @return \stdClass[]
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public static function taxonomiesAndTermsOf(\WP_Post $post) : array
        {
        }
        /**
         * @param RelationshipContext $context
         * @return array
         */
        public function allFieldsTabs(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * @return array
         */
        private function relationFields() : array
        {
        }
        /**
         * @param RelationshipContext $context
         * @return MetaboxField[]
         */
        private function baseFields(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * @return MetaboxField[]
         */
        private function excerptFields() : array
        {
        }
        /**
         * @param RelationshipContext $context
         * @return MetaboxField[]
         */
        private function moreFields(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * @param RelationshipContext $context
         * @return MetaboxField[]
         */
        private function taxonomiesFields(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : array
        {
        }
        /**
         * Will create a new hidden metabox field for detecting changed fields with JS
         *
         * @return MetaboxField
         */
        public function changedFieldsField() : \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField
        {
        }
    }
    class MetaboxTab implements \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFillable
    {
        const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
        const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
        const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.translation_ui_show_tab';
        /**
         * @var string
         */
        private $id;
        /**
         * @var MetaboxField[]
         */
        private $fields;
        /**
         * @var string
         */
        private $label;
        /**
         * @param string $id
         * @param string $label
         * @param MetaboxField[] ...$fields
         */
        public function __construct(string $id, string $label, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField ...$fields)
        {
        }
        /**
         * @return string
         */
        public function id() : string
        {
        }
        /**
         * @return string
         */
        public function label() : string
        {
        }
        /**
         * @return MetaboxField[]
         */
        public function fields() : array
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext) : bool
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    final class MetaboxView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @var MetaboxFields
         */
        private $fields;
        /**
         * @var MetaboxFieldsHelper
         */
        private $helper;
        /**
         * @var RelationshipContext
         */
        private $relationshipContext;
        /**
         * @var ChangedFields
         */
        private $fieldsAreChangedInput;
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @param ChangedFields $fieldsAreChangedInput
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\TranslationUi\Post\Field\ChangedFields $fieldsAreChangedInput)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info)
        {
        }
        /**
         * @return void
         */
        private function boxDataAttributes()
        {
        }
        /**
         * @param MetaboxFillable $tab
         */
        private function renderTabAnchor(\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFillable $tab)
        {
        }
        /**
         * @return void
         */
        private function renderTrashedMessage()
        {
        }
        /**
         * Retrieve the post edit link for the remote post
         *
         * @return string
         */
        private function remotePostUrl() : string
        {
        }
    }
    class PostModifiedDateFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        public function __construct()
        {
        }
        /**
         * @param array $data
         * @param array $postarr
         * @return array
         */
        public function doNotUpdateModifiedDate(array $data, array $postarr) : array
        {
        }
    }
    class PostRelationSaveHelper
    {
        const FILTER_METADATA = 'multilingualpress.post_meta_data';
        const FILTER_SYNC_KEYS = 'multilingualpress.sync_post_meta_keys';
        const ACTION_BEFORE_SAVE_RELATIONS = 'multilingualpress.before_save_posts_relations';
        const ACTION_AFTER_SAVED_RELATIONS = 'multilingualpress.after_saved_posts_relations';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param RelationshipContext $context
         * @return int
         */
        public function relatedPostParent(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : int
        {
        }
        /**
         * Set the source id of the element.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        public function relatePosts(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : bool
        {
        }
        /**
         * @param RelationshipContext $context
         * @param Request $request
         */
        public function syncMetadata(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @param RelationshipContext $context
         * @return bool
         */
        public function syncThumb(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : bool
        {
        }
        /**
         * Sync terms from source post to remote post.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        public function syncTaxonomyTerms(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context) : bool
        {
        }
        /**
         * @param int $remoteSiteId
         * @return int
         */
        private function maybeSwitchSite(int $remoteSiteId) : int
        {
        }
        /**
         * @param int $originalSiteId
         * @return bool
         */
        private function maybeRestoreSite(int $originalSiteId) : bool
        {
        }
    }
    /**
     * Relationship context data object.
     */
    class RelationshipContext
    {
        const REMOTE_POST_ID = 'remote_post_id';
        const REMOTE_SITE_ID = 'remote_site_id';
        const SOURCE_POST_ID = 'source_post_id';
        const SOURCE_SITE_ID = 'source_site_id';
        const DEFAULTS = [self::REMOTE_POST_ID => 0, self::REMOTE_SITE_ID => 0, self::SOURCE_POST_ID => 0, self::SOURCE_SITE_ID => 0];
        /**
         * @var \WP_Post[]
         */
        private $posts = [];
        /**
         * @var array
         */
        private $data;
        /**
         * Returns a new context object, instantiated according to the data in the given context object
         * and the array.
         *
         * @param RelationshipContext $context
         * @param array $data
         * @return RelationshipContext
         */
        public static function fromExistingAndData(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, array $data) : \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext
        {
        }
        /**
         * @param array $data
         */
        public function __construct(array $data = [])
        {
        }
        /**
         * Returns the remote post ID.
         *
         * @return int
         */
        public function remotePostId() : int
        {
        }
        /**
         * Returns the remote site ID.
         *
         * @return int
         */
        public function remoteSiteId() : int
        {
        }
        /**
         * Returns the source post ID.
         *
         * @return int
         */
        public function sourcePostId() : int
        {
        }
        /**
         * Returns the source site ID.
         *
         * @return int
         */
        public function sourceSiteId() : int
        {
        }
        /**
         * Returns the source post object.
         *
         * @return bool
         */
        public function hasRemotePost() : bool
        {
        }
        /**
         * Returns the source post object.
         *
         * @return \WP_Post|null
         */
        public function remotePost()
        {
        }
        /**
         * Returns the source post object.
         *
         * @return \WP_Post
         */
        public function sourcePost() : \WP_Post
        {
        }
        /**
         * Print HTML fields for the relationship context.
         * @param MetaboxFieldsHelper $helper
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * Returns the source post object.
         *
         * @param int $siteId
         * @param string $type
         * @return \WP_Post|null
         */
        private function post(int $siteId, string $type)
        {
        }
    }
    /**
     * Permission checker to be used to either permit or prevent access to posts.
     */
    class RelationshipPermission
    {
        const FILTER_IS_RELATED_POST_EDITABLE = 'multilingualpress.is_related_post_editable';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var int[][]
         */
        private $relatedPosts = [];
        /**
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Checks if the current user can edit (or create) a post in the site with the given ID that is
         * related to given post in the current site.
         *
         * @param \WP_Post $post
         * @param int $relatedSiteId
         * @return bool
         */
        public function isRelatedPostEditable(\WP_Post $post, int $relatedSiteId) : bool
        {
        }
        /**
         * Returns the ID of the post in the site with the given ID that is related to given post in
         * the current site.
         *
         * @param \WP_Post $post
         * @param int $relatedSiteId
         * @return \WP_Post|null
         */
        private function relatedPost(\WP_Post $post, int $relatedSiteId)
        {
        }
        /**
         * Returns an array with the IDs of all related posts for the post with the given ID.
         *
         * @param int $postId
         * @return int[]
         */
        private function relatedPosts(int $postId) : array
        {
        }
    }
    class SourcePostSaveContext
    {
        const POST_TYPE = 'real_post_type';
        const POST_ID = 'real_post_id';
        const POST = 'post';
        const POST_STATUS = 'original_post_status';
        const FEATURED_IMG_PATH = 'featured_image_path';
        const CONNECTABLE_STATUSES = ['auto-draft', 'draft', 'future', 'private', 'publish'];
        /**
         * @var \WP_Post
         */
        private $sourcePost;
        /**
         * @var ActivePostTypes
         */
        private $postTypes;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var string
         */
        private $postType;
        /**
         * @var string
         */
        private $postStatus;
        /**
         * @var string
         */
        private $thumbPath;
        /**
         * @param \WP_Post $sourcePost
         * @param ActivePostTypes $postTypes
         * @param Request $request
         */
        public function __construct(\WP_Post $sourcePost, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return string
         */
        public function postType() : string
        {
        }
        /**
         * @return string
         */
        public function postStatus() : string
        {
        }
    }
    /**
     * Class TableList
     * @package Inpsyde\MultilingualPress\TranslationUi\Post
     */
    class TableList
    {
        const RELATION_TYPE = 'post';
        const EDIT_TRANSLATIONS_COLUMN_NAME = 'translations';
        const FILTER_SITE_LANGUAGE_TAG = 'multilingualpress.site_language_tag';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * TableList constructor.
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param array $postsColumns
         * @return array
         */
        public function editTranslationColumns(array $postsColumns) : array
        {
        }
        /**
         * @param string $columnName
         * @param int $postId
         * @return void
         */
        public function editTranslationLinks(string $columnName, int $postId)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi {
    /**
     * Service provider for all translation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const CONFIGURATION_NAME_FOR_ALREADY_CONNECTED_ENTITY_NOTICE = 'multilingualpress.TranslationUi.AlreadyConnectedEntityNotice';
        public const FILTER_NAME_FOR_ALREADY_CONNECTED_ENTITY_NOTICE = 'multilingualpress.TranslationUi.already_connected_entity_notice';
        /**
         * @inheritdoc
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        private function registerForTerm(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        private function registerForPost(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         * @param Container $container
         * @throws Throwable
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Delete the relation when a content is permanently deleted
         *
         * @param Container $container
         * @throws NonexistentTable
         */
        private function deleteRelationOnContentDelete(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstrap the Table Lists
         *
         * @param Container $container
         * @throws Throwable
         */
        private function bootstrapTablesLists(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstrap the Post Type table lists
         *
         * @param Container $container
         * @throws Throwable
         */
        private function bootstrapPostTypeTablesLists(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstrap the Taxonomy table list
         *
         * @param Container $container
         * @throws Throwable
         */
        private function bootstrapTaxonomyTablesLists(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param int $currentSite
         * @param int $relatedSite
         * @param Container $container
         * @return array
         */
        private function createBoxes(int $currentSite, int $relatedSite, \Inpsyde\MultilingualPress\Framework\Service\Container $container) : array
        {
        }
        /**
         * @param Container $container
         */
        private function bootstrapAjax(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Bootstraps the functionality for relationship metaboxes.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService | NameNotFound | NonexistentTable
         */
        protected function bootstrapRelationshipMetaboxes(\Inpsyde\MultilingualPress\Framework\Service\Container $container) : void
        {
        }
        /**
         * Renders the relationship metaboxes with given name for given screen and post ID.
         *
         * @param int $postId The post ID.
         * @param array<PostMetaboxRendererInterface> $relationshipMetaBoxRenderers The list of relationship metaboxes.
         * @param WP_Screen $screen
         * @param string $relationshipMetaName
         */
        protected function renderRelationshipMetaboxes(int $postId, array $relationshipMetaBoxRenderers, \WP_Screen $screen, string $relationshipMetaName) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term\Ajax {
    class ContextBuilder
    {
        const SOURCE_SITE_PARAM = 'source_site_id';
        const SOURCE_TERM_PARAM = 'source_term_id';
        const REMOTE_SITE_PARAM = 'remote_site_id';
        const REMOTE_TERM_PARAM = 'remote_term_id';
        /**
         * @var Request
         */
        private $request;
        /**
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return RelationshipContext
         */
        public function build() : \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext
        {
        }
    }
    class RelationshipUpdater
    {
        const ACTION = 'multilingualpress_update_term_relationship';
        const TASK_PARAM = 'task';
        const TASK_METHOD_MAP = [\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_EXISTING => 'connectExistingTerm', \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_REMOVE => 'disconnectTerm'];
        /**
         * @var Request
         */
        private $request;
        /**
         * @var ContextBuilder
         */
        private $contextBuilder;
        /**
         * @var string
         */
        private $lastError = 'Unknown error.';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var ActiveTaxonomies
         */
        private $taxonomies;
        /**
         * @var RelationshipPermission
         */
        private $relationshipPermission;
        /**
         * @param Request $request
         * @param ContextBuilder $contextBuilder
         * @param ContentRelations $contentRelations
         * @param ActiveTaxonomies $taxonomies
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Term\Ajax\ContextBuilder $contextBuilder, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $taxonomies, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Handle AJAX request.
         *
         * @see RelationshipUpdater::connectExistingTerm()
         * @see RelationshipUpdater::disconnectTerm()
         */
        public function handle()
        {
        }
        /**
         * Connects the current term with an existing remote one.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        private function connectExistingTerm(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context) : bool
        {
        }
        /**
         * Disconnects the current term with the one given in the request.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        private function disconnectTerm(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context) : bool
        {
        }
    }
    /**
     * @psalm-type termId = int
     * @psalm-type title = string
     */
    class Search
    {
        public const ACTION = 'multilingualpress_remote_term_search_arguments';
        public const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_term_search_arguments';
        /**
         * @var Request
         */
        private $request;
        /**
         * @var ContextBuilder
         */
        private $contextBuilder;
        /**
         * @var string
         */
        protected $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Term\Ajax\ContextBuilder $contextBuilder, string $alreadyConnectedNotice)
        {
        }
        /**
         * Handle AJAX request.
         */
        public function handle()
        {
        }
        /**
         * Finds the term by given search query.
         *
         * @param string $searchQuery The search query.
         * @param RelationshipContext $context
         * @return array<int, string> A map of term ID to term title.
         * @psalm-return array<termId, title>
         * @throws NonexistentTable
         */
        public function findTerm(string $searchQuery, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context) : array
        {
        }
        /**
         * Checks if the term with given term ID is connected to any term from given site ID.
         *
         * @param int $termId The term ID.
         * @param int $siteId The site ID.
         * @return bool true if is connected, otherwise false.
         * @throws NonexistentTable
         */
        protected function isConnectedWithTermOfSite(int $termId, int $siteId) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term\Field {
    class Base
    {
        /**
         * @var string
         */
        private $key;
        /**
         * Relation constructor.
         * @param string $key
         */
        public function __construct(string $key)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
        /**
         * @param bool $hasRemoteTerm
         * @return string
         */
        private function label(bool $hasRemoteTerm) : string
        {
        }
    }
    class Description
    {
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
    }
    class ParentTerm
    {
        /**
         * @param $value
         * @return int
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : int
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
    }
    class Relation
    {
        const VALUES = [\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_NEW, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_EXISTING, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_REMOVE, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_LEAVE];
        protected const PREFIX_FOR_TERM_RELATION_MESSAGE_FILTER = 'multilingualpress.term.translation_ui.relation_message_';
        /**
         * @param $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value) : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         * @throws NonexistentTable
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $key
         * @return string[]
         */
        private function idAndName(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $key) : array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function newTermField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function existingTermField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function removeConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param bool $hasRemoteTerm
         */
        protected function leaveConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, bool $hasRemoteTerm)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @return void
         */
        protected function searchRow(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @return void
         */
        protected function buttonRow()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term {
    final class Metabox implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox
    {
        const RELATIONSHIP_TYPE = 'term';
        const ID_PREFIX = 'multilingualpress_term_translation_metabox_';
        const HOOK_PREFIX = 'multilingualpress_.term_translation_metabox_';
        /**
         * @var int
         */
        private $sourceSiteId;
        /**
         * @var int
         */
        private $remoteSiteId;
        /**
         * @var ActiveTaxonomies
         */
        private $taxonomies;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var RelationshipPermission
         */
        private $relationshipPermission;
        /**
         * @var MetaboxFieldsHelper
         */
        private $fieldsHelper;
        /**
         * @var RelationshipContext
         */
        private $relationshipContext;
        /**
         * @param int $sourceSiteSite
         * @param int $remoteSiteId
         * @param ActiveTaxonomies $taxonomies
         * @param ContentRelations $contentRelations
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(int $sourceSiteSite, int $remoteSiteId, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $taxonomies, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Returns the site ID for the meta box.
         * @return int
         */
        public function siteId() : int
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity) : bool
        {
        }
        /**
         * @inheritdoc
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info
        {
        }
        /**
         * @inheritdoc
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
        {
        }
        /**
         * @inheritdoc
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity) : \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
        /**
         * Returns the meta box title for the site with the given ID.
         *
         * @return string
         */
        private function buildBoxTitle() : string
        {
        }
        /**
         * @param \WP_Term $sourceTerm
         * @return RelationshipContext
         */
        private function relationshipContext(\WP_Term $sourceTerm) : \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext
        {
        }
    }
    final class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        const ACTION_METABOX_AFTER_RELATE_TERMS = 'multilingualpress.metabox_after_relate_terms';
        const ACTION_BEFORE_UPDATE_REMOTE_TERM = 'multilingualpress.metabox_before_update_remote_term';
        const ACTION_AFTER_UPDATE_REMOTE_TERM = 'multilingualpress.metabox_after_update_remote_term';
        /**
         * @var MetaboxFields
         */
        private $fields;
        /**
         * @var MetaboxFieldsHelper
         */
        private $fieldsHelper;
        /**
         * @var RelationshipContext
         */
        private $relationshipContext;
        /**
         * @var ActiveTaxonomies
         */
        private $taxonomies;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $fieldsHelper
         * @param RelationshipContext $relationshipContext
         * @param ActiveTaxonomies $taxonomies
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $fieldsHelper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $taxonomies, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * @param Request $request
         * @return string
         */
        private function saveOperation(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : string
        {
        }
        /**
         * @param array $values
         * @param TermRelationSaveHelper $relationshipHelper
         * @return array
         */
        private function generateTermData(array $values, \Inpsyde\MultilingualPress\TranslationUi\Term\TermRelationSaveHelper $relationshipHelper, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * @param Request $request
         * @param TermRelationSaveHelper $relationshipHelper
         * @param PersistentAdminNotices $notices
         * @return bool
         */
        private function doSaveOperation(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Term\TermRelationSaveHelper $relationshipHelper, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : bool
        {
        }
        /**
         * @param Request $request
         * @return array
         */
        private function allFieldsValues(\Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * @param MetaboxTab $tab
         * @param Request $request
         * @return array
         */
        private function tabFieldsValues(\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxTab $tab, \Inpsyde\MultilingualPress\Framework\Http\Request $request) : array
        {
        }
        /**
         * @param array $termData
         * @param TermRelationSaveHelper $helper
         * @param Request $request
         * @param PersistentAdminNotices $notices
         * @return int
         */
        private function saveTerm(array $termData, \Inpsyde\MultilingualPress\TranslationUi\Term\TermRelationSaveHelper $helper, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices) : int
        {
        }
        /**
         * @param array $termData
         * @return int
         */
        private function updateTerm(array $termData) : int
        {
        }
        /**
         * @param array $termData
         * @return int
         */
        private function insertTerm(array $termData) : int
        {
        }
    }
    class MetaboxField
    {
        const ACTION_AFTER_TRANSLATION_UI_FIELD = 'multilingualpress.after_translation_ui_field';
        const ACTION_BEFORE_TRANSLATION_UI_FIELD = 'multilingualpress.before_translation_ui_field';
        const FILTER_TRANSLATION_UI_SHOW_FIELD = 'multilingualpress.translation_ui_show_field';
        /**
         * @var string
         */
        private $key;
        /**
         * @var callable
         */
        private $renderCallback;
        /**
         * @var callable
         */
        private $sanitizer;
        /**
         * @param string $key
         * @param callable $renderCallback
         * @param callable|null $sanitizer
         */
        public function __construct(string $key, callable $renderCallback, callable $sanitizer = null)
        {
        }
        /**
         * @return string
         */
        public function key() : string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext)
        {
        }
        /**
         * @param Request $request
         * @param MetaboxFieldsHelper $helper
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext) : bool
        {
        }
    }
    class MetaboxFields
    {
        const TAB_RELATION = 'tab-relation';
        const TAB_DATA = 'tab-data';
        const FIELD_RELATION = 'relationship';
        const FIELD_RELATION_NEW = 'new';
        const FIELD_RELATION_EXISTING = 'existing';
        const FIELD_RELATION_REMOVE = 'remove';
        const FIELD_RELATION_LEAVE = 'leave';
        const FIELD_RELATION_NOTHING = 'nothing';
        const FIELD_RELATION_SEARCH = 'search_term_id';
        const FIELD_NAME = 'remote-name';
        const FIELD_SLUG = 'remote-slug';
        const FIELD_DESCRIPTION = 'remote-description';
        const FIELD_PARENT = 'remote-parent';
        /**
         * @return array
         */
        public function allFieldsTabs() : array
        {
        }
        /**
         * @return array
         */
        private function relationFields() : array
        {
        }
        /**
         * @return array
         */
        private function dataFields() : array
        {
        }
    }
    class MetaboxTab
    {
        const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
        const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
        const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.translation_ui_show_tab';
        /**
         * @var string
         */
        private $id;
        /**
         * @var MetaboxField[]
         */
        private $fields;
        /**
         * @var string
         */
        private $label;
        /**
         * @param string $id
         * @param string $label
         * @param MetaboxField[] ...$fields
         */
        public function __construct(string $id, string $label, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxField ...$fields)
        {
        }
        /**
         * @return string
         */
        public function id() : string
        {
        }
        /**
         * @return string
         */
        public function label() : string
        {
        }
        /**
         * @return MetaboxField[]
         */
        public function fields() : array
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext) : bool
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext)
        {
        }
    }
    final class MetaboxView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @var MetaboxFields
         */
        private $fields;
        /**
         * @var MetaboxFieldsHelper
         */
        private $helper;
        /**
         * @var RelationshipContext
         */
        private $relationshipContext;
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info)
        {
        }
        /**
         * @return void
         */
        private function boxDataAttributes()
        {
        }
        /**
         * @param MetaboxTab $tab
         */
        private function renderTabAnchor(\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxTab $tab)
        {
        }
        /**
         * Retrieve the edit link for the remote term
         *
         * @return string
         */
        private function remoteTermUrl() : string
        {
        }
    }
    /**
     * Relationship context data object.
     */
    class RelationshipContext
    {
        const REMOTE_TERM_ID = 'remote_term_id';
        const REMOTE_SITE_ID = 'remote_site_id';
        const SOURCE_TERM_ID = 'source_term_id';
        const SOURCE_SITE_ID = 'source_site_id';
        const DEFAULTS = [self::REMOTE_TERM_ID => 0, self::REMOTE_SITE_ID => 0, self::SOURCE_TERM_ID => 0, self::SOURCE_SITE_ID => 0];
        /**
         * @var \WP_Term[]
         */
        private $terms = [];
        /**
         * @var array
         */
        private $data;
        /**
         * Returns a new context object, instantiated according to the data in the given context object
         * and the array.
         *
         * @param RelationshipContext $context
         * @param array $data
         * @return RelationshipContext
         */
        public static function fromExistingAndData(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context, array $data) : \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext
        {
        }
        /**
         * @param array $data
         */
        public function __construct(array $data = [])
        {
        }
        /**
         * @return int
         */
        public function remoteTermId() : int
        {
        }
        /**
         * @return int
         */
        public function remoteSiteId() : int
        {
        }
        /**
         * @return int
         */
        public function sourceTermId() : int
        {
        }
        /**
         * @return int
         */
        public function sourceSiteId() : int
        {
        }
        /**
         * @return bool
         */
        public function hasRemoteTerm() : bool
        {
        }
        /**
         * @return \WP_Term|null
         */
        public function remoteTerm()
        {
        }
        /**
         * @return \WP_Term
         */
        public function sourceTerm() : \WP_Term
        {
        }
        /**
         * Print HTML fields for the relationship context.
         * @param MetaboxFieldsHelper $helper
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @param int $siteId
         * @param string $type
         * @return \WP_Term|null
         */
        private function term(int $siteId, string $type)
        {
        }
    }
    /**
     * Permission checker to be used to either permit or prevent access to terms.
     */
    class RelationshipPermission
    {
        const FILTER_IS_RELATED_TERM_EDITABLE = 'multilingualpress.is_related_term_editable';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @var int[][]
         */
        private $relatedTerms = [];
        /**
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Checks if the current user can edit (or create) a term in the site with the given ID that is
         * related to given term in the current site.
         *
         * @param \WP_Term $sourceTerm
         * @param int $remoteSiteId
         * @return bool
         */
        public function isRelatedTermEditable(\WP_Term $sourceTerm, int $remoteSiteId) : bool
        {
        }
        /**
         * Returns an array with the IDs of all related terms for the term with the given ID as an
         * array with site IDs as keys and term IDs as values.
         *
         * @param int $termTaxonomyId
         * @param int $remoteSiteId
         * @return int
         */
        private function relatedTermTaxonomyId(int $termTaxonomyId, int $remoteSiteId) : int
        {
        }
    }
    class TableList
    {
        const RELATION_TYPE = 'term';
        const EDIT_TRANSLATIONS_COLUMN_NAME = 'translations';
        const FILTER_SITE_LANGUAGE_TAG = 'multilingualpress.site_language_tag';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * TableList constructor.
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param array $postsColumns
         * @return array
         */
        public function editTranslationColumns(array $postsColumns) : array
        {
        }
        /**
         * @param string $content
         * @param string $columnName
         * @param int $termId
         * @return void
         */
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public function editTranslationLinks(string $content, string $columnName, int $termId)
        {
        }
    }
    class TermRelationSaveHelper
    {
        const FILTER_METADATA = 'multilingualpress.term_meta_data';
        const FILTER_SYNC_META_KEYS = 'multilingualpress.sync_term_meta_keys';
        const ACTION_BEFORE_SAVE_RELATIONS = 'multilingualpress.before_save_terms_relations';
        const ACTION_AFTER_SAVED_RELATIONS = 'multilingualpress.after_saved_terms_relations';
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Finds the related term parent ID.
         *
         * @param RelationshipContext $context
         * @param int $sourceParentId The source parent ID.
         * @return int The related parent ID.
         * @throws NonexistentTable
         */
        public function relatedTermParent(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context, int $sourceParentId) : int
        {
        }
        /**
         * @param RelationshipContext $context
         * @return bool
         */
        public function relateTerms(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context) : bool
        {
        }
        /**
         * @param RelationshipContext $context
         * @param Request $request
         */
        public function syncMetadata(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @param int $targetSiteId
         * @return int
         */
        private function maybeSwitchSite(int $targetSiteId) : int
        {
        }
        /**
         * @param int $originalSiteId
         * @return bool
         */
        private function maybeRestoreSite(int $originalSiteId) : bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Translator {
    trait UrlBlogFragmentTrailingTrait
    {
        /**
         * @param string $string
         * @return string
         */
        private function untrailingBlogIt(string $string) : string
        {
        }
        /**
         * @param string $string
         * @return string
         */
        private function trailingBlogIt(string $string) : string
        {
        }
    }
    /**
     * Class DateTranslator
     * @package Inpsyde\MultilingualPress\Translator
     */
    final class DateTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        use \Inpsyde\MultilingualPress\Translator\UrlBlogFragmentTrailingTrait;
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @var \WP
         */
        private $wp = null;
        /**
         * @var \WP_Rewrite
         */
        private $wpRewrite = null;
        /**
         * DateTranslator constructor.
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return Translation
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @param \WP|null $wp
         * @return bool
         */
        public function ensureWp(\WP $wp = null) : bool
        {
        }
        /**
         * @param \WP_Rewrite|null $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null) : bool
        {
        }
        /**
         * @param string $struct
         * @return bool
         */
        private function ensurePermalinkStructure(string $struct) : bool
        {
        }
        /**
         * @param bool $hasBlogPrefix
         * @param int $siteId
         * @return string
         */
        private function ensureRequestFragment(bool $hasBlogPrefix, int $siteId) : string
        {
        }
    }
    /**
     * Translator implementation for front-page requests.
     */
    final class HomeTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        const FILTER_TRANSLATION = 'multilingualpress.filter_home_translation';
        const SHOW_ON_FRONT_POSTS = 'posts';
        const SHOW_ON_FRONT_PAGE = 'page';
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @var ContentRelations
         */
        private $contentRelations;
        /**
         * HomeTranslator constructor.
         * @param UrlFactory $urlFactory
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * Retrieve the translation url
         *
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return string
         */
        private function url(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : string
        {
        }
        /**
         * Retrieve the url used by the show on front option
         *
         * @param int $originalSiteId
         * @param int $siteId
         * @param string $homeUrl
         * @param TranslationSearchArgs $args
         * @return string
         */
        private function showOnFrontUrl(int $originalSiteId, int $siteId, string $homeUrl, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : string
        {
        }
        /**
         * @param int $remoteSiteId
         * @return int
         */
        private function maybeSwitchSite(int $remoteSiteId) : int
        {
        }
        /**
         * @param int $originalSiteId
         * @return bool
         */
        private function maybeRestoreSite(int $originalSiteId) : bool
        {
        }
    }
    /**
     * Translator implementation for posts.
     */
    final class PostTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        const ACTION_GENERATE_PERMALINK = 'multilingualpress.generate_permalink';
        const ACTION_GENERATED_PERMALINK = 'multilingualpress.generated_permalink';
        const FILTER_TRANSLATION = 'multilingualpress.filter_post_translation';
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @var \WP_Rewrite
         */
        private $wpRewrite;
        /**
         * @var PostTypeRepository
         */
        private $postTypeRepository;
        /**
         * @var PostTypeSlugsSettingsRepository
         */
        private $slugsRepository;
        /**
         * @var array
         */
        private $customBase = [];
        /**
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository, \Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $slugsRepository, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @param \WP_Rewrite|null $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null) : bool
        {
        }
        /**
         * @param string $key
         * @param callable $function
         */
        public function registerBaseStructureCallback(string $key, callable $function)
        {
        }
        /**
         * Returns the translation data for the given post ID.
         *
         * @param int $postId
         * @param string[] $postStatuses
         * @param bool $strict
         * @return array
         */
        private function translationData(int $postId, array $postStatuses, bool $strict) : array
        {
        }
        /**
         * @param int $postId
         * @param bool $currentUserCanEdit
         * @return array
         */
        private function translationAdminData(int $postId, bool $currentUserCanEdit) : array
        {
        }
        /**
         * @param int $postId
         * @return string
         */
        private function publicUrl(int $postId) : string
        {
        }
        /**
         * @param int $postId
         */
        private function fixPostBase(int $postId)
        {
        }
        /**
         * @param string $postType
         * @return string
         */
        private function expectedBase(string $postType) : string
        {
        }
        /**
         * @param string $postType
         * @param string $struct
         */
        private function updateExtraRewritePermastruct(string $postType, string $struct)
        {
        }
        /**
         * @param string $struct
         */
        private function ensurePermastruct(string $struct)
        {
        }
        /**
         * @param string $translated
         * @param string $postType
         * @return string
         */
        private function composeBase(string $translated, string $postType) : string
        {
        }
    }
    /**
     * Translator implementation for post types.
     */
    final class PostTypeTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        const FILTER_POST_TYPE_PERMALINK = 'multilingualpress.post_type_permalink';
        const FILTER_TRANSLATION = 'multilingualpress.filter_post_type_translation';
        /**
         * @var ActivePostTypes
         */
        private $activePostTypes;
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @var PostTypeSlugsSettingsRepository
         */
        private $slugsRepository;
        /**
         * @param UrlFactory $urlFactory
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $slugsRepository, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
    /**
     * Translator implementation for search requests.
     */
    final class SearchTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        const FILTER_TRANSLATION = 'multilingualpress.filter_search_translation';
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
    /**
     * Service provider for all translation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function registerContentRelatedTranslations(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         */
        private function registerNotContentRelatedTranslations(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @param Container $container
         * @param Translations $translations
         */
        private function bootstrapPostTranslator(\Inpsyde\MultilingualPress\Framework\Service\Container $container, \Inpsyde\MultilingualPress\Framework\Api\Translations $translations)
        {
        }
        /**
         * @param Container $container
         * @param Translations $translations
         *
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        private function bootstrapTermTranslator(\Inpsyde\MultilingualPress\Framework\Service\Container $container, \Inpsyde\MultilingualPress\Framework\Api\Translations $translations)
        {
        }
        /**
         * @param Container $container
         * @param Translations $translations
         */
        private function bootstrapDateTranslation(\Inpsyde\MultilingualPress\Framework\Service\Container $container, \Inpsyde\MultilingualPress\Framework\Api\Translations $translations)
        {
        }
    }
    /**
     * Translator implementation for terms.
     */
    class TermTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        use \Inpsyde\MultilingualPress\Translator\UrlBlogFragmentTrailingTrait;
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        const FILTER_TAXONOMY_LIST = 'multilingualpress.term_translator_taxonomy_list';
        const FILTER_TRANSLATION = 'multilingualpress.filter_term_translation';
        const FILTER_TERM_PUBLIC_URL = 'multilingualpress.filter_term_public_url';
        /**
         * @var UrlFactory
         */
        private $urlFactory;
        /**
         * @var \WP_Rewrite
         */
        private $wpRewrite;
        /**
         * @var TaxonomyRepository
         */
        private $taxonomyRepository;
        /**
         * @var array
         */
        private $customBase = [];
        /**
         * @var \WP
         */
        private $wp;
        /**
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args) : \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @param \WP_Rewrite|null $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null) : bool
        {
        }
        /**
         * @param string $key
         * @param callable $function
         */
        public function registerBaseStructureCallback(string $key, callable $function)
        {
        }
        /**
         * @param \WP|null $wp
         * @return bool
         */
        public function ensureWp(\WP $wp = null) : bool
        {
        }
        /**
         * Returns the translation data for the given term taxonomy ID.
         *
         * @param int $termTaxonomyId
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @return array
         */
        protected function translationData(int $termTaxonomyId, int $sourceSiteId, int $remoteSiteId) : array
        {
        }
        /**
         * Returns term data according to the given term taxonomy ID.
         *
         * @param int $termTaxonomyId
         * @return array
         */
        protected function termByTermTaxonomyId(int $termTaxonomyId) : array
        {
        }
        /**
         * Returns permalink for the given taxonomy term.
         *
         * @param int $termId
         * @param string $taxonomySlug
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @return string
         */
        protected function publicUrl(int $termId, string $taxonomySlug, int $sourceSiteId, int $remoteSiteId) : string
        {
        }
        /**
         * Updates the global WordPress rewrite instance if it is wrong.
         *
         * @param string $taxonomySlug
         * @return void
         */
        protected function fixTermBase(string $taxonomySlug)
        {
        }
        /**
         * Finds a custom taxonomy base.
         *
         * @param string $taxonomySlug
         * @return string
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        protected function expectedBase(string $taxonomySlug) : string
        {
        }
        /**
         * @param string $fragment
         * @param bool $hasBlogPrefix
         * @return string
         */
        protected function ensureRequestFragment(string $fragment, bool $hasBlogPrefix) : string
        {
        }
        /**
         * @param string $translated
         * @param string $taxonomySlug
         * @return string
         */
        protected function composeBase(string $translated, string $taxonomySlug) : string
        {
        }
        /**
         * Updates the global WordPress rewrite instance for the given custom taxonomy.
         *
         * @param string $taxonomy
         * @param string $struct
         */
        protected function updateRewritePermastruct(string $taxonomy, string $struct)
        {
        }
        /**
         * @param string $struct
         */
        protected function ensurePermastruct(string $struct)
        {
        }
        /**
         * @param string $taxonomySlug
         * @return string
         */
        protected function taxonomy(string $taxonomySlug) : string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * @inheritDoc
         * @throws NameNotFound
         * @throws LateAccessToNotSharedService
         * @throws Exception
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container)
        {
        }
        /**
         * Register Cli commands for MLP
         *
         * @param iterable<WpCliCommand> $wpCliCommands
         * @param WpCliCommandsHelper $wpCliCommandsHelper
         * @throws Exception
         */
        protected function registerCliCommands(iterable $wpCliCommands, \Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper)
        {
        }
    }
    /**
     * WP-CLI commands helper.
     */
    class WpCliCommandsHelper
    {
        /**
         * Registers a CLI command handler.
         *
         * @param string $command The command.
         * Sub-commands should be separated by a space, i.e. `command sub-command`.
         * @param callable $handler The command handler.
         * @throws Exception
         */
        public function addCliCommand(string $command, callable $handler, array $documentation = []) : void
        {
        }
        /**
         * Display an error message
         *
         * @param string $message An error message.
         * @throws WP_CLI\ExitException
         */
        public function showCliError(string $message) : void
        {
        }
        /**
         * Display a success message
         *
         * @param string $message An error message.
         */
        public function showCliSuccess(string $message) : void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * @param $function
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    function deactivateNotice($function)
    {
    }
    /**
     * Loads definitions and/or autoloader.
     *
     * @param string $rootDir
     * @throws Exception
     */
    function autoload(string $rootDir)
    {
    }
    /**
     * Bootstraps MultilingualPress.
     *
     * @return bool
     *
     * @wp-hook plugins_loaded
     * @throws \Throwable
     * @return bool
     */
    function bootstrap()
    {
    }
    /**
     * Triggers a plugin-specific activation action third parties can listen to.
     *
     * @wp-hook activate_{$plugin}
     */
    function activate()
    {
    }
    /**
     * Load missed WordPress functions.
     */
    function loadWordPressFunctions()
    {
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * Returns the content IDs of all translations for the given content element data.
     *
     * @param int $contentId
     * @param string $type
     * @param int $siteId
     * @return int[]
     * @throws NonexistentTable
     */
    function translationIds(int $contentId = 0, string $type = 'post', int $siteId = 0) : array
    {
    }
    /**
     * Returns the MultilingualPress language for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteLocale(int $siteId = 0) : string
    {
    }
    /**
     * Returns the MultilingualPress language for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     */
    function siteLanguageTag(int $siteId = 0) : string
    {
    }
    /**
     * Returns the MultilingualPress locale name for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteLocaleName(int $siteId = 0) : string
    {
    }
    /**
     * Returns the MultilingualPress language name for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteLanguageName(int $siteId = 0) : string
    {
    }
    /**
     * Returns the MultilingualPress site name (which includes site name and site language) for the site
     * with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteNameWithLanguage(int $siteId = 0) : string
    {
    }
    /**
     * Return all available languages, including default and DB.
     * Array keys are BCP-47 tags.
     *
     * @return Language[]
     * @throws NonexistentTable
     */
    function allLanguages() : array
    {
    }
    /**
     * Returns the names of all available languages according to the given arguments.
     *
     * @param bool $onlyRelatedToCurrentSite
     * @param bool $includeCurrentSite
     * @return string[]
     * @throws NonexistentTable
     */
    function assignedLanguageNames(bool $onlyRelatedToCurrentSite = true, bool $includeCurrentSite = true) : array
    {
    }
    /**
     * Retrieves a map of site IDs to languages, which the given user is assigned.
     *
     * @param int $userId The user ID.
     *
     * @return array<int, string> A map of site IDs to languages.
     * @throws NonexistentTable
     */
    function assignedLanguagesForUser(int $userId) : array
    {
    }
    /**
     * Returns the individual MultilingualPress language code of all (related) sites.
     *
     * @param bool $relatedSitesOnly
     * @param bool $includeCurrentSite
     * @return string[]
     * @throws NonexistentTable
     */
    function assignedLanguageTags(bool $relatedSitesOnly = true, bool $includeCurrentSite = true) : array
    {
    }
    /**
     * Returns the individual MultilingualPress language object of all (related) sites.
     *
     * @param bool $relatedSitesOnly
     * @return Language[]
     * @throws NonexistentTable
     */
    function assignedLanguages(bool $relatedSitesOnly = true) : array
    {
    }
    /**
     * Returns the MultilingualPress language for the current site.
     *
     * @return string
     * @throws NonexistentTable
     */
    function currentSiteLocale() : string
    {
    }
    /**
     * Returns the language with the given BCP-47 tag.
     *
     * @param string $bcp47tag
     * @return Language
     * @throws NonexistentTable
     */
    function languageByTag(string $bcp47tag) : \Inpsyde\MultilingualPress\Framework\Language\Language
    {
    }
    /**
     * @return Language[]
     */
    function allDefaultLanguages() : array
    {
    }
    /**
     * @param string $bcp47tag
     * @return Language
     */
    function defaultLanguageByTag(string $bcp47tag) : \Inpsyde\MultilingualPress\Framework\Language\Language
    {
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * Resolves the value with the given name from the container.
     *
     * @param string|null $name
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    function resolve(string $name = null)
    {
    }
    /**
     * Checks if MultilingualPress debug mode is on.
     *
     * @return bool
     */
    function isDebugMode() : bool
    {
    }
    /**
     * Checks if either MultilingualPress or WordPress script debug mode is on.
     *
     * @return bool
     */
    function isScriptDebugMode() : bool
    {
    }
    /**
     * Checks if either MultilingualPress or WordPress debug mode is on.
     *
     * @return bool
     */
    function isWpDebugMode() : bool
    {
    }
    /**
     * Check if the plugin need license or not
     *
     * @return bool
     */
    function isLicensed() : bool
    {
    }
    /**
     * Returns the given content ID, if valid, and the ID of the queried object otherwise.
     *
     * @param int $contentId
     * @return int
     */
    function defaultContentId(int $contentId) : int
    {
    }
    /**
     * Print the setting page header
     *
     * @param \WP_Site $site
     * @param string $id
     */
    function settingsPageHead(\WP_Site $site, string $id)
    {
    }
    /**
     * Add error messages to the settings_errors transient.
     *
     * @param array $errors
     * @param string $setting
     * @param string $type
     */
    function settingsErrors(array $errors, string $setting, string $type)
    {
    }
    /**
     * Redirects to the given URL (or the referer) after a settings update request.
     *
     * @param string $url
     * @param string $setting
     * @param string $code
     */
    function redirectAfterSettingsUpdate(string $url = '', string $setting = 'mlp-setting', string $code = 'mlp-setting')
    {
    }
    /**
     * Checks if the site with the given ID exists (within the current or given network)
     * and is not marked as deleted.
     *
     * @param int $siteId
     * @param int $networkId
     * @return bool
     */
    function siteExists(int $siteId, int $networkId = 0) : bool
    {
    }
    /**
     * Checks if a given table exists within the database.
     *
     * @param string $tableName
     * @return bool
     */
    function tableExists(string $tableName) : bool
    {
    }
    /**
     * Wrapper for the exit language construct.
     *
     * Introduced to allow for easy unit testing.
     *
     * @param int|string $message
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    function callExit($message = '')
    {
    }
    /**
     * Renders the HTML string for the hidden nonce field according to the given nonce object.
     *
     * @param Nonce $nonce
     * @param bool $withReferer
     */
    function printNonceField(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, bool $withReferer = true)
    {
    }
    /**
     * Combine Attributes
     *
     * @param array $pairs
     * @param array $atts
     * @return array
     */
    function combineAtts(array $pairs, array $atts) : array
    {
    }
    /**
     * Array to attributes
     *
     * @param array $attributes
     * @param bool $xml
     * @return string
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    function arrayToAttrs(array $attributes, $xml = false) : string
    {
    }
    /**
     * Proxy for WordPress defined callbacks
     *
     * This function is used when we have to call one of our methods but the callback is hooked into
     * a WordPress filter or action.
     *
     * Since isn't possible to ensure third party plugins will pass the correct data declared
     * by WordPress we need a way to prevent fatal errors without introduce complexity.
     *
     * In this case, this function will allow us to maintain our type hints and in case something wrong
     * happen we rise a E_USER_NOTICE error so the issue get logged and also firing an action we allow
     * use or third party developer to be able to perform a more accurate debug.
     *
     * @param callable $callback
     * @return callable
     * @throws Throwable In case WP_DEBUG is active
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    function wpHookProxy(callable $callback) : callable
    {
    }
    /**
     * Convert String To Boolean
     *
     * This function is the same of wc_string_to_bool.
     *
     * @param string $value The string to convert to boolean. 'yes', 'true', '1' are converted to true.
     *
     * @return bool True or false depending on the passed value.
     */
    function stringToBool(string $value) : bool
    {
    }
    /**
     * Convert Boolean to String
     *
     * This function is the same of wc_bool_to_string
     *
     * @param bool $bool The bool value to convert.
     *
     * @return string The converted value. 'yes' or 'no'.
     */
    function boolToString(bool $bool) : string
    {
    }
    /**
     * @return string
     */
    function wpVersion() : string
    {
    }
    /**
     * Sanitize Html Class
     *
     * @param string|array|\Traversable $class
     * @return string
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    function sanitizeHtmlClass($class) : string
    {
    }
    /**
     * Will add the request params to given url.
     *
     * @param string $url The URL.
     * @return string The URL.
     */
    function preserveUrlRequestParams(string $url) : string
    {
    }
}
