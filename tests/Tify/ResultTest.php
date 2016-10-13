<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\Result;

/**
 * Result tests.
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->result = new Result('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527', new \DateTime);
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
    public function testGetterSetters()
    {
        $this->result->setToken('b8e3802766d2ced54910e68332e8c1e204b05abe96421c21eecc9f0cbc1c7043');
        self::assertEquals(
            'b8e3802766d2ced54910e68332e8c1e204b05abe96421c21eecc9f0cbc1c7043',
            $this->result->getToken()
        );

        $date = new \DateTime;
        $this->result->setDate($date);
        self::assertEquals($date, $this->result->getDate());

        $this->result->setStatus(Result::STATUS_INVALID_DEVICE);
        self::assertEquals(Result::STATUS_INVALID_DEVICE, $this->result->getStatus());
        self::assertFalse($this->result->isSuccess());
        self::assertTrue($this->result->isError());

        $this->result->setStatusMessage('Error');
        self::assertEquals('Error', $this->result->getStatusMessage());

        $this->result->setStatus('fake_status');
    }

    public function testSerializable()
    {
        $date = new \DateTime;
        $serialized = [
            'token' => 'aaaaa',
            'date' => $date->format('c'),
            'status' => Result::STATUS_UNKNOWN_ERROR,
            'statusMessage' => 'Unknown Error',
        ];

        $this->result->setToken($serialized['token']);
        $this->result->setDate($date);
        $this->result->setStatus($serialized['status']);
        $this->result->setStatusMessage($serialized['statusMessage']);

        self::assertEquals(json_encode($serialized), json_encode($this->result));
    }
}
