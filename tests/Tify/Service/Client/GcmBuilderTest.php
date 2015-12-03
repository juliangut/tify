<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Client;

use Jgut\Tify\Service\Client\GcmBuilder;
use ZendService\Google\Gcm\Client;

/**
 * @covers \Jgut\Tify\Service\Client\GcmBuilder
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Tify\Service\Client\GcmBuilder::buildPush
     */
    public function testPushClient()
    {
        $client = GcmBuilder::buildPush('my_api_key');

        $this->assertInstanceOf(Client::class, $client);
    }
}
