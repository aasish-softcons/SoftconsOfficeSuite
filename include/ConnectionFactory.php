<?php

include_once "Configuration.php";

class ConnectionFactory
{
    private static $factory;


    public static function getFactory() {
        if(!self::$factory)
            self::$factory = new ConnectionFactory();
        return self::$factory;
    }

    public function getConnection() {
        $config = Configuration::$config;
        $dbname = $config['db_name'];
        $host = $config['db_host'];
        $user = $config['db_user'];
        $pass = $config['db_pass'];
        try {
            $connection = new PDO("mysql:host=$host;dbname=$dbname",$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=''"));
            $connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }catch(PDOException $pde) {
            throw $pde;
        }
        return $connection;
    }

    public function close($connection) {
        if(!$connection)
            $connection = null;
    }
}
