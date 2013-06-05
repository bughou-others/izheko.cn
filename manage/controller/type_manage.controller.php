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
        $target_view = 'type_manage';
        require_once APP_ROOT . '/view/layout.view.php';
    }

    static function save()
    {
        if (!isset($_POST['name']) ||
            !($name = trim($_POST['name'])) ||
            !($pinyin = trim($_POST['pinyin']))
        ) return;

        if ($id = $_POST['save'])
        {
            if ($r = TypeManage::update($id, $name, $pinyin))
                $data = TypeManage::select($id);
            elseif ($r === 0)
                $error = '已经是: ' . $name;
            else
                $error = '更新失败: ' . $name;
        }
        else
        {
            if ($id = TypeManage::insert($name, $pinyin))
                $data = TypeManage::select($id);
            elseif ($id === 0)
                $error = '已经存在: ' . $name;
            else 
                $error = '保存失败: ' . $name;
        }
        if (isset($error)) $result = array('error' => $error);
        else  $result = array('data' => $data);
        echo json_encode($result);
    }

    static function delete()
    {
        if (!$id = $_POST['delete']) return;
        if ($r = TypeManage::delete($id))
            echo 'ok';
        elseif ($r === 0)
            echo 'ID不存在: ' . $id;
        else 
            echo '删除失败: ' . $id;
    }
}

TypeManageController::run();

