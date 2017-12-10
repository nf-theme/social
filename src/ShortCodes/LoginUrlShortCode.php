<?php

namespace NightFury\Social\ShortCodes;

use Illuminate\Support\Collection;
use NF\Abstracts\ShortCode;
use NightFury\Social\Facades\Social;
use NightFury\Social\Manager;

class LoginUrlShortCode extends ShortCode
{
    public $name = 'nf_social_url';

    public function render($attrs)
    {
        $providers = new Collection(explode(',', $attrs['providers']));

        $providers = $providers->map(function ($item) {
            switch ($item) {
                case Manager::FACEBOOK_PROVIDER:
                    return [
                        'name' => Manager::FACEBOOK_PROVIDER,
                        'url'  => Social::driver($item)->stateless()->redirect()->getTargetUrl(),
                        'html' => '<i class="fa fa-facebook"></i> <span>' . $item . '</span>',
                    ];
                case Manager::TWITTER_PROVIDER:
                    return [
                        'name' => Manager::TWITTER_PROVIDER,
                        'url'  => Social::driver($item)->redirect()->getTargetUrl(),
                        'html' => '<i class="fa fa-twitter"></i> <span>' . $item . '</span>',
                    ];
                case Manager::GOOGLE_PROVIDER:
                    return [
                        'name' => Manager::GOOGLE_PROVIDER,
                        'url'  => Social::driver($item)->stateless()->redirect()->getTargetUrl(),
                        'html' => '<i class="fa fa-google-plus"></i> <span>' . $item . '</span>',
                    ];
                default:
                    break;
            }
        });

        $output = '<ul class="nf-social-login-urls">';
        foreach ($providers as $item) {
            $output .= "<li><a href=\"{$item['url']}\">{$item['html']}</a></li>";
        }
        $output .= '</ul>';

        echo $output;
    }
}
