# Social login provider
 > It's an extension for our theme https://github.com/hieu-pv/nf-theme 
 
#### Installation
##### Step 1: Install Through Composer
```
composer require nf/social
```
##### Step 2: Add the Service Provider
> Open `config/app.php` and register the required service provider.

```php
  'providers'  => [
        // .... Others providers 
        \NightFury\Social\SocialServiceProvider::class,
    ],
```
##### Step 3: Create a callback url
Create a new page and use shortcode `[nf_social_oauth_callback]`, then use url of the page as callback url with one more query param `?provider={your_provider}` 

For example we have a page with url is `https://{your_domain}/oauth` then we use 
- `https://{your_domain}/oauth?provider=facebook` for Facebook app 
- `https://{your_domain}/oauth?provider=twitter` for Twitter app 
- `https://{your_domain}/oauth?provider=google` for Google app 

##### Step 4: Update your config file `config/app.php` with CLIENT_ID and SECRET_KEY
```php
<?php

return [
  // Other config 
  'services'   => [
        'facebook' => [
            'client_id'     => 'your_facebook_app_client_id',
            'client_secret' => 'your_facebook_app_secret_key',
            'redirect'      => 'https://{your_domain}/oauth?provider=facebook',
        ],
        'twitter'  => [
            'client_id'     => 'your_twitter_app_client_id',
            'client_secret' => 'your_twitter_app_secret_key',
            'redirect'      => 'https://{your_domain}/oauth?provider=twitter',
        ],
        'google'   => [
            'client_id'     => '',
            'client_secret' => '',
            'redirect'      => '',
        ],
    ],
]
```
##### Step 5: Show login urls
We have a shortcode to render login urls `[nf_social_url providers]`
```
[nf_social_url providers="facebook,twitter"]
```
use it anywhere that you want to display login button

Or you can generate login url for each provider
```php
$login_url = \NightFury\Social\Facades\Social::driver($your_driver)->redirect()->getTargetUrl();

```
