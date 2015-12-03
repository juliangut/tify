<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat;

use Jgut\Pushat\Service\AbstractService;
use Jgut\Pushat\Exception\ServiceException;
use Jgut\Pushat\Notification\AbstractNotification;
use Jgut\Pushat\Service\FeedbackInterface;

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
            if ($notification->getService()->send($notification)) {
                $notification->setStatus(AbstractNotification::STATUS_PUSHED);
                $notification->setPushTime(new \DateTime);

                $pushedNotifications[] = $notification;
            }
        }

        return $pushedNotifications;
    }

    /**
     * Get feedback from service.
     *
     * @param \Jgut\Pushat\Service\AbstractService $service
     *
     * @throws \Jgut\Pushat\Exception\ServiceException
     *
     * @return array
     */
    public function feedback(AbstractService $service)
    {
        if (!$service instanceof FeedbackInterface) {
            throw new ServiceException(sprintf('%s is not a feedback enabled service', (string) $service));
        }

        return $service->feedback();
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
