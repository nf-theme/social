<?php

namespace NightFury\Social;

use Illuminate\Support\ServiceProvider;
use NightFury\Social\Manager;
use NightFury\Social\ShortCodes\LoginUrlShortCode;
use NightFury\Social\ShortCodes\OauthShortCode;

class SocialServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\NightFury\Social\Manager::class, function ($app) {
            return new Manager($app);
        });
        $this->registerShortCoders();
        $this->registerFilters();
    }

    public function registerShortCoders()
    {
        new OauthShortCode;
        new LoginUrlShortCode;
    }

    public function registerFilters()
    {
        add_filter('get_avatar', [$this, 'social_avatar'], 10, 5);
    }

    public function social_avatar($avatar, $id_or_email, $size, $default, $alt)
    {
        $user = false;

        if (is_numeric($id_or_email)) {
            $id = (int) $id_or_email;
        } elseif (is_object($id_or_email)) {
            if (!empty($id_or_email->user_id)) {
                $id = (int) $id_or_email->user_id;
            }
        } else {
            $user = get_user_by('email', $id_or_email);
            $id   = $user->ID;
        }

        if (isset($id)) {
            $url = get_user_meta($id, Manager::NF_SOCIAL_IMAGE_META_KEY, true);
            if ($url != '') {
                $avatar = "<img alt='{$alt}' src='{$url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
            }
        }
        return $avatar;
    }
}
