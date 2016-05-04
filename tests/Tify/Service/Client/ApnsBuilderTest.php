<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Client;

use Jgut\Tify\Service\Client\ApnsClientBuilder;
use ZendService\Apple\Apns\Client\Feedback;
use ZendService\Apple\Apns\Client\Message;

/**
 * Apns service builder
 */
class ApnsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Jgut\Tify\Exception\ServiceException
     * @expectedExceptionMessageRegExp /^Unable to connect/
     * @expectedExceptionMessageRegExp /^Unable to set local cert chain file/
     */
    public function testPushClient()
    {
        $client = ApnsClientBuilder::buildPush(__DIR__ . '/../../../files/apns_certificate.pem');

        self::assertInstanceOf(Message::class, $client);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\ServiceException
     * @expectedExceptionMessageRegExp /^Unable to connect/
     * @expectedExceptionMessageRegExp /^Unable to set local cert chain file/
     */
    public function testFeedbackClient()
    {
        $client = ApnsClientBuilder::buildFeedback(__DIR__ . '/../../../files/apns_certificate.pem');

        self::assertInstanceOf(Feedback::class, $client);
    }
}
