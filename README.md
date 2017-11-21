
## Installation

You can install [`mutlucell/api`](https://packagist.org/packages/mutlucell/api)  via composer or by typing in command line  
```text
composer require mutlucell/api
```

**OR**   include require in **composer.json** 
 
```json
{
"require": {
           "mutlucell/api": "1.0.0"
       }
}
```

## Quickstart

### TO USE API AFTER DOWNLOAD
 In order to use Mutlucell Api, you must take the following steps:
 1. Registration on the site through the following steps:
    
    1. Go to the following link: **[`www.mutlucell.com.tr`](https://www.mutlucell.com.tr/)**
    2. Go to link (New Subscription) at the top of the page
    3. Enter the information
 2. Download the Mutlucell Api and install it in your system
 3. Insert user information (ApiKEY or mobile and password) ,this information's is provided to you by the site
 In the function setInfo defines user information

### Mutlucell Api Portals
We provide many services that make it easy to use the api, and these are some our of the services: 
1. Send bulk
2. Incomeing
3. Bulk report
4. Bulk summary report
5. Search bulk report
6. Credit
7. Senders
8. Cancel pending bulk
9. Add blacklist
10.Show blacklist
11.Delete blacklist
## Services Example

### Send bulk message
You can  send SMS messages using the transmission gate to ensure the privacy of information and the speed of sending and ensure they arrive, and this portal provid the ability to sending messages to many numbers at once and without any effortless and tired, the gate was receive data using XML technology And These an example of how to use the portal :

Send sms message :
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->sendBulk('This is Message','908555555555,908222222222','Sender');
?>
```
Send sms message with turkish charset and addLinkToEnd which adds description link in case  If you do not want to receive our messages .
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->sendBulk('This is Message','908555555555,908222222222','Sender','','',true,'','','','','turkish');
?>
```
Send message with send time  and expire send time  and the IP of the system sending the SMS and subscriber id and then the portal that is sending messages through which
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->sendBulk('This is Message','908555555555,908222222222','Sender','2017-12-27 18:30','2018-1-27 23:59',false,'78.85.12.15','14587','','','','curl');
?>
```
send message with vendor application
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->sendBulk('This is Message','908555555555,908222222222','Sender','','',false,'','','xyzsoft');
?>
```


### Incomeing
This portal offers the ability to get SMS messages using a subscriber number like this (0850 550 XX XX) , you can get it from your account on **[`www.mutlucell.com.tr`](https://www.mutlucell.com.tr/)** , and spacify the start date and end date
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->incomeing('0850 550 XX XX','2017-01-01 14:30','2017-12-30 14:30','curl');
?>
```

### Bulk Report
You can query your first report 1 minute after sending the message. Where there are pending messages, you should post the report interrogation code every 15 minutes instead of posting frequently. Otherwise, the system will detect frequently requested report inquiry requests as an attack and block your account.<br/> 
**Note:** You can get the id number from  the transmission result.
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->bulkReport('88096496');
?>
```

### Bulk Summary Report
You can get status reports of packages was sent in a certain time interval.<br/>
**Note :** The time interval cannot exceed 30 days.
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->bulkSummaryReport("2017-12-01 14:30","2017-12-30 14:30");
?>
```

### Search Bulk Report
You can search for messages sent to a specific number at a certain time interval
,and it returns reports for these messages.
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->searchBulkReport("5339998877","2017-12-01 14:30","2017-12-30 14:30");
?>
```

### Credit
This portal is get your account balance at the site.
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->credit();
?>
```

### Originators
You can get your originators from this portal and your originators will return with tab ("\ t").
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->originators();
?>
```
### Cancel Pending Bulk
This portal help you to cancel pending messages, the id parameter here is the ID that the SMS portal sends for the message to be canceled.
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->cancelPendingBulk("88102496");
?>
```
### Add Blacklist
With add blacklist portal, the sending of sms to desired number can be prevented for the specified account.
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->addBlacklist('905551112233');
?>
```
### Delete Blacklist
With this portal, you can delete any number you have added to the blacklist
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->deleteBlacklist('905551112233');
?>
```

### Show Blacklist
This portal get all blacklist number you have bean added   
```php
<?php
require_once('Mutlucell.php');
$sms = new Mutlucell('user name','password','ApiKEY');
$result=$sms->deleteBlacklist('905551112233');
?>
```

## Documentation

The documentation for the **mutlucell Api** is located **[`here`](http://mutlucell.com/api/)**.

The PHP library documentation can be found **[`here`](http://mutlucell.com/api/)**.

## Versions

`Mutlucell api`'s versioning strategy can be found **[`here`](http://mutlucell.com/api/)**.

## Prerequisites

* PHP >= 5.3
* The PHP XML extension

# Getting help

If you need help installing or using the library, please contact mutlucell.com.tr Support at **info@mutlusantral.com** first. mutlucell Support staff are well-versed in all of the mutlucell.com.tr Helper Libraries, and usually reply within 24 hours.

If you've instead found a bug in the library or would like new features added, go ahead and open issues or pull requests against this repo!

