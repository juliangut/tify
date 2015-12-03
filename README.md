[![Latest Version](https://img.shields.io/packagist/vpre/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![License](https://img.shields.io/github/license/juliangut/tify.svg?style=flat-square)](https://github.com/juliangut/tify/blob/master/LICENSE)

[![Build status](https://img.shields.io/travis/juliangut/tify.svg?style=flat-square)](https://travis-ci.org/juliangut/tify)
[![Style](https://styleci.io/repos/47275107/shield)](https://styleci.io/repos/47275107)
[![Code Quality](https://img.shields.io/scrutinizer/g/juliangut/tify.svg?style=flat-square)](https://scrutinizer-ci.com/g/juliangut/tify)
[![Code Coverage](https://img.shields.io/coveralls/juliangut/tify.svg?style=flat-square)](https://coveralls.io/github/juliangut/tify)
[![Total Downloads](https://img.shields.io/packagist/dt/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)

# Tify

Unified push notification services abstraction inspired by [NotificationPusher
](https://github.com/Ph3nol/NotificationPusher)

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
use Jgut\Pushat\Device\Apns as ApnsDevice;
use Jgut\Pushat\Device\Gcm as GcmDevice;
use Jgut\Pushat\Manager;
use Jgut\Pushat\Message\Apns as ApnsMessage;
use Jgut\Pushat\Message\Gcm as GcmMessage;
use Jgut\Pushat\Notification\Apns as ApnsNotification;
use Jgut\Pushat\Notification\Gcm as GcmNotification;
use Jgut\Pushat\Service\AbstractService;
use Jgut\Pushat\Service\Apns as ApnsService;
use Jgut\Pushat\Service\Gcm as GcmService;

$message = [
    'title' => 'title',
    'body' => 'body',
];

//Create GCM service interface
$gcmService = new GcmService(AbstractService::ENVIRONMENT_DEV, ['api_key' => '00000']);

//Create GCM message
$gcmMessage = new GcnMessage($message);

//Create a list of GCM devices
$gcmDevices = [
    new GcmDevice('aaaaaaaaaaa'),
    new GcmDevice('bbbbbbbbbbb'),
];

//Combine all to create a GCM notification
$gcmNotification = new GcmNotification($gcmService, $gcmMessage, $gcmDevices);


//Create APNS service interface
$apnsService = new ApnsService(AbstractService::ENVIRONMENT_DEV, ['certificate' => 'path_to_certificate']);

//Create APNS message
$apnsMessage = new ApnsMessage($message);

//Create a list of APNS devices
$apnsDevices = [
    new ApnsDevice('ccccccccccc'),
    new ApnsDevice('ddddddddddd'),
];

//Combine all to create a APNS notification
$apnsNotification = new ApnsNotification($apnsService, $apnsMessage, $apnsDevices);

$manager = new Manager;
$manager->addNotification($gcmNotification);
$manager->addNotification($apnsNotification);

$manager->send();
```

Except for the `Manager` component all the rest of the parts are service dependent, meaning there are devices, messages and notificiations specific for the two services provided, Apple's `APNS` and Google's `GCM` and they have to combined accordingly.

## Device

Devices have one mandatory parameter `token`. APNS devices can additionally hold ap optional parameter `badge` that will be used on notification send.

```php
new \Jgut\Pushat\Device\Apns('device_token');
new \Jgut\Pushat\Device\Gcm('device_token');
```

## Message

Messages compose the final information arriving to devices. GCM and APNS messages hold different information according to each service specification.

In order for the message to be shown on the device `title` and/or `body` options should be provided. If they are not present the notification will be handed directly to the application. If this is the case custom parameters should be included to be passed to the app.

Messages can hold any number of custom parametes that will compose additional data sent to the device.

### APNS

```php
$message = new \Jgut\Pushat\Message\Apns;
$message->setTitle('title');
$message->setBody('body');

$message->setOption('loc_key', 'LOCK_KEY');
$message->setOption('launch_image', 'image.jpg');
...

$message->setParameter('param_1', 'value_1');
$message->setParameter('param_2', 'value_2');
```

There can not be a `aps` parameter as it is reserved.

### GCM

```php
$message = new \Jgut\Pushat\Message\Gcm;
$message->setTitle('title');
$message->setBody('body');

$message->setOption('click_action', 'OPEN_ACTIVITY_1');
$message->setOption('icon', 'icon.png');
...

$message->setParameter('param_1', 'value_1');
$message->setParameter('param_2', 'value_2');
```

Parameters should not be a reserved word ("from" or any word starting with "google" or "gcm") or any GCM notification option. See [here](https://developers.google.com/cloud-messaging/http-server-ref#table2) for information on message options (notification payload)

## Notification

Each notification holds all the information to send a notification using the desired service. Each kind of service has its own options.

### APNS

```php
$notification = new \Jgut\Pushat\Service\Apns($apnsService, $apnsMessage, $apnsDevices, $options);

$notification->setOption('expire', 600);
$notification->setOption('badge', 1);
```

### GCM

```php
$notification = new \Jgut\Pushat\Service\Gcm($apnsService, $apnsMessage, $apnsDevices, $options);

$notification->setOption('time_to_live', 600);
$notification->setOption('dry_run', false);
```

## Service

It is the piece that actually send the messages. Services can be shared between notifications so normally you won't need to create more than one service for GCM and another for APNS and reuse them in all the notifications.

```php
$apnsService = new \Jgut\Pushat\Service\Apns(['certificate' => 'path_to_certificate.pem']);
$gcmService = new \Jgut\Pushat\Service\Gcm(['api_key' => 'google_api_key']);
```

Both services have `send` method to push notifications to its corresponding service. Additionally APNS service has `feedback` method to request from Apple's feedback service.

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/juliangut/tify/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/juliangut/tify/blob/master/CONTRIBUTING.md)
## License

See file [LICENSE](https://github.com/juliangut/tify/blob/master/LICENSE) included with the source code for a copy of the license terms.
