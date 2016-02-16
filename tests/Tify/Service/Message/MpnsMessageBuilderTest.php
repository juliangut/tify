<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Message;

use Jgut\Tify\Service\Message\MpnsMessageBuilder;
use Jgut\Tify\Service\Message\MpnsMessage;

/**
 * @covers \Jgut\Tify\Service\Message\MpnsMessageBuilder
 */
class MpnsMessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Tify\Service\Message\GcmMessageBuilder::build
     */
    public function testPushClient()
    {
        $service = new \Jgut\Tify\Service\MpnsService();
        $message = new \Jgut\Tify\Message\MpnsMessage(['title' => 'title', 'body' => 'body']);
        $notification = new \Jgut\Tify\Notification\MpnsNotification($service, $message);

        $client = MpnsMessageBuilder::build($notification);
        $this->assertInstanceOf(MpnsMessage::class, $client);
    }
}
