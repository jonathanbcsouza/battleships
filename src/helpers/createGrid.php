<?php

require '../../vendor/autoload.php';

use App\Classes\Grid;

$size = (int)$_POST['size'];
$numShips = (int)$_POST['numShips'];

$gridObject = new Grid($size, $numShips);

$grid = $gridObject->getGrid();

header('Content-Type: application/json');
echo json_encode($grid);