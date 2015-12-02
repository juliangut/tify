<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Adapter;

use InvalidArgumentException;
use Jgut\Pushat\Exception\PushException;
use Jgut\Pushat\Notification\AbstractNotification;
use Jgut\Pushat\Notification\Gcm as GcmNotification;
use Jgut\Pushat\Service\GcmServiceMessage as PushServiceMessage;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Socket;
use ZendService\Google\Exception\RuntimeException as ServiceRuntimeException;
use ZendService\Google\Gcm\Client as PushServiceClient;

class Gcm extends AbstractAdapter
{
    /**
     * @var \ZendService\Google\Gcm\Client
     */
    protected $pushServiceClient;

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Pushat\Exception\PushException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof GcmNotification) {
            throw new InvalidArgumentException('Notification must be an accepted GCM notification');
        }

        $service = $this->getPushService();

        $pushedDevices = [];

        foreach (array_chunk($notification->getDevices()->getTokens(), 100) as $tokensRange) {
            $message = $this->getServiceMessage($tokensRange, $notification);

            try {
                $this->response = $service->send($message);
            } catch (ServiceRuntimeException $exception) {
                throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
            }

            if ($this->response->getSuccessCount() > 0) {
                foreach ($tokensRange as $token) {
                    $pushedDevices[] = $notification->getDevices()->get($token);
                }
            }
        }

        return $pushedDevices;
    }

    /**
     * Get opened client.
     *
     * @return \ZendService\Google\Gcm\Client
     */
    protected function getPushService()
    {
        if (!isset($this->pushServiceClient)) {
            $this->pushServiceClient = new PushServiceClient;
            $this->pushServiceClient->setApiKey($this->getParameter('api_key'));

            $httpClient = new HttpClient(
                null,
                [
                    'adapter' => Socket::class,
                    'strictredirects' => true,
                    'sslverifypeer' => false
                ]
            );

            $this->pushServiceClient->setHttpClient($httpClient);
        }

        return $this->pushServiceClient;
    }

    /**
     * Get configured service message.
     *
     * @param array                         $tokens
     * @param \Jgut\Pushat\Notification\Gcm $notification
     *
     * @return \ZendService\Google\Gcm\Message
     */
    protected function getServiceMessage(array $tokens, GmcNotification $notification)
    {
        $message = $notification->getMessage();

        $pushMessage = new PushServiceMessage();

        $pushMessage
            ->setRegistrationIds($tokens)
            ->setCollapseKey($notification->getParameter('collapse_key'))
            ->setDelayWhileIdle($notification->getParameter('delay_while_idle'))
            ->setTimeToLive($notification->getParameter('time_to_live'))
            ->setRestrictedPackageName($notification->getParameter('restricted_package_name'))
            ->setDryRun($notification->getParameter('dry_run'))
            ->setData($message->getParameters());

        if ($message->getOption('title') !== null && $message->getOption('body') !== null) {
            $pushMessage->setNotificationPayload($message->getOptions());
        }

        return $pushMessage;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return [
            'api_key',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultParameters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return [
            'api_key'
        ];
    }
}
