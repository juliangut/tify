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
use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Adapter\FeedbackAdapter;
use Jgut\Tify\Adapter\PushAdapter;

/**
 * Notifications service.
 */
class Service
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $adapters;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $notifications;

    /**
     * Manager constructor.
     *
     * @param \Jgut\Tify\Adapter\AbstractAdapter|\Jgut\Tify\Adapter\AbstractAdapter[]|null $adapters
     * @param \Jgut\Tify\Notification|\Jgut\Tify\Notification[]|null                       $notifications
     */
    public function __construct($adapters = null, $notifications = null)
    {
        $this->adapters = new ArrayCollection;
        if ($adapters !== null) {
            if (!is_array($adapters)) {
                $adapters = [$adapters];
            }

            $this->setAdapters($adapters);
        }

        $this->notifications = new ArrayCollection;
        if ($notifications !== null) {
            if (!is_array($notifications)) {
                $notifications = [$notifications];
            }

            $this->setNotifications($notifications);
        }
    }

    /**
     * Retrieve registered adapters.
     *
     * @return \Jgut\Tify\Adapter\AbstractAdapter[]
     */
    public function getAdapters()
    {
        return $this->adapters->toArray();
    }

    /**
     * Register adapters.
     *
     * @param array $adapters
     *
     * @return $this
     */
    public function setAdapters(array $adapters)
    {
        $this->adapters->clear();

        foreach ($adapters as $adapter) {
            $this->addAdapter($adapter);
        }

        return $this;
    }

    /**
     * Register adapter.
     *
     * @param \Jgut\Tify\Adapter\AbstractAdapter $adapter
     *
     * @return $this
     */
    public function addAdapter(AbstractAdapter $adapter)
    {
        $this->adapters->add($adapter);

        return $this;
    }

    /**
     * Clear list of adapters.
     */
    public function clearAdapters()
    {
        $this->adapters->clear();

        return $this;
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
     * Register notifications.
     *
     * @param array $notifications
     *
     * @return $this
     */
    public function setNotifications(array $notifications)
    {
        $this->notifications->clear();

        foreach ($notifications as $notification) {
            $this->addNotification($notification);
        }

        return $this;
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
     * @return \Jgut\Tify\Result[]
     */
    public function push()
    {
        $pushResults = [];

        /** @var \Jgut\Tify\Adapter\PushAdapter[] $pushAdapters */
        $pushAdapters = array_filter(
            $this->adapters->toArray(),
            function (AbstractAdapter $adapter) {
                return $adapter instanceof PushAdapter;
            }
        );

        foreach ($pushAdapters as $adapter) {
            foreach ($this->notifications as $notification) {
                foreach ($adapter->push($notification) as $pushResult) {
                    $pushResults[] = $pushResult;
                }
            }
        }

        return $pushResults;
    }

    /**
     * Get feedback from push services.
     *
     * @return \Jgut\Tify\Result[]
     */
    public function feedback()
    {
        $feedbackResults = [];

        /** @var \Jgut\Tify\Adapter\FeedbackAdapter[] $feedbackAdapters */
        $feedbackAdapters = array_filter(
            $this->adapters->toArray(),
            function (AbstractAdapter $adapter) {
                return $adapter instanceof FeedbackAdapter;
            }
        );

        foreach ($feedbackAdapters as $adapter) {
            foreach ($adapter->feedback() as $feedbackResult) {
                $feedbackResults[] = $feedbackResult;
            }
        }

        return array_unique($feedbackResults, SORT_REGULAR);
    }
}
