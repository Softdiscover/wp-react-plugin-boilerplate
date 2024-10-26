<?php

declare(strict_types=1);

namespace Me\Plugin\Test;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use function Brain\Monkey\Functions\when;
use WP_Mock;
/**
 * Base class for project tests.
 */
class AbstractTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        WP_Mock::setUp();
        // __()
// Plugin url.
        if ( ! defined( 'MY_PLUGIN_URL' ) ) {
            define( 'MY_PLUGIN_URL', plugin_dir_url( dirname(__FILE__, 5) ) );
        }
// Plugin directory.
        if ( ! defined( 'MY_PLUGIN_DIR' ) ) {
            define( 'MY_PLUGIN_DIR', plugin_dir_path( dirname(__FILE__, 5) ) );
        }

        // Plugin version
        if ( ! defined( 'MY_PLUGIN_VERSION' ) ) {
            define( 'MY_PLUGIN_VERSION', '1.0.0');
        }

        /*WP_Mock::userFunction('get_current_user_id', [
            'return' => 1, // You can set any user ID for the test
        ]);*/

        /*when('__')
            ->returnArg();*/
    }

    public function tearDown(): void
    {
        Monkey\tearDown();
        WP_Mock::tearDown();
        parent::tearDown();
    }
}
