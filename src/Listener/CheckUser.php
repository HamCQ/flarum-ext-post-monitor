<?php

namespace Hamcq\NewPostMinitor\Listener;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Flarum\User\Event\Registered;

use Exception;

class CheckUser
{

     /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(Registered $event)
    { 
        $user = $event->user;
        // app('log')->info($user -> joined_at);

        if($user->discussion_count!=0 || $user->comment_count!=0){
            return;
        }

        if($user->is_email_confirmed){
            return;
        }

        if(strtotime($user->joined_at) - time() > 10){
            return;
        }

        $this->monitorNewUser($user);

    }

    function monitorNewUser($user){
        $content = "";
        $url = "";
        // app("log")->info($post->type); //实际发布discussion 也会携带comment

        if (!$this->settings->get('hamcq.monitor_switch_new_user')) {
            return;
        }
        if ($this->settings->get('hamcq.monitor_new_user_robot_webhook') == '') {
            return;
        }
        $content = sprintf("新用户注册 \n
                >用户： [%s](%s)
                >当前时间：%s", 
            $user->username, 
            app('flarum.config')["url"]."/u/".$user->id, 
            date('Y-m-d H:i:s'));
        $url = $this->settings->get('hamcq.monitor_new_user_robot_webhook');
       

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