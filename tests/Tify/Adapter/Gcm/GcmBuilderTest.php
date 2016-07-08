<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter\Gcm;

use Jgut\Tify\Adapter\Gcm\GcmBuilder;
use Jgut\Tify\Adapter\Gcm\GcmMessage;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use ZendService\Google\Gcm\Client;

/**
 * Gcm service builder
 */
class GcmBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Adapter\Gcm\GcmBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->builder = new GcmBuilder;
    }

    public function testPushClient()
    {
        $client = $this->builder->buildPushClient('my_api_key');

        self::assertInstanceOf(Client::class, $client);
    }

    public function testPushMessage()
    {
        $message = new Message();

        $notification = new Notification($message, [], ['collapse_key' => 'my_key']);

        $pushMessage = $this->builder->buildPushMessage(['my_token'], $notification);

        self::assertInstanceOf(GcmMessage::class, $pushMessage);
        self::assertEquals('my_key', $pushMessage->getCollapseKey());
    }

    public function testNotificationPushMessage()
    {
        $message = new Message(['title_loc_key' => 'MESSAGE_TITLE']);

        $notification = new Notification($message, []);

        $pushMessage = $this->builder->buildPushMessage(['my_token'], $notification);

        self::assertInstanceOf(GcmMessage::class, $pushMessage);
        self::assertEquals('MESSAGE_TITLE', $pushMessage->getNotificationPayload()['title_loc_key']);
    }
}
