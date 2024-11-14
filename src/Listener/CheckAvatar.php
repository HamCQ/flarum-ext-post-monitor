<?php

namespace Hamcq\NewPostMinitor\Listener;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\AvatarSaving;
use Illuminate\Support\Arr;

class CheckAvatar
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(AvatarSaving $event)
    {
        $user = $event->user;
        $actor = $event->actor;
        $image = $event->image;
        // app('log')->info($user -> id."用户修改头像");

        $this->monitorAtatar($user, $image);
    }

    function monitorAtatar($user, $image){
        // app('log')->info($user->avatar_url);
        //app('log')->info( $image ->encode("data-url"));

        if (!$this->settings->get('hamcq.monitor_switch_user_avatar')) {
            return;
        }
        if ($this->settings->get('hamcq.monitor_user_avatar_robot_webhook') == '') {
            return;
        }
        $url = $this->settings->get('hamcq.monitor_user_avatar_robot_webhook');
        $articles = [
            [
                "title" => "用户修改头像",
                "description" => date("Y-m-d H:i:s"),
                "url" => app('flarum.config')["url"]."/u/".$user->id,
                "picurl" => $user->avatar_url
            ]
        ];

        $data = [
            "msgtype" => "news",
            "news" => [
                "articles" => $articles
            ]
        ];
        resolve(Common::class)->send_post($url, $data);
    }
}