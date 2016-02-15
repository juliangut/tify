<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\Exception\NotificationException;
use Jgut\Tify\Exception\ServiceException;
use Jgut\Tify\Notification\AbstractNotification;
use Jgut\Tify\Notification\ApnsNotification;
use Jgut\Tify\Result;
use Jgut\Tify\Service\Client\ApnsClientBuilder;
use Jgut\Tify\Service\Message\ApnsMessageBuilder;
use ZendService\Apple\Exception\RuntimeException as ServiceRuntimeException;

class ApnsService extends AbstractService implements SendInterface, FeedbackInterface
{
    const RESULT_OK = 0;

    /**
     * Status codes translation.
     *
     * @var array
     */
    private static $statusCodes = [
        0 => 'OK',
        1 => 'Processing Error',
        2 => 'Missing Recipient Token',
        3 => 'Missing Topic',
        4 => 'Missing Payload',
        5 => 'Invalid Token Size',
        6 => 'Invalid Topic Size',
        7 => 'Invalid Payload Size',
        8 => 'Invalid Token',
        10 => 'Shutdown',
        255 => 'Unknown Error',
    ];

    /**
     * @var \ZendService\Apple\Apns\Client\Message
     */
    protected $pushClient;

    /**
     * @var \ZendService\Apple\Apns\Client\Feedback
     */
    protected $feedbackClient;

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Tify\Exception\ServiceException
     */
    public function __construct(array $parameters = [], $sandbox = false)
    {
        parent::__construct($parameters, $sandbox);

        $certificatePath = $this->getParameter('certificate');

        if ($certificatePath === null || !file_exists($certificatePath)) {
            throw new ServiceException(sprintf('Certificate file "%s" does not exist', $certificatePath));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof ApnsNotification) {
            throw new \InvalidArgumentException('Notification must be an accepted APNS notification');
        }

        $service = $this->getPushService();

        $results = [];

        /* @var \Jgut\Tify\Recipient\ApnsRecipient $recipient */
        foreach ($notification->getRecipients() as $recipient) {
            $message = ApnsMessageBuilder::build($recipient, $notification);

            $result = new Result($recipient->getToken(), new \DateTime);

            try {
                $response = $service->send($message);

                if ($response->getCode() !== static::RESULT_OK) {
                    $result->setStatus(Result::STATUS_ERROR);
                    $result->setStatusMessage(self::$statusCodes[$response->getCode()]);
                }
            } catch (ServiceRuntimeException $exception) {
                $result->setStatus(Result::STATUS_ERROR);
                $result->setStatusMessage($exception->getMessage());
            }

            $results[] = $result;
        }

        $notification->setSent($results);

        $service->close();
        $this->pushClient = null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Tify\Exception\NotificationException
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
            $time = new \DateTime(date('c', $response->getTime()));

            $responses[$response->getToken()] = $time;
        }

        $service->close();
        $this->feedbackClient = null;

        return $responses;
    }

    /**
     * Get opened ServiceClient
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    protected function getPushService()
    {
        if (!isset($this->pushClient)) {
            $this->pushClient = ApnsClientBuilder::buildPush(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->sandbox
            );
        }

        return $this->pushClient;
    }

    /**
     * Get opened ServiceFeedbackClient
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    protected function getFeedbackService()
    {
        if (!isset($this->feedbackClient)) {
            $this->feedbackClient = ApnsClientBuilder::buildFeedback(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->sandbox
            );
        }

        return $this->feedbackClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return ['certificate', 'pass_phrase'];
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
        return ['certificate'];
    }
}
