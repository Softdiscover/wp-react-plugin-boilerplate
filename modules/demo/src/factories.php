<?php

declare(strict_types=1);

use Dhii\Services\Factory;
use Me\Plugin\Demo\DemoMenu;
use Me\Plugin\Demo\SettingsManager;
use Me\Plugin\Demo\SettingsRepository;

return function (string $modDir): array {
    return [
        'me/admin/settings_repository' => new Factory([], function (): SettingsRepository {
            return new SettingsRepository('custom_plugin_settings');
        }),
        'me/admin/settings_manager' => new Factory([
            'me/admin/settings_repository',
        ], function (SettingsRepository $repository): SettingsManager {
            return new SettingsManager($repository);
        }),

        'me/admin/menu' => new Factory([
            'me/admin/settings_manager',
        ], function (SettingsManager $settingsManager): DemoMenu {
            return new DemoMenu($settingsManager);
        }),
        'me/plugin/demo/notice_text' => new Factory([
            'me/plugin/demo/plugin_title',
        ], function (string $pluginTitle): string {
            // translators: 1: Plugin name.
            return sprintf(__('Modular plugin "%1$s" is active!', 'me-plugin'), $pluginTitle);
        }),

        'me/plugin/demo/plugin_title' => new Factory([
        ], function () {
            return 'OOP Plugin Demo';
        }),
    ];
};
