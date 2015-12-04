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
 * @covers \Jgut\Tify\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    protected $result;

    public function setUp()
    {
        $this->result = new Result(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527',
            new \DateTime
        );
    }

    /**
     * @covers \Jgut\Tify\Result::getToken
     * @covers \Jgut\Tify\Result::getDate
     * @covers \Jgut\Tify\Result::getStatus
     * @covers \Jgut\Tify\Result::isSuccess
     * @covers \Jgut\Tify\Result::isError
     */
    public function testDefaults()
    {
        $this->assertEquals(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527',
            $this->result->getToken()
        );
        $this->assertInstanceOf('\DateTime', $this->result->getDate());
        $this->assertEquals(Result::STATUS_SUCCESS, $this->result->getStatus());
        $this->assertTrue($this->result->isSuccess());
        $this->assertFalse($this->result->isError());
        $this->assertEquals('', $this->result->getStatusMessage());
    }

    /**
     * @covers \Jgut\Tify\Result::getToken
     * @covers \Jgut\Tify\Result::setToken
     * @covers \Jgut\Tify\Result::getDate
     * @covers \Jgut\Tify\Result::setDate
     * @covers \Jgut\Tify\Result::getStatus
     * @covers \Jgut\Tify\Result::isSuccess
     * @covers \Jgut\Tify\Result::isError
     * @covers \Jgut\Tify\Result::setStatus
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAccessorsMutators()
    {
        $this->result->setToken('b8e3802766d2ced54910e68332e8c1e204b05abe96421c21eecc9f0cbc1c7043');
        $this->assertEquals(
            'b8e3802766d2ced54910e68332e8c1e204b05abe96421c21eecc9f0cbc1c7043',
            $this->result->getToken()
        );

        $date = new \DateTime;
        $this->result->setDate($date);
        $this->assertEquals($date, $this->result->getDate());

        $this->result->setStatus(Result::STATUS_ERROR);
        $this->assertEquals(Result::STATUS_ERROR, $this->result->getStatus());
        $this->assertFalse($this->result->isSuccess());
        $this->assertTrue($this->result->isError());

        $this->result->setStatusMessage('Error');
        $this->assertEquals('Error', $this->result->getStatusMessage());

        $this->result->setStatus('my_status');
    }
}
