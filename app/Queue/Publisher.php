<?php

namespace Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Records\Email;

class Publisher
{
    public static function sendEmail(Email $email)
    {
        $connection = new AMQPStreamConnection($_SERVER['RABBIT_HOST'], $_SERVER['RABBIT_PORT'], $_SERVER['RABBIT_USER'], $_SERVER['RABBIT_PASSWORD']);
        $channel = $connection->channel();
        $channel->exchange_declare('email', 'direct', false, true);
        $channel->queue_declare('send_email_user', false, true, false, false);
        $channel->queue_bind('send_email_user', 'email');

        $channel->basic_publish(new AMQPMessage(json_encode(
            ['email_id' => $email->id]
        )), 'email', '');

        $channel->close();
        $connection->close();


    }
}