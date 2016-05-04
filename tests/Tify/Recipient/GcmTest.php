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
 * Gcm recipient tests.
 */
class GcmTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Recipient\GcmRecipient
     */
    protected $recipient;

    public function setUp()
    {
        $this->recipient = new GcmRecipient('f59c88b12035278b86f26d448835939a');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->recipient->setToken('    ');
    }
}
