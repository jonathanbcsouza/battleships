<?php
require __DIR__ . '/vendor/autoload.php';

use App\Classes\Grid;

$gridSize = $_POST['gridSize'];
$numShips = $_POST['numShips'];

$grid = new Grid($gridSize);
$grid->placeShips($numShips);

echo json_encode($grid);
