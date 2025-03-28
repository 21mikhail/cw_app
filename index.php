<?php

require __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$mysqli = new mysqli($_SERVER['MYSQL_HOST'], $_SERVER['MYSQL_USER'], $_SERVER['MYSQL_PASSWORD'], $_SERVER['MYSQL_DATABASE'], 3306);

$connection = new AMQPStreamConnection($_SERVER['RABBIT_HOST'], $_SERVER['RABBIT_PORT'], $_SERVER['RABBIT_USER'], $_SERVER['RABBIT_PASSWORD']);
$channel = $connection->channel();
$channel->close();
$connection->close();

if ($_SERVER['REQUEST_URI'] === '/') {  /**  Add `user` and `queue` **/

    echoOut('Start add user');

    $names = json_decode(file_get_contents('names.json'), true);
    $name = $names[mt_rand(0, 999)];

    $domain = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
    $email = str_replace(array(' ', "'"), '', strtolower($name)) . '@' . $domain[mt_rand(0, 3)];

    $sql = "INSERT INTO users (`name`,`email`) VALUES('{$name}', '{$email}')";
    $mysqli->query($sql);
    $userId = $mysqli->insert_id;

    $sql = "SELECT email, name FROM users WHERE id = {$userId}";
    $user = $mysqli->query($sql)->fetch_assoc();

    $sql = "INSERT INTO emails (`email`, `text`, `status`) VALUES('{$user['email']}',". 'Hello, '. $user['name']. ", 'pending')";
    $mysqli->query($sql);
    $emailId = $mysqli->insert_id;

    echoOut('Insert email', ['email' => $email, 'userId' => $userId, 'emailId' => $emailId, 'status' => 'pending']);

} elseif ($_SERVER['REQUEST_URI'] === '/queue') {  /** Handle `queue` and send email **/
    echoOut('Start queue');

} elseif ($_SERVER['REQUEST_URI'] === '/users') { /** List of users **/

    var_dump($mysqli->query("SELECT * FROM users")->fetch_all());

} elseif ($_SERVER['REQUEST_URI'] === '/emails') {  /** List of email **/

    var_dump($mysqli->query("SELECT * FROM emails")->fetch_all());

} elseif($_SERVER['REQUEST_URI'] === '/info'){
    phpinfo();
} elseif($_SERVER['REQUEST_URI'] === '/server') {
    dd($_SERVER);
} elseif($_SERVER['REQUEST_URI'] === '/init_db'){ /** Init db if `mysql bitnami` doesn't handle with it **/
    $sql = "
    CREATE TABLE IF NOT EXISTS {$_SERVER['MYSQL_DATABASE']}.users (
     id INT UNSIGNED auto_increment PRIMARY KEY NOT NULL,
     name varchar(128) NOT NULL,
     email varchar(128) NOT NULL,
     INDEX (email)
     )
     ENGINE=InnoDB
     DEFAULT CHARSET=utf8mb4
     COLLATE=utf8mb4_general_ci;
    ";

    $mysqli->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS {{ .Values.mysql.auth.database }}.emails (
    id INT UNSIGNED auto_increment PRIMARY KEY NOT NULL,
    email varchar(128) NOT NULL,
    text varchar(256) NOT NULL,
    status ENUM('pending', 'sent', 'fail') NOT NULL,
    INDEX (email)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8mb4
    COLLATE=utf8mb4_general_ci;";

    $mysqli->query($sql);
} else {
    http_response_code(404);
    echo 'Page not found';
}


function echoOut($event = 'Event', array $array = array())
{
    echo "-- " . $_SERVER['HOSTNAME'] . ' -- ' . date('d-m-y H:i:s', time()) . ' -- ' . $event . ' -- ' . json_encode($array) . "\n <br/>";
}

