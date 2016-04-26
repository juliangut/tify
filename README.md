[![PHP version](https://img.shields.io/badge/PHP-%3E%3D5.5-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/vpre/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![License](https://img.shields.io/github/license/juliangut/tify.svg?style=flat-square)](https://github.com/juliangut/tify/blob/master/LICENSE)

[![Build status](https://img.shields.io/travis/juliangut/tify.svg?style=flat-square)](https://travis-ci.org/juliangut/tify)
[![Style](https://styleci.io/repos/47275107/shield)](https://styleci.io/repos/47275107)
[![Code Quality](https://img.shields.io/scrutinizer/g/juliangut/tify.svg?style=flat-square)](https://scrutinizer-ci.com/g/juliangut/tify)
[![Code Coverage](https://img.shields.io/coveralls/juliangut/tify.svg?style=flat-square)](https://coveralls.io/github/juliangut/tify)
[![Total Downloads](https://img.shields.io/packagist/dt/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)

# Tify

Unified push notification services abstraction layer to connect with Google GCM and Apple APNS services.

## Installation

Install using Composer:

```
composer require juliangut/tify
```

Then require_once the autoload file:

```php
require_once './vendor/autoload.php';
```

## Usage

Basic usage creating a message and sending it through GCM and APNS services.

```php
use Jgut\Tify\Recipient\ApnsRecipient;
use Jgut\Tify\Recipient\GcmRecipient;
use Jgut\Tify\Manager;
use Jgut\Tify\Message\ApnsMessage;
use Jgut\Tify\Message\GcmMessage;
use Jgut\Tify\Notification\ApnsNotification;
use Jgut\Tify\Notification\GcmNotification;
use Jgut\Tify\Service\AbstractService;
use Jgut\Tify\Service\ApnsService;
use Jgut\Tify\Service\GcmService;

$message = [
    'title' => 'title',
    'body' => 'body',
];

//Create GCM service interface
$gcmService = new GcmService(['api_key' => '00000']);

//Create GCM message
$gcmMessage = new GcnMessage($message);

//Create a list of GCM recipients
$gcmRecipients = [
    new GcmRecipient('aaaaaaaaaaa'),
    new GcmRecipient('bbbbbbbbbbb'),
];

//Combine previous to create a GCM notification
$gcmNotification = new GcmNotification($gcmService, $gcmMessage, $gcmRecipients);


//Create APNS service interface
$apnsService = new ApnsService(['certificate' => 'path_to_certificate']);

//Create APNS message
$apnsMessage = new ApnsMessage($message);

//Create a list of APNS recipients
$apnsRecipients = [
    new ApnsRecipient('ccccccccccc'),
    new ApnsRecipient('ddddddddddd'),
];

//Combine previous to create a APNS notification
$apnsNotification = new ApnsNotification($apnsService, $apnsMessage, $apnsRecipients);

$manager = new Manager;
$manager->addNotification($gcmNotification);
$manager->addNotification($apnsNotification);

$manager->send();
```

Except for the `Manager` component all the rest of the parts are service dependent, meaning there are recipients, messages and notificiations specific for the two services provided, Apple's `APNS` and Google's `GCM` and they have to combined accordingly.

## Recipient

Recipients have one mandatory parameter `token`.

```php
new \Jgut\Tify\Recipient\ApnsRecipient('recipient_token');
new \Jgut\Tify\Recipient\GcmRecipient('recipient_token');
```

APNS recipients can store an additional `badge` parameter that will be used in junction with message `badge` parameter if provided.

## Message

Messages compose the final information arriving to recipients. GCM and APNS messages hold different information according to each service specification.

In order for the message payload to be created `title` and/or `body` options should be provided. If they are not present custom message parameters should be included to be passed to the app.

Messages can hold any number of custom parametes that will compose additional data sent to the destination clients.

### APNS

```php
$message = new \Jgut\Tify\Message\ApnsMessage(['title' => 'title', 'body' => 'body']);

$message->setOption('loc_key', 'LOCK_KEY');
$message->setOption('launch_image', 'image.jpg');
...

$message->setParameter('param_1', 'value_1');
$message->setParameter('param_2', 'value_2');
```

*Find options [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-2.*

*There can not be an `aps` parameter as it is reserved by the APNS service.*

### GCM

```php
$message = new \Jgut\Tify\Message\GcmMessage;
$message->setTitle('title');
$message->setBody('body');

$message->setOption('click_action', 'OPEN_ACTIVITY_1');
$message->setOption('icon', 'icon.png');
...

$message->setParameter('param_1', 'value_1');
$message->setParameter('param_2', 'value_2');
```

*Find options [here](https://developers.google.com/cloud-messaging/http-server-ref#table2) in table 2.*

*Parameters should not be a reserved word (`from` or any word starting with `google` or `gcm`) or any GCM notification option. See [here](https://developers.google.com/cloud-messaging/http-server-ref#table2) for information on message options (notification payload)*

## Notification

Each notification holds all the information to send a notification using the desired service. Each kind of service has its own options.

### APNS

```php
$notification = new \Jgut\Tify\Notification\ApnsNotification($apnsService, $apnsMessage, $apnsRecipients, $options);

$notification->setOption('expire', 600);
$notification->setOption('badge', 1);
```

*Find options [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-1.*

### GCM

```php
$notification = new \Jgut\Tify\Notification\GcmNotification($apnsService, $apnsMessage, $apnsRecipients, $options);

$notification->setOption('time_to_live', 600);
$notification->setOption('dry_run', false);
```

*Find options [here](https://developers.google.com/cloud-messaging/http-server-ref#table1) in table 1.*

## Service

It is the piece that actually sends the messages. Services can be shared between notifications so normally you won't need to create more than one service for GCM and another for APNS and reuse them in all the notifications.

For APNS service `certificate` option is mandatory, denoting the path to the service certificate. In GCM `api_key` is the mandatory option denoting Google API key.

```php
$apnsService = new \Jgut\Tify\Service\ApnsService(['certificate' => 'path_to_certificate.pem']);
$gcmService = new \Jgut\Tify\Service\GcmService(['api_key' => 'google_api_key']);
```

## Manager

Holds the list of notifications and exposes two methods:

* `push` performs notifications send to devices.
* `feedback` uses APNS feedback service.

## Result

`push` service returns a list of Result objects in order to match return data from APNS and GCM services and have one common interface. This objects are composed of device token, date, status and status message.

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/juliangut/tify/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/juliangut/tify/blob/master/CONTRIBUTING.md)
## License

See file [LICENSE](https://github.com/juliangut/tify/blob/master/LICENSE) included with the source code for a copy of the license terms.
