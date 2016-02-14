<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Notification;

use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\OptionsTrait;
use Jgut\Tify\Service\AbstractService;

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

        $this->setOptions(array_merge($this->getDefaultOptions(), $options));
    }

    /**
     * Get default notification options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }

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
     * Check if notification status is pushed.
     *
     * @return bool
     */
    final public function isSent()
    {
        return $this->status === static::STATUS_SENT;
    }

    /**
     * Set notification as sent.
     *
     * @param array $results
     */
    final public function setSent(array $results = [])
    {
        $this->status = static::STATUS_SENT;
        $this->results = $results;
    }

    /**
     * Set notification pending (not sent).
     */
    final public function setPending()
    {
        $this->status = static::STATUS_PENDING;
        $this->results = [];
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
     * @return array
     */
    final public function getTokens()
    {
        return array_keys($this->recipients);
    }
}
