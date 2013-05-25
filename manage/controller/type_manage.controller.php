<?php
require_once APP_ROOT . '/model/type_manage.model.php';

class TypeManageController
{
    static function run()
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
        case 'GET':
            self::index();
            break;
        case 'POST':
            if (isset($_POST['save']))
                self::save();
            elseif (isset($_POST['delete']))
                self::delete();
            break;
        }
    }

    static function index()
    {
        $types = TypeManage::select();
        require_once APP_ROOT . '/view/type_manage.view.php';
    }

    static function save()
    {
        if ($id = $_POST['save'])
        {
            if(preg_match('/^\d+$/', $id) && isset($_POST['name']) &&
                ($name = trim($_POST['name']))
            )
            {
                TypeManage::update($id, $name);
                TypeManage::select($id);
            }
        }
        else
        {
        }
    }

    static function delete()
    {
        if ($id = $_POST['save'])
        {
        }
    }
}
TypeManageController::run();

