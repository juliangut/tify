<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Client;

use Jgut\Tify\Service\Client\ApnsBuilder;
use ZendService\Apple\Apns\Client\Feedback;
use ZendService\Apple\Apns\Client\Message;

/**
 * @covers \Jgut\Tify\Service\Client\ApnsBuilder
 */
class ApnsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Jgut\Tify\Service\Client\ApnsBuilder::buildPush
     * @covers \Jgut\Tify\Service\Client\ApnsBuilder::buildClient
     *
     * @expectedException \Jgut\Tify\Exception\ServiceException
     * @expectedExceptionMessageRegExp /Unable to set local cert chain file/
     */
    public function testPushClient()
    {
        $client = ApnsBuilder::buildPush(dirname(dirname(dirname(__DIR__))) . '/files/apns_certificate.pem');

        $this->assertInstanceOf(Message::class, $client);
    }

    /**
     * @covers \Jgut\Tify\Service\Client\ApnsBuilder::buildFeedback
     * @covers \Jgut\Tify\Service\Client\ApnsBuilder::buildClient
     *
     * @expectedException \Jgut\Tify\Exception\ServiceException
     * @expectedExceptionMessageRegExp /Unable to set local cert chain file/
     */
    public function testPushFeedback()
    {
        $client = ApnsBuilder::buildFeedback(dirname(dirname(dirname(__DIR__))) . '/files/apns_certificate.pem');

        $this->assertInstanceOf(Feedback::class, $client);
    }
}
