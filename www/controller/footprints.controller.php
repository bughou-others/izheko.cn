<?php
require_once APP_ROOT . '/model/footprints.model.php';

class FootprintsController
{
    static function index()
    {
        if(isset($_GET['item_ids']))
        {
            $item_ids = trim($_GET['item_ids']);
            if(preg_match('/^\d+(,\d+)*$/', $item_ids))
            {
                $data = Footprints::get($item_ids);
                echo json_encode($data);
            }
        }
    }
}

FootprintsController::index();

