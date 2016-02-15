<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Recipient;

use Jgut\Tify\Recipient\GcmRecipient;

/**
 * @covers \Jgut\Tify\Recipient\GcmRecipient
 */
class GcmRecipientTest extends \PHPUnit_Framework_TestCase
{
    protected $recipient;

    public function setUp()
    {
        $this->recipient = new GcmRecipient('f59c88b12035278b86f26d448835939a');
    }

    /**
     * @covers \Jgut\Tify\Recipient\GcmRecipient::setToken
     *
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->recipient->setToken('    ');
    }
}
