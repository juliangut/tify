<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Service\Message;

use Jgut\Pushat\Service\Message\GcmBuilder;
use Jgut\Pushat\Service\Message\Gcm;

/**
 * @covers \Jgut\Pushat\Service\Message\GcmBuilder
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Pushat\Service\Message\GcmBuilder::build
     */
    public function testPushClient()
    {
        $service = new \Jgut\Pushat\Service\Gcm(
            'prod',
            ['api_key' => 'my_api_key']
        );

        $message = new \Jgut\Pushat\Message\Gcm(['title' => 'title', 'body' => 'body']);

        $notification = new \Jgut\Pushat\Notification\Gcm($service, $message);

        $client = GcmBuilder::build(['my_token'], $notification);
        $this->assertInstanceOf(Gcm::class, $client);
    }
}
