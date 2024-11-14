<?php

namespace Hamcq\NewPostMinitor\Listener;
use Flarum\Settings\SettingsRepositoryInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface;
use Flarum\Http\RequestUtil;
use Illuminate\Validation\ValidationException;
use Flarum\Foundation\ErrorHandling\ExceptionHandler\IlluminateValidationExceptionHandler;
use Flarum\Foundation\ErrorHandling\JsonApiFormatter;

class CheckCoverMiddleware implements MiddlewareInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $path = $request->getUri()->getPath();
        if(stristr($path,"cover") && $request->getMethod() === 'POST'){
            try {
                // app("log")->info("用户修改封面");

                if (!$this->settings->get('hamcq.monitor_switch_user_cover')) {
                    return $handler->handle($request);
                }
                if ($this->settings->get('hamcq.monitor_user_cover_robot_webhook') == '') {
                    return $handler->handle($request);
                }

                $actor = RequestUtil::getActor($request);

                $this->monitorUserCover($actor,$request);

                return $handler->handle($request);

            }catch (ValidationException $exception) {
                  
                $handler = resolve(IlluminateValidationExceptionHandler::class);
               
                $error = $handler->handle($exception);
    
                return (new JsonApiFormatter())->format($error, $request);
            }
        } 

        return $handler->handle($request);
    }

    public function monitorUserCover($actor, $request){
        $url = $this->settings->get('hamcq.monitor_user_cover_robot_webhook');
        $avatar = $actor->avatar_url;
        $cover = app('flarum.config')["url"]."/assets/covers/".$actor->cover;
        $cardText = [
            "msgtype" =>"template_card",
            "template_card" =>[
                "card_type" =>"news_notice",
                "source" =>[
                    "icon_url" => $avatar,
                    "desc" => $actor->username
                ],
                "main_title" =>[
                    "title" => "用户修改背景页",
                    "desc" => date("Y-m-d H:i:s")
                ],
                "card_image" => [
                    "url" => $cover,
                ],
                "card_action" => [
                    "type" => 1,
                    "url" => app('flarum.config')["url"]."/u/".$actor->id
                ]
        
            ]
        ];

        resolve(Common::class)->send_post($url, $cardText);
    }
   
}