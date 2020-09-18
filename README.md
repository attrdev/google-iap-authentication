# google-iap-authentication

A simple class to help you authenticate the users accessing your ressources thru Google Identity-Aware Proxy.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install google-iap-authentication.

```bash
$ composer require attraction/google-iap-authentication:^1.0
```

This will install google-iap-authentication and all required dependencies. google-iap-authentication requires PHP 7.4 or newer.

## Usage

```php
use Attraction\GoogleIAPAuthentication;

// Project ID and Number are available in Google Cloud Project Settings - https://console.cloud.google.com/iam-admin/settings
$projectId = 'YOUR_PROJECT_ID';
$projectNumber = 'YOUR_PROJECT_NUMBER';

// You can also use your preferred framework to fetch the headers, here we use getallheaders() to make it simple.
$idToken = getallheaders()['X-Goog-Iap-Jwt-Assertion'] ?? '';

$iap = new GoogleIAPAuthentication($projectId, $projectNumber);

if($user = $iap->validateAssertion($idToken)) {
    // User is authentified
    print(sprintf('Authentication succesful. Email Address: %s - Unique ID: %s', $user->emailAddress, $user->id));
} else {
    // User is not authentified
    print("Authentication failed.");
}
```