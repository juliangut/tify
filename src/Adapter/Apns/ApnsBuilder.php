<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter\Apns;

use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\Notification;
use Jgut\Tify\Recipient\ApnsRecipient;
use ZendService\Apple\Apns\Client\AbstractClient;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as MessageClient;
use ZendService\Apple\Apns\Message\Alert as ServiceMessageAlert;
use ZendService\Apple\Apns\Message as ServiceMessage;

/**
 * Class ApnsBuilder
 */
class ApnsBuilder
{
    /**
     * Get opened push service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $sandbox
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public function buildPushClient($certificate, $passPhrase = '', $sandbox = false)
    {
        return $this->buildClient(new MessageClient, $certificate, $passPhrase, $sandbox);
    }

    /**
     * Get opened feedback service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $sandbox
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    public function buildFeedbackClient($certificate, $passPhrase = '', $sandbox = false)
    {
        return $this->buildClient(new FeedbackClient, $certificate, $passPhrase, $sandbox);
    }

    /**
     * Get opened client.
     *
     * @param \ZendService\Apple\Apns\Client\AbstractClient $client
     * @param string                                        $certificate
     * @param string                                        $passPhrase
     * @param bool                                          $sandbox
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     *
     * @codeCoverageIgnore
     */
    protected function buildClient(AbstractClient $client, $certificate, $passPhrase = '', $sandbox = false)
    {
        try {
            $client->open(
                (bool) $sandbox ? AbstractClient::SANDBOX_URI : AbstractClient::PRODUCTION_URI,
                $certificate,
                $passPhrase
            );
        } catch (\Exception $exception) {
            throw new AdapterException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $client;
    }

    /**
     * Get service message from origin.
     *
     * @param \Jgut\Tify\Recipient\ApnsRecipient $recipient
     * @param \Jgut\Tify\Notification            $notification
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \ZendService\Apple\Apns\Message
     */
    public function buildPushMessage(ApnsRecipient $recipient, Notification $notification)
    {
        $message = $notification->getMessage();

        $badge = ((int) $notification->getParameter('badge', 0) === 0)
            ? null
            : $notification->getParameter('badge') + (int) $recipient->getParameter('badge', 0);

        $pushMessage = new ServiceMessage();

        $pushMessage
            ->setId(sha1($recipient->getToken() . $message->getParameter('body')))
            ->setToken($recipient->getToken())
            ->setSound($notification->getParameter('sound'))
            ->setContentAvailable($notification->getParameter('content_available'))
            ->setCategory($notification->getParameter('category'))
            ->setCustom($message->getPayload())
            ->setBadge($badge);

        if ($notification->getParameter('expire') !== null) {
            $pushMessage->setExpire($notification->getParameter('expire'));
        }

        if ($message->getParameter('title') !== null || $message->getParameter('body') !== null) {
            $pushMessage->setAlert(new ServiceMessageAlert(
                $message->getParameter('body'),
                $message->getParameter('action_loc_key'),
                $message->getParameter('loc_key'),
                $message->getParameter('loc_args'),
                $message->getParameter('launch_image'),
                $message->getParameter('title'),
                $message->getParameter('title_loc_key'),
                $message->getParameter('title_loc_args')
            ));
        }

        return $pushMessage;
    }
}
