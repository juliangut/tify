<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Recipient;

use Jgut\Tify\Recipient\WnsRecipient;

/**
 * @covers \Jgut\Tify\Recipient\WnsRecipient
 */
class WnsRecipientTest extends \PHPUnit_Framework_TestCase
{
    protected $recipient;

    public function setUp()
    {
        $this->recipient = new WnsRecipient('http://example.com');
    }

    /**
     * @covers \Jgut\Tify\Recipient\WnsRecipient::setToken
     *
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->recipient->setToken('    ');
    }
}
