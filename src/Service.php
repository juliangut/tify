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
use Jgut\Tify\Adapter\SendAdapter;

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

            foreach ($adapters as $adapter) {
                $this->addAdapter($adapter);
            }
        }

        $this->notifications = new ArrayCollection;
        if ($notifications !== null) {
            if (!is_array($notifications)) {
                $notifications = [$notifications];
            }

            foreach ($notifications as $notification) {
                $this->addNotification($notification);
            }
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
        $results = [];

        /** @var \Jgut\Tify\Adapter\SendAdapter[] $pushAdapters */
        $pushAdapters = array_filter(
            $this->adapters->toArray(),
            function (AbstractAdapter $adapter) {
                return $adapter instanceof SendAdapter;
            }
        );

        foreach ($pushAdapters as $adapter) {
            foreach ($this->notifications as $notification) {
                $notification->clearResults();

                $adapter->send($notification);

                foreach ($notification->getResults() as $result) {
                    $results[] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * Get feedback from push services.
     *
     * @return array
     */
    public function feedback()
    {
        $results = [];

        /** @var \Jgut\Tify\Adapter\FeedbackAdapter[] $feedbackAdapters */
        $feedbackAdapters = array_filter(
            $this->adapters->toArray(),
            function (AbstractAdapter $adapter) {
                return $adapter instanceof FeedbackAdapter;
            }
        );

        foreach ($feedbackAdapters as $adapter) {
            foreach ($adapter->feedback() as $result) {
                $results[] = $result;
            }
        }

        return array_unique($results);
    }
}
