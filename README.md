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
> You can register any route by update `routes/api.php`

```php
<?php

return [
  // Other config 
  'services'   => [
        'facebook' => [
            'client_id'     => '167002993705494',
            'client_secret' => '7e22c6e5253831c1938cfb50ae23f767',
            'redirect'      => 'http://wp-origin.dev/oauth?provider=facebook',
        ],
        'twitter'  => [
            'client_id'     => 'BYk9oKJKxLMqfYYbRAJXwPKrt',
            'client_secret' => 'lkcqQzaR0V7FCFUyJE55uTxvUQlZfnzBEIE3sIxKHTXNf1bNQw',
            'redirect'      => 'http://wp-origin.dev/oauth?provider=twitter',
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
