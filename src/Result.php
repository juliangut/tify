<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

/**
 * Push result abstraction.
 */
class Result implements \JsonSerializable
{
    const STATUS_SUCCESS = 'success';
    const STATUS_INVALID_DEVICE = 'invalidDevice';
    const STATUS_INVALID_MESSAGE = 'invalidMessage';
    const STATUS_RATE_ERROR = 'rateError';
    const STATUS_AUTH_ERROR = 'authError';
    const STATUS_SERVER_ERROR = 'serverError';
    const STATUS_UNKNOWN_ERROR = 'unknownError';

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
     * @var string
     */
    protected $status;

    /**
     * Result status message.
     *
     * @var string
     */
    protected $statusMessage;

    /**
     * @param string             $token
     * @param int|\DateTime|null $date
     * @param string             $status
     * @param string|null        $statusMessage
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $token,
        $date = null,
        $status = self::STATUS_SUCCESS,
        $statusMessage = null
    ) {
        $this->token = $token;

        if (is_int($date)) {
            $date = \DateTime::createFromFormat('U', (string) $date);
        }
        if ($date === null) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
        }

        if (!$date instanceof \DateTime) {
            throw new \InvalidArgumentException('Wrong date value provided');
        }
        $this->setDate($date);

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
     *
     * @return $this
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
     *
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = clone $date;

        return $this;
    }

    /**
     * Retrieve result status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Check successful status.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status === static::STATUS_SUCCESS;
    }

    /**
     * Check error status.
     *
     * @return bool
     */
    public function isError()
    {
        return $this->status !== static::STATUS_SUCCESS;
    }

    /**
     * Set result status.
     *
     * @param string $status
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $ref = new \ReflectionClass(self::class);
        if (!in_array($status, $ref->getConstants())) {
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
     * Check successful status message.
     *
     * @param string|null $statusMessage
     *
     * @return $this
     */
    public function setStatusMessage($statusMessage = null)
    {
        $this->statusMessage = $statusMessage !== null ? trim($statusMessage) : null;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'token' => $this->token,
            'date' => $this->date->format('c'),
            'status' => $this->status,
            'statusMessage' => $this->statusMessage,
        ];
    }
}
