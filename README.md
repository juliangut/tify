[![PHP version](https://img.shields.io/badge/PHP-%3E%3D5.5-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/vpre/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![License](https://img.shields.io/github/license/juliangut/tify.svg?style=flat-square)](https://github.com/juliangut/tify/blob/master/LICENSE)

[![Build status](https://img.shields.io/travis/juliangut/tify.svg?style=flat-square)](https://travis-ci.org/juliangut/tify)
[![Style Check](https://styleci.io/repos/47275107/shield)](https://styleci.io/repos/47275107)
[![Code Quality](https://img.shields.io/scrutinizer/g/juliangut/tify.svg?style=flat-square)](https://scrutinizer-ci.com/g/juliangut/tify)
[![Code Coverage](https://img.shields.io/coveralls/juliangut/tify.svg?style=flat-square)](https://coveralls.io/github/juliangut/tify)

[![Total Downloads](https://img.shields.io/packagist/dt/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![Monthly Downloads](https://img.shields.io/packagist/dm/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)

# Tify

Unified push notification services abstraction to connect with Google GCM and Apple APNS services.

## Installation

Install using Composer:

```
composer require juliangut/tify
```

Then require_once the autoload file:

```php
require_once './vendor/autoload.php';
```

## Elements

### Receiver

Each of the individual devices that will receive push notification. Identified by a device `token` provided by push notification service (APNS or GCM).

```php
new \Jgut\Tify\Receiver\ApnsReceiver('device_token');
new \Jgut\Tify\Receiver\GcmReceiver('device_token');
```

`device_token` follow different formats depending on service type. Review APNS and GCM documentation for proper formatting.

### Message

Compose the final information arriving to receivers. GCM and APNS messages hold different information according to each service specification.

*There are defined constants to identify each possible parameter*

In order for the message payload to be created one of the following message parameters must be set:

* For APNS: `Message::PARAMETER_TITLE`, `Message::PARAMETER_TITLE_LOC_KEY`, `Message::PARAMETER_TITLE_LOC_ARGS`, `Message::PARAMETER_BODY`, `Message::PARAMETER_BODY_LOC_KEY`, `Message::PARAMETER_BODY_LOC_ARGS`, `Message::PARAMETER_ACTION_LOC_KEY`, `Message::PARAMETER_LAUNCH_IMAGE`
* For GCM: `Message::PARAMETER_TITLE`, `Message::PARAMETER_TITLE_LOC_KEY`, `Message::PARAMETER_TITLE_LOC_ARGS`, `Message::PARAMETER_BODY`, `Message::PARAMETER_BODY_LOC_KEY`, `Message::PARAMETER_BODY_LOC_ARGS`, `Message::PARAMETER_ICON`, `Message::PARAMETER_SOUND`, `Message::PARAMETER_TAG`, `Message::PARAMETER_COLOR`

Messages can hold any number of custom payload data that will compose additional data sent to the destination receivers. This key/value payload data must comply with some limitations to be fully compatible with different services at once, for this a prefix (`data_` by default) is automatically added to the key. This prefix can be changed or removed if needed, but be aware that payload data should not be a reserved word such as `apns`, `from` or any word starting with `google` or `gcm` or GCM notification parameter name.

*Find APNS message parameters [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-2.*

*Find GCM message parameters [here](https://developers.google.com/cloud-messaging/http-server-ref#table2) in table 2.*

### Notification

It's a container to keep a message and its associated destination receivers.

Notifications are the central unit of work, several notifications can be set into a Service sharing the same adapters but sending different messages to different receivers.

Notifications hold some extra parameters used by the notification services to control behaviour and/or be used in notification creation.

*There are defined constants to identify each possible parameter*

By default Notification TTL is normalized in both services to "2 weeks" (1209600 seconds) instead of the default 4 weeks for GCM and immediate for APNS.

`Notification::TTL` parameter is used instead of GCM `time_to_live` and APNS `expire` to unify both services under the same interface, it must be an integer representing notification TTL in seconds.

*There are some convenience constants in Notification class for common TTL values*

*Find APNS notification parameters [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-1.*

*Find GCM notification parameters [here](https://developers.google.com/cloud-messaging/http-server-ref#table1) in table 1.*

### Adapter

Adapters will be given notifications to actually send the messages to associated receivers using the corresponding notification service. Receivers will be automatically filtered for the correct service by their type.

For APNS adapter `certificate` parameter is mandatory, denoting the path to the service certificate (.pem file) and `pass_phrase` parameter might be needed.

For GCM adapter `api_key` is the mandatory parameter denoting Google API key.

```php
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Adapter\GcmAdapter;

$apnsAdapter = new ApnsAdapter([ApnsAdapter::PARAMETER_CERTIFICATE => 'path_to_certificate.pem']);
$gcmAdapter = new GcmAdapter([GcmAdapter::PARAMETER_API_KEY => 'google_api_key']);
```

### Result

Responses from APNS and GCM push services are very different from one another, Result is a response abstraction in order to provide a common interface to access this non-equal returning data from APNS and GCM services.

This objects are composed of device token, date, status code (a status categorization) and status message (which corresponds to the original APNS or GCM response status).

#### Status Codes

* `STATUS_SUCCESS`, push was successful
* `STATUS_INVALID_DEVICE`, device token provided is invalid
* `STATUS_INVALID_MESSAGE`, message was not properly composed
* `STATUS_RATE_ERROR`, (only for GCM)
* `STATUS_AUTH_ERROR`, (only for GCM)
* `STATUS_SERVER_ERROR`
* `STATUS_UNKNOWN_ERROR`

Among all result statuses `STATUS_INVALID_DEVICE` is the most interesting because it is a signal that you should probably remove that token from your database.

### Service

For simplicity instead of handing notifications to adapters one by one 'Tify Service' can be used to send Notifications to its corresponding receivers using correct Adapter, automatically merging notification Results into a single returned array.

## Usage

### Push

Basic usage creating a one message to be sent through different adapters.

```php
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Adapter\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Service;

$adapters = [
    new GcmAdapter([GcmAdapter::PARAMETER_API_KEY => '00000']),
    new ApnsAdapter([ApnsAdapter::PARAMETER_CERTIFICATE => 'path_to_certificate']),
];

$message = new Message([
    Message::PARAMETER_TITLE => 'title',
    Message::PARAMETER_BODY => 'body',
]);

$receivers = [
    new GcmReceiver('aaaaaaaaaaa'),
    new GcmReceiver('bbbbbbbbbbb'),
    new ApnsReceiver('xxxxxxxxxxx'),
    new ApnsReceiver('zzzzzzzzzzz'),
];

$notification = new Notification($message, $receivers);
$notification->setParameter(Notification::PARAMETER_TTL, Notification::TTL_EXTRA_LONG);

$service = new Service($adapters, $notification);

$results = $service->push();
```

Sharing the same adapters to send different messages

```php
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Adapter\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Service;

$adapters = [
    new GcmAdapter([GcmAdapter::PARAMETER_API_KEY => '00000']),
    new ApnsAdapter([ApnsAdapter::PARAMETER_CERTIFICATE => 'path_to_certificate']),
];

$service = new Service($adapters);

$service->addNotification(new Notification(
    new Message([
        Message::PARAMETER_TITLE => 'title_one',
        Message::PARAMETER_BODY => 'body_one',
    ]),
    [
        new GcmReceiver('aaaaaaaaaaa'),
        new ApnsReceiver('xxxxxxxxxxx'),
    ],
    [
        Notification::PARAMETER_CONTENT_AVAILABLE => 1,  // Only for APNS
        Notification::PARAMETER_DRY_RUN => true, // Only for GCM
    ]
));

$service->addNotification(new Notification(
    new Message([
        Message::PARAMETER_TITLE => 'title_two',
        Message::PARAMETER_BODY => 'body_two',
    ]),
    [
        new GcmReceiver('bbbbbbbbbbb'),
        new ApnsReceiver('zzzzzzzzzzz'),
    ]
));

$results = $service->push();
```

### Feedback

Feedback returns Result objects with token and time of expired device tokens. Feedback service is only available for Apple APNS.

```php
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Service;

$adapters = [
    new ApnsAdapter([ApnsAdapter::PARAMETER_CERTIFICATE => 'path_to_certificate_one']),
    new ApnsAdapter([ApnsAdapter::PARAMETER_CERTIFICATE => 'path_to_certificate_two']),
];

$service = new Service($adapters);

$results = $service->feedback();
```

You should pay attention to results with `STATUS_INVALID_DEVICE` status in order to invalidate those tokens.

Feedback service for GCM can be mimicked by sending a test Notification (by setting parameter `dry_run` to true), this will NOT send the message to receivers but will return responses according to what would have happened if the messages were sent, so you can scan results for `STATUS_INVALID_DEVICE` status codes.

## Update 1.x to 2.x

GCM adapter now points to Firebase Cloud Messaging (FCM) as Google is moving forward to using this platform instead.

Adapters and Receivers abstract classes have been removed, interfaces have been introduced instead.

Notification's TTL is now controlled by `Notificaton::PARAMETER_TTL` instead of separated GCM and APNS parameters. You can still use `time_to_live` and `expire` as they are aliases of `Notificaton::PARAMETER_TTL`.

GCM adapter now supports "sandboxing" the same way APNS adapter did before. When setting sandbox constructor attribute to true the Notification sent to GCM will have `Notification::PARAMETER_DRY_RUN` parameter set to true regardless its previous value to ensure the message is not sent to receivers.

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/juliangut/tify/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/juliangut/tify/blob/master/CONTRIBUTING.md)
## License

See file [LICENSE](https://github.com/juliangut/tify/blob/master/LICENSE) included with the source code for a copy of the license terms.
