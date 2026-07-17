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
    'settings.short_key' => [
        'default' => 'short',
        'label' => 'A short-keyed setting',
        'type' => 'text',
    ],
];
