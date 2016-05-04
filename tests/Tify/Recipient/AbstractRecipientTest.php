<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Recipient;

use Jgut\Tify\Recipient\AbstractRecipient;

/**
 * AbstractRecipient tests.
 */
class AbstractRecipientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Recipient\AbstractRecipient
     */
    protected $recipient;

    public function setUp()
    {
        $this->recipient = $this->getMockForAbstractClass(
            AbstractRecipient::class,
            ['9a4ecb987ef59c88b12035278b86f26d44883593']
        );
    }

    public function testDefaults()
    {
        self::assertNull($this->recipient->getToken());
    }
}
