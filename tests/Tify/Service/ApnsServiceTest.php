<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service;

use Jgut\Tify\Service\ApnsService;

/**
 * Apns service tests.
 */
class ApnsServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Service\AbstractService
     */
    protected $service;

    /**
     * @expectedException \Jgut\Tify\Exception\ServiceException
     */
    public function testInvalidCertificate()
    {
        new ApnsService(['certificate' => 'fake_path']);
    }
}
