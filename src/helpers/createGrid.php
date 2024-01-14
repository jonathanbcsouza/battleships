<?php

require_once __DIR__ . '/../classes/Grid.php';

$size = $_POST['size'];
$numShips = $_POST['numShips'];

$gridObject = new Grid($size, $numShips);
$grid = $gridObject->getGrid();

header('Content-Type: application/json');
echo json_encode($grid);
