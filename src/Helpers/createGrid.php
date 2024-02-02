<?php

require '../../vendor/autoload.php';

use App\Classes\Grid;

$size = (int)$_POST['size'];
$num_ships = (int)$_POST['num_ships'];

$grid_object = new Grid($size, $num_ships);

$grid = $grid_object->getGrid();

header('Content-Type: application/json');
echo json_encode($grid);