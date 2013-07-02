<?php
require_once APP_ROOT . '/model/my_history.model.php';

class MyHistoryController
{
    static function index()
    {
        if(isset($_GET['item_ids']))
        {
            $item_ids = trim($_GET['item_ids']);
            if(preg_match('/^\d+(,\d+)*$/', $item_ids))
            {
                $data = MyHistory::get($item_ids);
                echo json_encode($data);
            }
        }
    }
}

MyHistoryController::index();

