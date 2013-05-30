<?php
class DB
{
    static $db;
    static function connect()
    {
        static $config;
        if (!$config) $config = parse_ini_file(dirname(__FILE__) . '/config/db.ini');

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $mysqli = new mysqli($config['host'], $config['user'], $config['password'], $config['database']);
        if ($mysqli->connect_errno) 
        {
            error_log('Failed to connect MySQL: ' . $mysqli->connect_error);
            return;
        }
        $mysqli->query('set names utf8');
        self::$db = $mysqli;
    }

    static function escape($str)
    {
        return self::$db->escape_string($str);
    }

    static function query($sql, $where = null, $suffix = null)
    {
        return self::$db->query($sql);
    }

    static function get_row($sql)
    {
        return self::$db->query($sql)->fetch_assoc();
    }

    static function get_rows($sql)
    {
        return self::$db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    static function get_rows_and_field($sql, $field)
    {
        $data = array();
        $field_data = array();
        $result = self::$db->query($sql);
        while($row = $result->fetch_assoc())
        {
            $data[] = $row;
            $field_data[] = $row[$field];
        }
        return array($data, $field_data);
    }

    static function get_value($sql)
    {
        list($value) = self::$db->query($sql)->fetch_row();
        return $value;
    }

    static function get_map($sql)
    {
        $map = array();
        $result = self::$db->query($sql);
        while(list($k, $v) = $result->fetch_row())
            $map[$k] = $v;
        return $map;
    }

    static function insert_id($sql)
    {
        if(self::$db->query($sql))
            return self::$db->insert_id;
        else return false;
    }

    static function affected_rows($sql)
    {
        if(self::$db->query($sql))
            return self::$db->affected_rows;
        else return false;
    }

    static function update($sql)
    {
    }
}

DB::connect();

