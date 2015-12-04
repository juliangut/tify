<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

use Jgut\Tify\Service\AbstractService;
use Jgut\Tify\Exception\ServiceException;
use Jgut\Tify\Notification\AbstractNotification;
use Jgut\Tify\Service\FeedbackInterface;

class Manager
{
    /**
     * Registered notifications.
     *
     * @var array
     */
    protected $notifications = [];

    /**
     * Push notifications.
     *
     * @return \Jgut\Tify\Result
     */
    public function push()
    {
        $results = [];

        foreach ($this->notifications as $notification) {
            $notification->setPending();

            $notification->getService()->send($notification);

            $results = array_merge($results, $notification->getResults());
        }

        return $results;
    }

    /**
     * Get feedback from service.
     *
     * @param \Jgut\Tify\Service\AbstractService $service
     *
     * @throws \Jgut\Tify\Exception\ServiceException
     *
     * @return array
     */
    public function feedback(AbstractService $service)
    {
        if (!$service instanceof FeedbackInterface) {
            throw new ServiceException(sprintf('%s is not a feedback enabled service', get_class($service)));
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
     * @param \Jgut\Tify\Notification\AbstractNotification $notification
     */
    public function addNotification(AbstractNotification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }
}
