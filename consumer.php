<?php

require __DIR__ . '/vendor/autoload.php';

use Records\Email;
use Queue\Consumer;


App::initDb();

Consumer::handleEmailQueue(function ($msg) {

    App::log('Consumer start', ['message' => $msg->body]);

    $email = Email::find((json_decode($msg->body))->email_id);

    App::simulateSendEmail($email);

    Email::seStatusSent($email);

    $msg->ack();

    App::log('Consumer end', ['email' => $email->toArray()]);
});
