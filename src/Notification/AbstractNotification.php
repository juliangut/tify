<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Notification;

use Doctrine\Common\Collections\ArrayCollection;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\OptionsTrait;
use Jgut\Tify\Service\AbstractService;

/**
 * Class AbstractNotification
 */
abstract class AbstractNotification
{
    use OptionsTrait;

    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;

    /**
     * @var \Jgut\Tify\Service\AbstractService
     */
    protected $service;

    /**
     * @var \Jgut\Tify\Message\AbstractMessage
     */
    protected $message;

    /**
     * @var \Jgut\Tify\Recipient\AbstractRecipient[]
     */
    protected $recipients = [];

    /**
     * @var int
     */
    protected $status = self::STATUS_PENDING;

    /**
     * Notification resultss.
     *
     * @var array
     */
    protected $results = [];

    /**
     * @param \Jgut\Tify\Service\AbstractService       $service
     * @param \Jgut\Tify\Message\AbstractMessage       $message
     * @param \Jgut\Tify\Recipient\AbstractRecipient[] $recipients
     * @param array                                    $options
     */
    public function __construct(
        AbstractService $service,
        AbstractMessage $message,
        array $recipients = [],
        array $options = []
    ) {
        $this->service = $service;
        $this->message = $message;

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        $this->options = new ArrayCollection(array_merge($this->getDefaultOptions(), $options));
    }

    /**
     * Get default notification options.
     *
     * @return array
     */
    abstract protected function getDefaultOptions();

    /**
     * Get service.
     *
     * @return \Jgut\Tify\Service\AbstractService
     */
    final public function getService()
    {
        return $this->service;
    }

    /**
     * Set service.
     *
     * @param \Jgut\Tify\Service\AbstractService $service
     */
    abstract public function setService(AbstractService $service);

    /**
     * Get message.
     *
     * @return \Jgut\Tify\Message\AbstractMessage
     */
    final public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message.
     *
     * @param \Jgut\Tify\Message\AbstractMessage $message
     */
    abstract public function setMessage(AbstractMessage $message);

    /**
     * Retrieve list of recipients.
     *
     * @return \Jgut\Tify\Recipient\AbstractRecipient[]
     */
    final public function getRecipients()
    {
        return array_values($this->recipients);
    }

    /**
     * Add recipient.
     *
     * @param \Jgut\Tify\Recipient\AbstractRecipient $recipient
     */
    abstract public function addRecipient(AbstractRecipient $recipient);

    /**
     * Retrieve notification status.
     *
     * @return int
     */
    final public function getStatus()
    {
        return $this->status;
    }

    /**
     * Check if notification status is sent.
     *
     * @return bool
     */
    final public function isSent()
    {
        return $this->status === static::STATUS_SENT;
    }

    /**
     * Check if notification status is pending.
     *
     * @return bool
     */
    final public function isPending()
    {
        return $this->status === static::STATUS_PENDING;
    }

    /**
     * Set notification status.
     *
     * @param int                 $status
     * @param \Jgut\Tify\Result[] $results
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    final public function setStatus($status, array $results = [])
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_SENT])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid status', $status));
        }

        $this->status = $status;

        $this->results = [];
        if ($status === static::STATUS_SENT) {
            $this->results = $results;
        }

        return $this;
    }

    /**
     * Retrieve results.
     *
     * @return \Jgut\Tify\Result[]
     */
    final public function getResults()
    {
        return $this->results;
    }

    /**
     * Retrieve recipients tokens list.
     *
     * @return string[]
     */
    final public function getTokens()
    {
        return array_keys($this->recipients);
    }
}
