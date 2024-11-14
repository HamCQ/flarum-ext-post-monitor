<?php

namespace Hamcq\NewPostMinitor\Listener;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Post\Event\Saving;
use Illuminate\Support\Arr;

class CheckPost
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
        $post = $event->post;
        $data = $event->data;

        //防止点赞重新触发审核
        if ($post->exists && isset($data['attributes']['isLiked'])){
            return;
        }
        if ($post->exists && Arr::has($data, 'attributes.reaction')) {
            return;
        }

        $this->monitorNewContent($post);

    }
    
    public function monitorNewContent($post)
    {
        $content = "";
        $url = "";
        $limit = 0;
        // app("log")->info($post->type); //实际发布discussion 也会携带comment

        if (!$this->settings->get('hamcq.monitor_switch_new_post')) {
            return;
        }
        if ($this->settings->get('hamcq.monitor_new_post_robot_webhook') == '') {
            return;
        }
        $limit = $this->settings->get('hamcq.monitor_switch_new_post_summary_length');
        if($limit<=0){
            $limit = 1024;
        }
       
        $content = sprintf("<font color=\"warning\">有用户发布新内容！</font>辛苦管理员留意 (#^.^#) \n
                >相关用户： [%s](%s)
                >讨论标题： <font color=\"comment\">%s</font>
                >内容摘要： <font color=\"comment\">%s</font>
                >链接： [点此查看](%s)", 
            $post->user->username, app('flarum.config')["url"]."/u/".$post->user->id,
            $post->discussion->title, 
            mb_substr($post->content, 0, $limit),
            app('flarum.config')["url"]."/d/".$post->discussion_id."/".$post->number);
        $url = $this->settings->get('hamcq.monitor_new_post_robot_webhook');
       

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
    