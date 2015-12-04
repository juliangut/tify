<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Recipient;

/**
 * @covers \Jgut\Tify\Recipient\AbstractRecipient
 */
class AbstractRecipientTest extends \PHPUnit_Framework_TestCase
{
    protected $recipient;

    public function setUp()
    {
        $this->recipient = $this->getMockForAbstractClass(
            '\Jgut\Tify\Recipient\AbstractRecipient',
            ['9a4ecb987ef59c88b12035278b86f26d44883593']
        );
    }

    /**
     * @covers \Jgut\Tify\Recipient\AbstractRecipient::getToken
     */
    public function testDefaults()
    {
        $this->assertNull($this->recipient->getToken());
    }
}
