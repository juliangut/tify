<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter\Gcm;

use Jgut\Tify\Adapter\Gcm\GcmAdapter;
use Jgut\Tify\Adapter\Gcm\GcmBuilder;
use Jgut\Tify\Adapter\Gcm\GcmMessage;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\GcmReceiver;
use ZendService\Google\Gcm\Client;
use ZendService\Google\Gcm\Response;

/**
 * Gcm adapter tests.
 */
class GcmAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Adapter\Gcm\GcmAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $message = $this->getMock(GcmMessage::class, [], [], '', false);
        $message->expects(self::any())->method('getRegistrationIds')->will(self::returnValue(['aaa', 'bbb']));

        $response = $this->getMock(Response::class, [], [], '', false);
        $response->expects(self::any())->method('getResults')
            ->will(self::returnValue(['aaa' => [], 'bbb' => ['error' => 'NotRegistered']]));

        $client = $this->getMock(Client::class, [], [], '', false);
        $client->expects(self::any())->method('send')->will(self::returnValue($response));

        $builder = $this->getMock(GcmBuilder::class, [], [], '', false);
        $builder->expects(self::any())->method('buildPushClient')->will(self::returnValue($client));
        $builder->expects(self::any())->method('buildPushMessage')->will(self::returnValue($message));

        $this->adapter = new GcmAdapter(['api_key' => 'aaa'], $builder);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     */
    public function testInvalidApiKey()
    {
        new GcmAdapter();
    }

    public function testSend()
    {
        $message = $this->getMock(Message::class, [], [], '', false);

        $receiver = $this->getMock(GcmReceiver::class, [], [], '', false);
        $receiver->expects(self::any())->method('getToken')->will(self::returnValue('aaa'));

        $notification = new Notification($message, [$receiver]);
        $this->adapter->send($notification);

        self::assertCount(2, $notification->getResults());
    }
}
