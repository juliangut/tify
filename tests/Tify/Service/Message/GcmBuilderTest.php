<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Message;

use Jgut\Tify\Service\Message\Gcm;
use Jgut\Tify\Service\Message\GcmMessageBuilder;

/**
 * @covers \Jgut\Tify\Service\Message\GcmBuilder
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testPushClient()
    {
        $service = new \Jgut\Tify\Service\GcmService(['api_key' => 'my_api_key']);

        $message = new \Jgut\Tify\Message(['title' => 'title', 'body' => 'body']);

        $notification = new \Jgut\Tify\Notification($service, $message);

        $client = GcmMessageBuilder::build(['my_token'], $notification);
        $this->assertInstanceOf(Gcm::class, $client);
    }
}
