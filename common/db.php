<?php
class DB
{
    static function connect()
    {
        $driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR;
        $config = self::config();
        $mysqli = new mysqli($config['host'], $config['user'], $config['password'], $config['database']);
        if ($mysqli->connect_errno) 
        {
            echo 'Failed to connect to MySQL: ' . $mysqli->connect_error;
            return;
        }
        $mysqli->query('set names utf8');
        return $mysqli;
    }

    static function config()
    {
        static $config;
        if (!$config) $config = parse_ini_file(dirname(__FILE__) . '/config/db.ini');
        return $config;
    }
}
