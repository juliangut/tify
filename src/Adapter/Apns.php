<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Adapter;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use Jgut\Pushat\Device\Apns as ApnsDevice;
use Jgut\Pushat\Exception\AdapterException;
use Jgut\Pushat\Exception\NotificationException;
use Jgut\Pushat\Notification\AbstractNotification;
use Jgut\Pushat\Notification\Apns as ApnsNotification;
use ZendService\Apple\Apns\Client\AbstractClient as AbstractServiceClient;
use ZendService\Apple\Apns\Client\Feedback as FeedbackServiceClient;
use ZendService\Apple\Apns\Client\Message as PushServiceClient;
use ZendService\Apple\Apns\Message as PushServiceMessage;
use ZendService\Apple\Apns\Message\Alert as PushServiceAlert;
use ZendService\Apple\Apns\Response\Message as PushServiceResponse;
use ZendService\Apple\Exception\RuntimeException as ServiceRuntimeException;

class Apns extends AbstractAdapter
{
    /**
     * @var \ZendService\Apple\Apns\Client\Message
     */
    protected $pushServiceClient;

    /**
     * @var \ZendService\Apple\Apns\Client\Feedback
     */
    protected $fbServiceClient;

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Pushat\Exception\AdapterException
     */
    public function __construct($environment = AbstractAdapter::ENVIRONMENT_PROD, array $parameters = [])
    {
        parent::__construct($environment, $parameters);

        $certificatePath = $this->getParameter('certificate');

        if ($certificatePath === null || !file_exists($certificatePath)) {
            throw new AdapterException(sprintf('Certificate file "%s" does not exist', $certificatePath));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Pushat\Exception\PushException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof ApnsNotification) {
            throw new InvalidArgumentException('Notification must be an accepted APNS notification');
        }

        $service = $this->getPushService();

        $pushedDevices = [];

        foreach ($notification->getDevices() as $device) {
            $message = $this->getServiceMessage($device, $notification);

            try {
                $this->response = $service->send($message);
            } catch (ServiceRuntimeException $exception) {
                throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
            }

            if ($this->response->getCode() === PushServiceResponse::RESULT_OK) {
                $pushedDevices[] = $device;
            }
        }

        return $pushedDevices;
    }

    /**
     * Feedback.
     *
     * @return array
     */
    public function feedback()
    {
        $service = $this->getFeedbackService();

        try {
            $feedbackResponse = $service->feedback();
        } catch (ServiceRuntimeException $exception) {
            throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $responses = [];

        foreach ($feedbackResponse as $response) {
            $time = new DateTime(date('c', $response->getTime()));
            $time->setTimeZone(new DateTimeZone('UTC'));

            $responses[$response->getToken()] = $time;
        }

        return $responses;
    }

    /**
     * Get opened ServiceClient
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected function getPushService()
    {
        if (!isset($this->pushServiceClient)) {
            $this->pushServiceClient = $this->getServiceClient(new PushServiceClient);
        }

        return $this->pushServiceClient;
    }

    /**
     * Get opened ServiceFeedbackClient
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected function getFeedbackService()
    {
        if (!isset($this->fbServiceClient)) {
            $this->fbServiceClient = $this->getServiceClient(new FeedbackServiceClient);
        }

        return $this->fbServiceClient;
    }

    /**
     * Get opened client.
     *
     * @param \ZendService\Apple\Apns\Client\AbstractClient $client
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected function getServiceClient(AbstractServiceClient $client)
    {
        $uri = $this->isProductionEnvironment()
            ? AbstractServiceClient::PRODUCTION_URI
            : AbstractServiceClient::SANDBOX_URI;

        $client->open(
            $uri,
            $this->getParameter('certificate'),
            $this->getParameter('pass_phrase')
        );

        return $client;
    }

    /**
     * Get service message from origin.
     *
     * @param \Jgut\Pushat\Device\Apns       $device
     * @param \Jgut\Pushat\Notification\Apns $notification
     *
     * @return \ZendService\Apple\Apns\Message
     */
    protected function getServiceMessage(ApnsDevice $device, ApnsNotification $notification)
    {
        $message = $notification->getMessage();

        $alert = new PushServiceAlert(
            $message->getOption('body'),
            $message->getOption('action_loc_key'),
            $message->getOption('loc_key'),
            $message->getOption('loc_args'),
            $message->getOption('launch_image'),
            $message->getOption('title'),
            $message->getOption('title_loc_key'),
            $message->getOption('title_loc_args')
        );

        $pushMessage = new PushServiceMessage();

        $pushMessage
            ->setId(sha1($device->getToken() . $message->getOption('body')))
            ->setToken($device->getToken())
            ->setAlert($alert)
            ->setSound($notification->getOption('sound'))
            ->setContentAvailable($notification->getOption('content_available'))
            ->setCategory($notification->getOption('category'))
            ->setCustom($message->getParameters());

        if ($notification->getOption('expire') !== null) {
            $pushMessage->setExpire($notification->getOption('expire'));
        }

        if ((int) $notification->getOption('badge') !== 0) {
            $pushMessage->setBadge($notification->getOption('badge') + $device->getParameter('badge', 0));
        }

        return $pushMessage;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return [
            'certificate',
            'pass_phrase',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultParameters()
    {
        return [
            'pass_phrase' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return [
            'certificate',
        ];
    }
}
