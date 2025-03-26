<?php

require __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$connection = new AMQPStreamConnection($_SERVER['RABBIT_HOST'], $_SERVER['RABBIT_PORT'], $_SERVER['RABBIT_USER'], $_SERVER['RABBIT_PASSWORD']);
$channel = $connection->channel();
var_dump($channel);


$capsule = new Capsule();
$capsule->addConnection([
    'host' => $_SERVER['MYSQL_HOST'],
    'database' => $_SERVER['MYSQL_DATABASE'],
    'username' => $_SERVER['MYSQL_USER'],
    'password' => $_SERVER['MYSQL_PASSWORD'],
]);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

var_dump(Capsule::select('SHOW DATABASES'));




//$mysqli = new mysqli("db", "site", "123123", "site");
//$sql = 'SELECT COUNT(*) AS `exists` FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMATA.SCHEMA_NAME="my_database_name"';
//$query = $mysqli->query($sql);
//$row = $query->fetch_object();
//var_dump($row);





