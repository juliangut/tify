<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Service\Client;

use Jgut\Pushat\Service\Client\GcmBuilder;
use ZendService\Google\Gcm\Client;

/**
 * @covers \Jgut\Pushat\Service\Client\GcmBuilder
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Pushat\Service\Client\GcmBuilder::buildPush
     */
    public function testPushClient()
    {
        $client = GcmBuilder::buildPush('my_api_key');

        $this->assertInstanceOf(Client::class, $client);
    }
}
