<?php

namespace NightFury\Social\ShortCodes;

use Exception;
use NF\Abstracts\ShortCode;
use NF\Facades\Request;
use NightFury\Social\Facades\Social;
use NightFury\Social\Manager;

class OauthShortCode extends ShortCode
{
    public $name = 'nf_social_oauth_callback';

    public function render($attrs)
    {
        $provider = Request::get('provider');

        if (is_user_logged_in()) {
            throw new Exception("Another user is logged in", 1);
        }

        switch ($provider) {
            case Manager::FACEBOOK_PROVIDER:
                $user = Social::driver(Manager::FACEBOOK_PROVIDER)->stateless()->user();
                break;
            case Manager::TWITTER_PROVIDER:
                $user = Social::driver(Manager::TWITTER_PROVIDER)->user();
                break;
            default:
                throw new Exception("Provider not found", 1);
                break;
        }

        $wp_user = Social::login($user);

        wp_redirect(get_home_url());
    }

}
