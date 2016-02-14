<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

class Result
{
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    /**
     * Device token.
     *
     * @var string
     */
    protected $token;

    /**
     * Result time.
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * Result status.
     *
     * @var int
     */
    protected $status;

    /**
     * Result status message.
     *
     * @var string
     */
    protected $statusMessage;

    /**
     * @param string         $token
     * @param \DateTime|null $date
     * @param int            $status
     * @param string         $statusMessage
     */
    public function __construct($token, \DateTime $date = null, $status = self::STATUS_SUCCESS, $statusMessage = '')
    {
        $this->token = $token;

        $this->setDate($date !== null ? $date : new \DateTime);
        $this->setStatus($status);
        $this->setStatusMessage($statusMessage);
    }

    /**
     * Retrieve result device token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set result device token.
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = trim($token);

        return $this;
    }

    /**
     * Retrieve result time.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set result time.
     *
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = clone $date;

        return $this;
    }

    /**
     * Retrieve result status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Check successfull status.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status === static::STATUS_SUCCESS;
    }

    /**
     * Check errored status.
     *
     * @return bool
     */
    public function isError()
    {
        return $this->status === static::STATUS_ERROR;
    }

    /**
     * Set result status.
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        if (!is_int($status) || !in_array($status, [static::STATUS_SUCCESS, static::STATUS_ERROR])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid status', $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Retrieve result status message.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Check successfull status message.
     *
     * @param string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = trim($statusMessage);

        return $this;
    }
}
