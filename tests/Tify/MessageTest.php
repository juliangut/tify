<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Message;

use Jgut\Tify\Message;

/**
 * Message tests.
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Message
     */
    protected $message;

    public function setUp()
    {
        $this->message = new Message;
    }

    /**
     * @dataProvider defaultParametersProvider
     */
    public function testDefaults($parameter)
    {
        self::assertTrue($this->message->hasParameter($parameter));
        self::assertNull($this->message->getParameter($parameter));

        self::assertNull($this->message->getPayload('any_parameter'));
        self::assertEmpty($this->message->getPayloadData());
    }

    /**
     * Parameters provider
     *
     * @return array
     */
    public function defaultParametersProvider()
    {
        return [
            ['title'],
            ['body'],
        ];
    }

    public function testMutators()
    {
        $this->message->setTitle('message title');
        self::assertEquals('message title', $this->message->getParameter('title'));

        $this->message->setBody('message body');
        self::assertEquals('message body', $this->message->getParameter('body'));

        self::assertEquals('data_', $this->message->getPayloadPrefix());
        $this->message->setPayloadPrefix('');
        self::assertEquals('', $this->message->getPayloadPrefix());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAPNSPayload()
    {
        $this->message->setPayloadPrefix('');

        $this->message->setPayload('apc', 'value');
    }

    /**
     * @dataProvider invalidGCMPayloadProvider
     *
     * @param string $parameter
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidGCMPayload($parameter)
    {
        $this->message->setPayloadPrefix('');

        $this->message->setPayload($parameter, 'value');
    }

    /**
     * Payload provider.
     *
     * @return array
     */
    public function invalidGCMPayloadProvider()
    {
        return [
            [''],
            ['google'],
            ['google_param'],
            ['gcm'],
            ['gcm_param'],
            ['from'],
            ['collapse_key'],
            ['delay_while_idle'],
            ['time_to_live'],
            ['restricted_package_name'],
            ['dry_run'],
            ['priority'],
            ['content_available'],
        ];
    }

    public function testPayload()
    {
        $this->message->setPayload('first', true);
        self::assertTrue($this->message->hasPayload('first'));
        self::assertTrue($this->message->getPayload('first'));
        self::assertCount(1, $this->message->getPayloadData());

        $this->message->setPayloadData([
            'second' => 'second',
            'third' => 'third',
        ]);
        self::assertTrue($this->message->hasPayload('second'));
        self::assertCount(2, $this->message->getPayloadData());
    }
}
