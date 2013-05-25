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
        if (!isset($_POST['name']) || !($name = trim($_POST['name']))
            return;

        if ($id = $_POST['save'])
        {
            if(preg_match('/^\d+$/', $id))
            {
                if ($r = TypeManage::update($id, $name))
                    echo json_encode(TypeManage::select($id));
                elseif ($r === 0)
                    echo '已经存在: ' . $name;
                else
                    echo '更新失败: ' . $name;
            }
        }
        else
        {
            #header('Content-Type: text/json');
            if ($id = TypeManage::insert($name))
                echo json_encode(TypeManage::select($id));
            elseif ($id === 0)
                echo '已经存在: ' . $name;
            else 
                echo '保存失败: ' . $name;
        }
    }

    static function delete()
    {
        if (!($id = $_POST['save']) || !preg_match('/^\d+$/', $id))
            return;
        if ($r = TypeManage::delete($id))
            echo 'ok';
        elseif ($id === 0)
            echo 'ID不存在: ' . $id;
        else 
            echo '删除失败: ' . $id;
    }
}
TypeManageController::run();

