<?php

namespace Hamcq\NewPostMinitor\Listener;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CheckBio
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;


    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(Saving $event)
    {
        $user = $event->user;
        $data = $event->data;
        // $actor = $event->actor;
        $attributes = Arr::get($data, 'attributes', []);
        if (isset($attributes['bio'])) {
            $bio = Str::of($attributes['bio'])->trim();
            // app("log")->info($bio);
            $this->monitorBio($user, $bio);
        }
    }

    function monitorBio($user, $bio){
        if (!$this->settings->get('hamcq.monitor_switch_user_bio')) {
            return;
        }
        if ($this->settings->get('hamcq.monitor_user_bio_robot_webhook') == '') {
            return;
        }
        if($bio!=""){
            $content = sprintf("用户修改签名 \n
                >用户： [%s](%s)
                >签名：%s
                >当前时间：%s", 
                $user->username, 
                app('flarum.config')["url"]."/u/".$user->id, 
                $bio,
                date('Y-m-d H:i:s'));
        }else{
            $content = sprintf("用户删除了签名 \n
                >用户： [%s](%s)
                >当前时间：%s", 
            $user->username, 
            app('flarum.config')["url"]."/u/".$user->id, 
            date('Y-m-d H:i:s'));
        }
       
        $url = $this->settings->get('hamcq.monitor_user_bio_robot_webhook');

        if ($content == "" || $url == "") {
            return;
        }

        $data = [
            "msgtype" => "markdown",
            "markdown" => [
                "content" => $content
            ]
        ];

        resolve(Common::class)->send_post($url, $data);
    }
}