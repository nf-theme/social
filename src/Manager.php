<?php

namespace NightFury\Social;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Socialite\Two\BitbucketProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\LinkedInProvider;
use League\OAuth1\Client\Server\Twitter as TwitterServer;
use NightFury\Social\Providers\One\TwitterProvider;

class Manager extends \Illuminate\Support\Manager implements Contracts\Factory
{
    const NF_SOCIAL_PROVIDER_META_KEY    = 'nf_social_provider';
    const NF_SOCIAL_ID_META_KEY          = 'nf_social_id';
    const NF_SOCIAL_PROFILE_URL_META_KEY = 'nf_social_profile_url';
    const NF_SOCIAL_IMAGE_META_KEY       = 'nf_social_image';
    const NF_SOCIAL_RAW_META_KEY         = 'nf_social_raw';
    const FACEBOOK_PROVIDER              = 'facebook';
    const TWITTER_PROVIDER               = 'twitter';
    const GOOGLE_PROVIDER                = 'google';

    public function loginWithAccessToken($provider, $token)
    {
        $driver = $this->driver($provider);
        $user   = $driver->userFromToken($token);
        return $this->login($user, $provider);
    }

    public function loginWithAuthToken($provider, $code, $verify_code = null)
    {
        $driver = $this->driver($provider);
        if ($provider == self::TWITTER_PROVIDER) {
            $request = new \Illuminate\Http\Request;
            $request->offsetSet('oauth_token', $code);
            $request->offsetSet('oauth_verifier', $verify_code);
            $driver->setRequest($request);
            $user = $driver->user();
            var_dump($user);die();
        } else {
            $response = $driver->getAccessTokenResponse($code);
            $token    = Arr::get($response, 'access_token');
            $user     = $driver->userFromToken($token);
        }
        return $this->login($user, $provider);
    }

    public function login(\Laravel\Socialite\AbstractUser $user, $provider = '')
    {
        $users = get_users(['meta_key' => Manager::NF_SOCIAL_ID_META_KEY, 'meta_value' => $user->getId()]);

        if (empty($users)) {
            $wp_user = $this->createUser($user->getName(), time(), $user->getEmail(), $user->getName());
            update_user_meta($wp_user->ID, Manager::NF_SOCIAL_PROVIDER_META_KEY, $provider);
            update_user_meta($wp_user->ID, Manager::NF_SOCIAL_ID_META_KEY, $user->getId());
            update_user_meta($wp_user->ID, Manager::NF_SOCIAL_IMAGE_META_KEY, $user->getAvatar());
            if (isset($user->profileUrl)) {
                update_user_meta($wp_user->ID, Manager::NF_SOCIAL_PROFILE_URL_META_KEY, $user->profileUrl);
            }
            update_user_meta($wp_user->ID, Manager::NF_SOCIAL_RAW_META_KEY, json_encode($user->getRaw()));
        } else {
            $wp_user = $users[0];
        }
        wp_set_current_user($wp_user->ID, $wp_user->user_login);
        wp_set_auth_cookie($wp_user->ID);
        do_action('wp_login', $wp_user->user_login);
        return $wp_user;
    }

    public function createUser($username, $password, $email, $display_name = null)
    {
        $username = sanitize_user($username);
        if (isset($username) && username_exists($username)) {
            $i = 1;
            while (username_exists($username)) {
                $i++;
                $username = $username . '_' . $i;
            }
        }
        if (isset($email) && email_exists($email)) {
            $email = null;
        }
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            throw new Exception($user_id->get_error_message(), 0);
        } else {
            $user = get_user_by('ID', $user_id);
            if (!isset($display_name)) {
                $display_name = $username;
            }
            $user->__set('display_name', $display_name);
            wp_update_user($user);
            return $user;
        }
    }

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGithubDriver()
    {
        $config = $this->app->config('services')['github'];

        return $this->buildProvider(
            GithubProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createFacebookDriver()
    {
        $config = $this->app->config('services')['facebook'];

        return $this->buildProvider(
            FacebookProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGoogleDriver()
    {
        $config = $this->app->config('services')['google'];

        return $this->buildProvider(
            GoogleProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createLinkedinDriver()
    {
        $config = $this->app->config('services')['linkedin'];

        return $this->buildProvider(
            LinkedInProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createBitbucketDriver()
    {
        $config = $this->app->config('services')['bitbucket'];

        return $this->buildProvider(
            BitbucketProvider::class, $config
        );
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->app['request'], $config['client_id'],
            $config['client_secret'], $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\One\AbstractProvider
     */
    protected function createTwitterDriver()
    {
        $config = $this->app->config('services')['twitter'];

        return new TwitterProvider(
            $this->app['request'], new TwitterServer($this->formatConfig($config))
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier'   => $config['client_id'],
            'secret'       => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
        ], $config);
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/')
        ? $this->app['url']->to($redirect)
        : $redirect;
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
}
