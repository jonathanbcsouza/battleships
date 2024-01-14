<?php

require_once __DIR__ . '/../configs/constants.php';

class Grid
{
    private $size;
    private $grid;
    private $numShips;
    private $emptyIcon;
    private $shipIcon;

    public function __construct($size, $numShips)
    {

        $this->size = $size;
        $this->numShips = $numShips;
        $this->emptyIcon = EMPTY_ICON;
        $this->shipIcon = SHIP_ICON;
        $this->grid = $this->buildGrid();
        $this->placeShips();
    }

    private function buildGrid()
    {
        $grid = array_fill(0, $this->size, array_fill(0, $this->size, $this->emptyIcon));
        return $grid;
    }

    private function placeShips()
    {
        $shipsPlaced = 0;
        while ($shipsPlaced < $this->numShips) {
            $randomRow = rand(0, $this->size - 1);
            $randomCol = rand(0, $this->size - 1);

            if ($this->grid[$randomRow][$randomCol] == $this->emptyIcon) {
                $this->grid[$randomRow][$randomCol] = $this->shipIcon;
                $shipsPlaced++;
            }
        }
    }

    public function getGrid()
    {
        return $this->grid;
    }
}
