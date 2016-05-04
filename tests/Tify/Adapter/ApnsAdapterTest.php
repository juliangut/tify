<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter;

use Jgut\Tify\Adapter\ApnsAdapter;

/**
 * Apns adapter tests.
 */
class ApnsAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Adapter\AbstractAdapter
     */
    protected $adapter;

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     */
    public function testInvalidCertificate()
    {
        new ApnsAdapter(['certificate' => 'fake_path']);
    }
}
