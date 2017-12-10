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
    }

    public function registerShortCoders()
    {
        new OauthShortCode;
        new LoginUrlShortCode;
    }
}
