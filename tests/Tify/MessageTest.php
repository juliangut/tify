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
    }

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
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAPNSParameter()
    {
        $this->message->setParameter('apc', 'value');
    }

    /**
     * @dataProvider invalidGCMParametersProvider
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidGCMParameter($parameter)
    {
        $this->message->setParameter($parameter, 'value');
    }

    public function invalidGCMParametersProvider()
    {
        return [
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
        ];
    }
}
