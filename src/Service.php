<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;
use Jgut\Tify\Adapter\Adapter;
use Jgut\Tify\Adapter\FeedbackAdapter;
use Jgut\Tify\Adapter\PushAdapter;

/**
 * Notifications service.
 */
class Service
{
    /**
     * Registered adapters.
     *
     * @var Adapter[]
     */
    protected $adapters;

    /**
     * Registered notifications.
     *
     * @var Notification[]
     */
    protected $notifications;

    /**
     * Manager constructor.
     *
     * @param Adapter|Adapter[]           $adapters
     * @param Notification|Notification[] $notifications
     */
    public function __construct($adapters = null, $notifications = null)
    {
        $this->adapters = new ArrayCollection;
        if ($adapters !== null) {
            if (!is_array($adapters)) {
                $adapters = [$adapters];
            }

            $this->setAdapters((array) $adapters);
        }

        $this->notifications = new ArrayCollection;
        if ($notifications !== null) {
            if (!is_array($notifications)) {
                $notifications = [$notifications];
            }

            $this->setNotifications((array) $notifications);
        }
    }

    /**
     * Retrieve registered adapters.
     *
     * @return Adapter[]
     */
    public function getAdapters()
    {
        return $this->adapters->toArray();
    }

    /**
     * Register adapters.
     *
     * @param Adapter[] $adapters
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
     * @param Adapter $adapter
     *
     * @return $this
     */
    public function addAdapter(Adapter $adapter)
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
     * @return Notification[]
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
     * @param Notification $notification
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
     * @return Result[]
     */
    public function push()
    {
        $pushResults = [];

        /* @var PushAdapter[] $pushAdapters */
        $pushAdapters = array_filter(
            $this->adapters->toArray(),
            function (Adapter $adapter) {
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
     * @return Result[]
     */
    public function feedback()
    {
        $feedbackResults = [];

        /* @var FeedbackAdapter[] $feedbackAdapters */
        $feedbackAdapters = array_filter(
            $this->adapters->toArray(),
            function (Adapter $adapter) {
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
