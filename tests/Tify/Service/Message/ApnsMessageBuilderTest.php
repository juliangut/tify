<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Message;

use Jgut\Tify\Service\Message\ApnsMessageBuilder;
use ZendService\Apple\Apns\Message;

/**
 * @covers \Jgut\Tify\Service\Message\ApnsMessageBuilder
 */
class ApnsMessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Tify\Service\Message\ApnsMessageBuilder::build
     */
    public function testPushClient()
    {
        $recipient = new \Jgut\Tify\Recipient\ApnsRecipient(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527'
        );

        $service = new \Jgut\Tify\Service\ApnsService(
            ['certificate' => dirname(dirname(dirname(__DIR__))) . '/files/apns_certificate.pem']
        );

        $message = new \Jgut\Tify\Message\ApnsMessage(['title' => 'title']);

        $notification = new \Jgut\Tify\Notification\ApnsNotification(
            $service,
            $message,
            [],
            ['expire' => 600, 'badge' => 1]
        );

        $client = ApnsMessageBuilder::build($recipient, $notification);
        $this->assertInstanceOf(Message::class, $client);
    }
}
