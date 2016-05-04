<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;
use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Adapter\FeedbackInterface;
use Jgut\Tify\Adapter\SendInterface;

/**
 * Notifications manager.
 */
class Manager
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $notifications;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->notifications = new ArrayCollection;
    }

    /**
     * Retrieve registered notifications.
     *
     * @return \Jgut\Tify\Notification[]
     */
    public function getNotifications()
    {
        return $this->notifications->toArray();
    }

    /**
     * Register notification.
     *
     * @param \Jgut\Tify\Notification $notification
     *
     * @return $this
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications->add($notification);

        return $this;
    }

    /**
     * Clear list of notifications.
     */
    public function clearNotifications()
    {
        $this->notifications->clear();

        return $this;
    }

    /**
     * Push notifications.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Jgut\Tify\Result[]
     */
    public function push()
    {
        $results = new ArrayCollection;

        foreach ($this->notifications as $notification) {
            /* @var \Jgut\Tify\Notification $notification */
            $notification->setStatus(Notification::STATUS_PENDING);
            $notification->clearResults();

            $adapter = $notification->getAdapter();
            // @codeCoverageIgnoreStart
            if ($adapter instanceof SendInterface) {
                $adapter->send($notification);
            }
            // @codeCoverageIgnoreEnd

            $results->add($notification->getResults());
        }

        return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($results)));
    }

    /**
     * Get feedback from service.
     *
     * @param \Jgut\Tify\Adapter\AbstractAdapter $adapter
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     *
     * @return array
     */
    public function feedback(AbstractAdapter $adapter)
    {
        if (!$adapter instanceof FeedbackInterface) {
            throw new AdapterException(sprintf('"%s" is not a feedback enabled service', get_class($adapter)));
        }

        return $adapter->feedback();
    }
}
