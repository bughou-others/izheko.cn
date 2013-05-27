<?php
require_once APP_ROOT . '/model/category_manage.model.php';
require_once APP_ROOT . '/model/type_manage.model.php';

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
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page_size = 2;
        list($categories, $total_count) = CategoryManage::select($page, $page_size);
        $types = TypeManage::get_types();
        $target_view = 'category_manage';
        require_once APP_ROOT . '/view/layout.view.php';
    }

    static function update()
    {
        if (!($id = $_POST['update']) ||
            !isset($_POST['type_id']) ||
            !($type_id = trim($_POST['type_id']))
        ) return;

        if ($r = CategoryManage::update($id, $type_id))
            echo 'ok'; 
        else
            echo '更新失败';
    }
}

CategoryManageController::run();

