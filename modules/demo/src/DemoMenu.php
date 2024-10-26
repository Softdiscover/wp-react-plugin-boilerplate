<?php

declare(strict_types=1);

namespace Me\Plugin\Demo;

class DemoMenu
{
    /**
     * Assets version.
     *
     * @var string
     */
    private string $assets_version = '';
    /**
     * A unique string id to be used in markup and jsx.
     *
     * @var string
     */
    private string $unique_id = '';

    /**
     * Page Assets.
     *
     * @var array<string, array{
     *     src: mixed,
     *     style_src: mixed,
     *     deps: mixed,
     *     ver: mixed,
     *     strategy: bool,
     *     localize: array{
     *         dom_element_id: string
     *     }
     * }>
     */
    private array $page_scripts = [];

    protected SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
        $this->unique_id = "my_plugin_settings";
    }

    public function registerMenuPage(): void
    {
        $page = add_menu_page(
            __('Demo Plugin', 'me-plugin'),
            __('DemoPlugin', 'me-plugin'),
            'manage_options',
            'my_demo_panel',
            [$this, 'displaySettingsPage'],
            'dashicons-admin-generic',
            20
        );


        add_action('load-' . $page, array( $this, 'prepareAssets'));
    }

    public function prepareAssets(): void
    {
        $handle       = 'my_demo_plugin_src';
        $src          = MY_PLUGIN_URL . 'assets/js/demopage.min.js';
        $style_src    = MY_PLUGIN_URL . 'assets/css/demopage.min.css';
        $dependencies = ! empty($this->scriptData('dependencies'))
            ? $this->scriptData('dependencies')
            : array(
                'react',
                'wp-element',
                'wp-i18n',
                'wp-is-shallow-equal',
                'wp-polyfill',
            );

        $this->page_scripts[ $handle ] = array(
            'src'       => $src,
            'style_src' => $style_src,
            'deps'      => $dependencies,
            'ver'       => $this->assets_version,
            'strategy'  => true,
            'localize'  => array(
                'dom_element_id'   => $this->unique_id
            ),
        );
    }


    /**
     * Prepares assets.
     *
     * @psalm-suppress MixedArgument
     */
    public function enqueueAssets(): void
    {
        if (! empty($this->page_scripts)) {
            foreach ($this->page_scripts as $handle => $page_script) {
                wp_register_script(
                    $handle,
                    $page_script['src'],
                    $page_script['deps'],
                    $page_script['ver'],
                    $page_script['strategy']
                );

                if (! empty($page_script['localize'])) {
                    wp_localize_script($handle, 'myPluginTest', $page_script['localize']);
                }

                wp_enqueue_script($handle);

                if (! empty($page_script['style_src'])) {
                    wp_enqueue_style($handle, $page_script['style_src'], array(), $this->assets_version);
                }
            }
        }
    }

    public function displaySettingsPage(): void
    {
        echo '<div id="' . esc_attr($this->unique_id) . '" ></div>';
    }

    /**
     * Gets assets data for given key.
     *
     * @param string $key key
     *
     * @return string|string[] Either a single string or an array of strings
     */
    protected function scriptData(string $key = ''): string|array
    {
        $raw_script_data = $this->rawScriptData();

        return ! empty($key) && ! empty($raw_script_data[ $key ]) ? $raw_script_data[ $key ] : '';
    }

    /**
     * Gets the script data from assets php file.
     *
     * @return string[] array of strings
     * @psalm-suppress MissingFile
     * @psalm-suppress UnresolvableInclude
     */
    protected function rawScriptData(): array
    {
        static $script_data = null;

        if (is_null($script_data) && file_exists(MY_PLUGIN_DIR . 'assets/js/demopage.min.asset.php')) {
            $script_data = include MY_PLUGIN_DIR . 'assets/js/demopage.min.asset.php';
        }

        return (array) $script_data;
    }
}
