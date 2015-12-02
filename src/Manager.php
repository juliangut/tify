<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat;

use DateTime;
use InvalidArgumentException;
use Jgut\Pushat\Adapter\AbstractAdapter;
use Jgut\Pushat\Exception\AdapterException;
use Jgut\Pushat\Notification\AbstractNotification;

class Manager
{
    protected $notifications = [];

    /**
     * Push notifications.
     *
     * @return array
     */
    public function push()
    {
        $pushedNotifications = [];

        foreach ($this->notifications as $notification) {
            if ($notification->getAdapter()->send($notification)) {
                $notification->setStatus(AbstractNotification::STATUS_PUSHED);
                $notification->setPushTime(new DateTime);

                $pushedNotifications[] = $notification;
            }
        }

        return $pushedNotifications;
    }

    /**
     * Get feedback from service.
     *
     * @param \Jgut\Pushat\Adapter\AbstractAdapter $adapter
     *
     * @return array
     *
     * @throws AdapterException When the adapter has no dedicated `getFeedback` method
     */
    public function feedback(AbstractAdapter $adapter)
    {
        if (method_exists($adapter, 'feedback') === false) {
            throw new AdapterException(sprintf('%s adapter has no dedicated "getFeedback" method', (string) $adapter));
        }

        return $adapter->feedback();
    }

    /**
     * Retrieve registered notifications.
     *
     * @return array
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Register notification.
     *
     * @param \Jgut\Pushat\Notification\AbstractNotification $notification
     */
    public function addNotification(AbstractNotification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }
}
