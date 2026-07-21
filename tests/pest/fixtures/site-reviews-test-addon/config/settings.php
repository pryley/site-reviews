<?php

return [
    'settings.addons.test-addon.enabled' => [
        'default' => 'no',
        'label' => 'Enable the test addon',
        'type' => 'yes_no',
    ],
    // A SHORT config key: Addons\Controller::filterSettings() namespaces it to
    // settings.addons.test-addon.short_key when merging into the settings form.
    // The fully-prefixed key above passes through untouched — both forms are
    // exercised by AddonSettingsStorageTest.
    // The 8.1.x addon shape: fully-prefixed key AND fully-prefixed depends_on.
    // Every released addon is written this way, and must keep working.
    'settings.short_key' => [
        'default' => 'short',
        'depends_on' => ['settings.addons.test-addon.enabled' => ['yes']],
        'label' => 'A short-keyed setting',
        'type' => 'text',
    ],
    // A BARE config key with a BARE depends_on key: both mount at the addon's
    // composed-view path, so a config can be written without repeating the
    // addon's mount and stay correct in the hosted and standalone shapes.
    'bare_key' => [
        'default' => 'bare',
        'depends_on' => ['short_key' => ['short']],
        'label' => 'A bare-keyed setting',
        'type' => 'text',
    ],
];
