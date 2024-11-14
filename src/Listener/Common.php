<?php
namespace Hamcq\NewPostMinitor\Listener;

use Exception;

class Common
{
    public function send_post( $url , array $post_data ) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                )
            );
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            // app("log")->info($response);
            // app("log")->info($httpCode);

            return array($httpCode, $response);
        }
        catch (Exception $error) {
            app('log')->error($error->getMessage());
            return;
        }
        return ;
    }
}