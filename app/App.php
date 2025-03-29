<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class App
{
    public static function getPath(): string
    {
        return $_SERVER['REQUEST_URI'] == '/' ? 'home' : str_replace('/', '', $_SERVER['REQUEST_URI']);
    }


    public static function simulateSendEmail(): void
    {
        usleep(500000);
    }

    public static function simulateValidationUser(): void
    {
        usleep(500000);
    }

    public static function out($array)
    {
        echo json_encode($array);
    }

    public static function log($event = 'Event', array $array = array()): void
    {
        //STDOUT
        if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
        fwrite(STDOUT, "-- " . $_SERVER['HOSTNAME'] . ' -- ' . date('d-m-y H:i:s', time()) . ' -- ' . $event . ' -- ' . json_encode($array) . "\n ");
    }

    public static function initDb()
    {

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $_SERVER['MYSQL_HOST'],
            'database' => $_SERVER['MYSQL_DATABASE'],
            'username' => $_SERVER['MYSQL_USER'],
            'password' => $_SERVER['MYSQL_PASSWORD'],
        ]);
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public static function createTables(): void
    {

        $mysqli = new mysqli($_SERVER['MYSQL_HOST'], $_SERVER['MYSQL_USER'], $_SERVER['MYSQL_PASSWORD'], $_SERVER['MYSQL_DATABASE'], 3306);

        /** Init db if `mysql bitnami` doesn't handle with it **/
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
    }


}