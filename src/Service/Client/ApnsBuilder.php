<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service\Client;

use ZendService\Apple\Apns\Client\AbstractClient;
use ZendService\Apple\Apns\Client\Feedback;
use ZendService\Apple\Apns\Client\Message;

class ApnsBuilder
{
    /**
     * Get opened push service client.
     *
     * @param bool $production
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public static function buildPush($production = true)
    {
        return static::buildClient(new Message, $production);
    }

    /**
     * Get opened feedback service client.
     *
     * @param bool $production
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public static function buildFeedback($production = true)
    {
        return static::buildClient(new Feedback, $production);
    }

    /**
     * Get opened client.
     *
     * @param \ZendService\Apple\Apns\Client\AbstractClient $client
     * @param bool                                          $production
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected static function buildClient(AbstractClient $client, $production = true)
    {
        $client->open(
            (bool) $production ? AbstractClient::PRODUCTION_URI : AbstractClient::SANDBOX_URI,
            $this->getParameter('certificate'),
            $this->getParameter('pass_phrase')
        );

        return $client;
    }
}
