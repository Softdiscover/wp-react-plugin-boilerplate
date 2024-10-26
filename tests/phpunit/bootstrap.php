<?php

declare(strict_types=1);

(function (string $baseFile) {
    $baseDir = dirname($baseFile);
    $rootDir = dirname($baseDir, 2);
    define('BASE_DIR', $rootDir);
    define('BASE_PATH', "$rootDir/plugin.php");
   // define('ABSPATH', '/var/www/html/');
   // define('WPINC', 'wp-includes'); // Define WPINC

    // Define WP_PLUGIN_DIR constant.
   /* if (! defined('WP_PLUGIN_DIR')) {
        //	"/var/www/shop/wp/wp-content/plugins"
        define('WP_PLUGIN_DIR', './wp/wp-content/plugins'); // Full path, no trailing slash.
    }*/

    // Define constants to prevent loading unnecessary parts of WordPress
   /* define('WP_USE_THEMES', false);          // Prevents loading the theme
    define('WP_INSTALLING', true);           // Sets a safe installation mode
    define('WP_DEBUG_DISPLAY', false);       // Disables error display in the browser
    define('SHORTINIT', true);               // Loads only essential parts of WordPress*/

    // error_reporting(E_ALL | E_STRICT);


    require_once "$rootDir/vendor/autoload.php";
    require_once "$rootDir/vendor/antecedent/patchwork/Patchwork.php";

    // Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$polyfillsPath = getenv('WP_TESTS_PHPUNIT_POLYFILLS_PATH');
if (false !== $polyfillsPath) {
	define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', $polyfillsPath);
}
    
    $testsDirectoryLibrary = './tmp/wordpress-tests-lib';
    if (! file_exists("$testsDirectoryLibrary/includes/functions.php")) {
        echo "Could not find $testsDirectoryLibrary/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
        exit(1);
    }


    // Give access to tests_add_filter() function.
    require_once "$testsDirectoryLibrary/includes/functions.php";


    /**
     * Manually load the plugin being tested.
     */
    function _manually_load_plugin() {

        $pluginsPath = dirname(__FILE__, 5) . '/plugins';
        #require "$pluginsPath/woocommerce/woocommerce.php";
        require "$pluginsPath/plugin/plugin.php";
    }

    //tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require "$testsDirectoryLibrary/includes/bootstrap.php";


    error_reporting(E_ALL & ~E_DEPRECATED);
    

})(__FILE__);
