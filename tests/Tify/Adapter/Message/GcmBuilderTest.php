<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter\Message;

use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Adapter\GcmAdapter;
use Jgut\Tify\Adapter\Message\Gcm;
use Jgut\Tify\Adapter\Message\GcmMessageBuilder;

/**
 * Gcm message builder tests.
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testPushClient()
    {
        $service = new GcmAdapter(['api_key' => 'my_api_key']);

        $message = new Message(['title' => 'title', 'body' => 'body']);

        $notification = new Notification($service, $message);

        $client = GcmMessageBuilder::build(['my_token'], $notification);
        self::assertInstanceOf(Gcm::class, $client);
    }
}
