<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service\Client;

use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Service\Socket;
use ZendService\Google\Gcm\Client;

class GcmBuilder
{
    /**
     * Get opened push service client.
     *
     * @return \ZendService\Google\Gcm\Client
     */
    public static function buildPush()
    {
        $client = new Client;
        $client->setApiKey($this->getParameter('api_key'));

        $httpClient = new HttpClient(
            null,
            [
                'service' => Socket::class,
                'strictredirects' => true,
                'sslverifypeer' => false,
            ]
        );

        $client->setHttpClient($httpClient);

        return $client;
    }
}
