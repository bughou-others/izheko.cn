<?php
class DB
{
    static $db;
    static function connect()
    {
        static $config;
        if (!$config) $config = parse_ini_file(APP_ROOT . '/common/database/db.ini');

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
        $result = self::$db->query($sql);
        $row = $result->fetch_assoc();
        $result->free();
        return $row;
    }

    static function get_rows($sql)
    {
        $result = self::$db->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        return $rows;
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
        $result->free();
        return array($data, $field_data);
    }

    static function get_value($sql)
    {
        $result = self::$db->query($sql);
        list($value) = $result->fetch_row();
        $result->free();
        return $value;
    }

    static function get_map($sql)
    {
        $map = array();
        $result = self::$db->query($sql);
        while(list($k, $v) = $result->fetch_row())
            $map[$k] = $v;
        $result->free();
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

    static function update_affected_rows($sql, $data = null)
    {
        if($data)
        {
            if(is_array($data))
            {
                $values = '';
                foreach($data as $k => $v)
                {
                    if(is_null($v)) $v = 'null';
                    elseif(is_string($v)) $v = '"' . self::$db->escape_string($v) . '"';
                    $values .= $values ? ",$k=$v" : "$k=$v";
                }
            }
            else $values = self::$db->escape_string($data);
            $sql = sprintf($sql, $values);
        }
        if(self::$db->query($sql))
            return self::$db->affected_rows;
        else return false;
    }
}

DB::connect();

