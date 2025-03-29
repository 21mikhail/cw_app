<?php

namespace Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;


class Consumer
{


    public static function handleEmailQueue($callback)
    {
        $connection = new AMQPStreamConnection($_SERVER['RABBIT_HOST'], $_SERVER['RABBIT_PORT'], $_SERVER['RABBIT_USER'], $_SERVER['RABBIT_PASSWORD']);
        $channel = $connection->channel();

        $channel->queue_declare('send_email_user', false, true, false, false);

        $channel->basic_consume('send_email_user', '', false, false, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }


    }


}