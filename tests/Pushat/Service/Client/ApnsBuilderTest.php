<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Service\Client;

use Jgut\Pushat\Service\Client\ApnsBuilder;
use ZendService\Apple\Apns\Client\Feedback;
use ZendService\Apple\Apns\Client\Message;

/**
 * @covers \Jgut\Pushat\Service\Client\ApnsBuilder
 */
class ApnsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Pushat\Service\Client\ApnsBuilder::buildPush
     * @covers \Jgut\Pushat\Service\Client\ApnsBuilder::buildClient
     *
     * @expectedException \Jgut\Pushat\Exception\ServiceException
     * @expectedExceptionMessageRegExp /^stream_socket_client\(\): Unable to set local cert chain file/
     */
    public function testPushClient()
    {
        $client = ApnsBuilder::buildPush(dirname(dirname(dirname(__DIR__))) . '/files/apns_certificate.pem');

        $this->assertInstanceOf(Message::class, $client);
    }

    /**
     * @covers \Jgut\Pushat\Service\Client\ApnsBuilder::buildFeedback
     * @covers \Jgut\Pushat\Service\Client\ApnsBuilder::buildClient
     *
     * @expectedException \Jgut\Pushat\Exception\ServiceException
     * @expectedExceptionMessageRegExp /^stream_socket_client\(\): Unable to set local cert chain file/
     */
    public function testPushFeedback()
    {
        $client = ApnsBuilder::buildFeedback(dirname(dirname(dirname(__DIR__))) . '/files/apns_certificate.pem');

        $this->assertInstanceOf(Feedback::class, $client);
    }
}
