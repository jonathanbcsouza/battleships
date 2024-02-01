<?php

namespace App\Classes;

require_once __DIR__ . '/../Configs/constants.php';

class Grid
{
    private int $size;
    private array $grid;
    private int $numShips;
    private string $emptyIcon;
    private string $shipIcon;

    public function __construct(int $size, int $numShips)
    {
        $this->size = $size;
        $this->numShips = $numShips;
        $this->emptyIcon = EMPTY_ICON;
        $this->shipIcon = SHIP_ICON;
        $this->grid = $this->buildGrid();
        $this->placeShips();
    }

    private function buildGrid(): array
    {
        $grid = array_fill(0, $this->size, array_fill(0, $this->size, $this->emptyIcon));
        return $grid;
    }

    private function placeShips(): void
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

    public function getGrid(): array
    {
        return $this->grid;
    }
}
