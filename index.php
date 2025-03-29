<?php

require __DIR__ . '/vendor/autoload.php';

use Records\User;
use Records\Email;

use Queue\Publisher;


App::initDb();

if (App::getPath() === 'home') { // create user and add Queue `sendEmail`

    App::simulateValidationUser();

    $user = User::create();

    $email = Email::create($user);

    Publisher::sendEmail($email);

    App::log('Add user', ['user' => $user->toArray(), 'email' => $email->toArray()]);

    App::out(['status' => 'ok', 'user' => $user->id, 'email' => $email->id]);

} elseif (App::getPath() === 'users') { // for debug print Users

    var_dump(User::all()->toArray());

} elseif (App::getPath() === 'emails') { // for debug print Emails

    var_dump(Email::all()->toArray());

} elseif (App::getPath() === 'server') { // for debug print Env

    dd($_SERVER);

} elseif (App::getPath() === 'init_db') { //  Init db if `mysql bitnami` doesn't handle with it(but bitnami)

    App::createTables();
}

