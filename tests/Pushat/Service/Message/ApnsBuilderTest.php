<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Service\Message;

use Jgut\Pushat\Service\Message\ApnsBuilder;
use ZendService\Apple\Apns\Message;

/**
 * @covers \Jgut\Pushat\Service\Message\ApnsBuilder
 */
class ApnsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Pushat\Service\Message\ApnsBuilder::build
     */
    public function testPushClient()
    {
        $device = new \Jgut\Pushat\Device\Apns('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527');

        $service = new \Jgut\Pushat\Service\Apns(
            ['certificate' => dirname(dirname(dirname(__DIR__))) . '/files/apns_certificate.pem']
        );

        $message = new \Jgut\Pushat\Message\Apns(['title' => 'title']);

        $notification = new \Jgut\Pushat\Notification\Apns(
            $service,
            $message,
            [],
            ['expire' => 600, 'badge' => 1]
        );

        $client = ApnsBuilder::build($device, $notification);
        $this->assertInstanceOf(Message::class, $client);
    }
}
