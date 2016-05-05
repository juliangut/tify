<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\Result;

/**
 * Result tests.
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Result
     */
    protected $result;

    public function setUp()
    {
        $this->result = new Result('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527');
    }

    public function testDefaults()
    {
        self::assertEquals(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527',
            $this->result->getToken()
        );
        self::assertInstanceOf('\DateTime', $this->result->getDate());
        self::assertEquals(Result::STATUS_SUCCESS, $this->result->getStatus());
        self::assertTrue($this->result->isSuccess());
        self::assertFalse($this->result->isError());
        self::assertEquals('', $this->result->getStatusMessage());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAccessorsMutators()
    {
        $this->result->setToken('b8e3802766d2ced54910e68332e8c1e204b05abe96421c21eecc9f0cbc1c7043');
        self::assertEquals(
            'b8e3802766d2ced54910e68332e8c1e204b05abe96421c21eecc9f0cbc1c7043',
            $this->result->getToken()
        );

        $date = new \DateTime;
        $this->result->setDate($date);
        self::assertEquals($date, $this->result->getDate());

        $this->result->setStatus(Result::STATUS_ERROR);
        self::assertEquals(Result::STATUS_ERROR, $this->result->getStatus());
        self::assertFalse($this->result->isSuccess());
        self::assertTrue($this->result->isError());

        $this->result->setStatusMessage('Error');
        self::assertEquals('Error', $this->result->getStatusMessage());

        $this->result->setStatus('my_status');
    }
}
