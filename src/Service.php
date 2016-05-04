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
     * @var \Jgut\Tify\Adapter\AbstractAdapter
     */
    protected $adapters;

    /**
     * @var \Jgut\Tify\Notification[]
     */
    protected $notifications;

    /**
     * Manager constructor.
     */
    public function __construct(array $adapters = [], array $notifications = [])
    {
        $this->adapters = new ArrayCollection;
        foreach ($adapters as $adapter) {
            $this->addAdapter($adapter);
        }

        $this->notifications = new ArrayCollection;
        foreach ($notifications as $notification) {
            $this->addNotification($notification);
        }
    }

    /**
     * Retrieve registered adapters.
     *
     * @return \Jgut\Tify\Notification[]
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

                $results[] = $notification->getResults();
            }
        }

        $return = array();
        array_walk_recursive($results, function ($current) use (&$return) {
            $return[] = $current;
        });

        return $return;
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
            $results[] = $adapter->feedback();
        }

        $return = array();
        array_walk_recursive($results, function ($current) use (&$return) {
            $return[] = $current;
        });

        return array_unique($return);
    }
}
