# DenGro Provider for OAuth 2.0 Client

This package provides DenGro OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require dengrocrm/oauth2-dengro
```

## Usage

Usage is the same as The League's OAuth client, using `\Dengro\OAuth2\Client\Provider\Dengro` as the provider.

### Authorization Code Flow

```php
$provider = new Dengro\OAuth2\Client\Provider\Dengro([
    'domain'            => '{domain}',
    'clientId'          => '{dengro-client-id}',
    'clientSecret'      => '{dengro-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getNickname());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

### Managing Scopes

When creating your DenGro authorization URL, you can specify the state and scopes your application may authorize.

```php
$options = [
    'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
    'scope' => ['user','user:email','repo'] // array or string
];

$authorizationUrl = $provider->getAuthorizationUrl($options);
```
If neither are defined, the provider will utilize internal defaults.

- user
- user:email

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/dengrocrm/oauth2-dengro/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Daniel Davidson](https://github.com/dan-rally)
- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/thephpleague/oauth2-client/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/dengrocrm/oauth2-dengro/blob/master/LICENSE) for more information.
