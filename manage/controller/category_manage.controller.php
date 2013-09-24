<?php
class CategoryManageController
{
    static function run()
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
        case 'GET':
            self::index();
            break;
        case 'POST':
            if (isset($_POST['update']))
                self::update();
            break;
        }
    }

    static function index()
    {
        require_once APP_ROOT . '/model/category_manage.model.php';
        require_once APP_ROOT . '/model/type_manage.model.php';

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page_size = 20;

        $parent = null;
        if (isset($_GET['traceup']) &&
            ($tmp = $_GET['traceup']) &&
            preg_match('/^\d+$/', $tmp)
        ) {
            list($categories, $total_count) = CategoryManage::traceup($tmp);
        } else {
            if(isset($_GET['parent']) &&
                ($tmp = $_GET['parent']) &&
                preg_match('/^\d+$/', $tmp)
            ) $parent = $tmp;
            list($categories, $total_count) = CategoryManage::select($parent, $page, $page_size);
        }

        $types = TypeManage::get_types();
        $target_view = 'category_manage';
        require_once APP_ROOT . '/view/layout.view.php';
    }

    static function update()
    {
        if (!($id = $_POST['update']) || !isset($_POST['type_id'])) return;
        $type_id = trim($_POST['type_id']);

        require_once APP_ROOT . '/model/category_manage.model.php';
        if ($r = CategoryManage::update($id, $type_id));
        elseif ($r === 0)
            $error =  "$id 已经是 $type_id";
        else
            $error =  "$id 更新为 $type_id 失败";

        if (isset($error)) $result = array('error' => $error);
        else  $result = array('data' => $r);
        echo json_encode($result);
    }
}

CategoryManageController::run();

