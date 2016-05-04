<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Client;

use Jgut\Tify\Service\Client\GcmClientBuilder;
use ZendService\Google\Gcm\Client;

/**
 * Gcm service builder
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testPushClient()
    {
        $client = GcmClientBuilder::buildPush('my_api_key');

        self::assertInstanceOf(Client::class, $client);
    }
}
